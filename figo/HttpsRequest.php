<?php
/*
 * Copyright (c) 2013 figo GmbH
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace figo;

use Psr\Log\LoggerInterface;

/**
 * HTTPS request class with certificate authentication and enhanced error handling
 */
class HttpsRequest {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var string
     */
    private $apiEndpoint;
    /**
     * @var array
     */
    private $fingerprints;

    public function __construct($apiEndpoint, array $fingerprints, LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->apiEndpoint = $apiEndpoint;
        $this->fingerprints = $fingerprints;
    }

    /**
     * Send client request and return server response.
     *
     * @param string $path the URL path on the server
     * @param string $data the HTTP body
     * @param string $method the HTTP method
     * @param array $headers additional HTTP headers
     * @return array JSON response
     * @throws Exception
     */
    public function request($path, $data, $method, array $headers) {
        // Open socket.
        $context = stream_context_create();
        stream_context_set_option($context, "ssl", "cafile", dirname(__FILE__).DIRECTORY_SEPARATOR."ca-bundle.crt");
        stream_context_set_option($context, "ssl", "verify_peer", true);
        stream_context_set_option($context, "ssl", "capture_peer_cert", true);

        $fp = stream_socket_client("tlsv1.2://". $this->apiEndpoint .":443", $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $context);
        if (!$fp) {
            throw new Exception("socket_error", $errstr);
        }
        stream_set_timeout($fp, 60);

        // Verify fingerprint of server SSL/TLS certificate.
        $options = stream_context_get_options($context);
        if (isset($options["ssl"]) && isset($options["ssl"]["peer_certificate"])) {
            $certificate = $options["ssl"]["peer_certificate"];
            openssl_x509_export($certificate, $certificate);
            $fingerprint = hash("sha256", base64_decode(preg_replace("/-.*/", "", $certificate)));
            $fingerprint = implode(":", str_split(strtoupper($fingerprint), 2));
            if (!in_array($fingerprint, $this->fingerprints)) {
                fclose($fp);
                throw new Exception("ssl_error", "SSL/TLS certificate fingerprint mismatch.");
            }
        }

        // Setup common HTTP headers.
        $headers["Host"] = Config::$API_ENDPOINT;
        $headers["Accept"] = "application/json";
        $headers["User-Agent"] = Config::$USER_AGENT . '/' . Config::$SDK_VERSION;
        $headers["Connection"] = "close";

        // Send client request.
        $header = "";
        foreach ($headers as $key => $value) {
            $header .= $key.": ".$value."\r\n";
        }
        fwrite($fp, $method." ".$path." HTTP/1.1\r\n".$header."\r\n".$data);

        // Read server response.
        $response = stream_get_contents($fp);
        fclose($fp);
        preg_match("/ (\d+)/", $response, $code);
        $code = intval($code[1]);
        $body = substr($response, strpos($response, "\r\n\r\n") + 4);
        
        $responseArray = json_decode($body, true);

        $loggingData = array(
            'path' => $path,
            'data' => $data,
            'method' => $method,
            'headers' => $headers,
            'response' => array(
                'status' => $code,
                'body' => $responseArray
            )
        );


        // Evaluate HTTP response.
        if ($code >= 200 && $code < 300) {
            if (strlen($body) === 0) {
                $this->logSuccessfulRequest($path, $responseArray, $loggingData);
                return array();
            }

            if (is_null($responseArray)) {
                $this->logFailedRequest($path, $responseArray, $loggingData);
                throw new Exception("json_error", "Cannot decode JSON object.");
            }

            $this->logSuccessfulRequest($path, $responseArray, $loggingData);
            return $responseArray;
        }

        if (is_null($responseArray)) {
            $this->logFailedRequest($path, $responseArray, $loggingData);
            throw new Exception("json_error", "Cannot decode JSON object.");
        }

        if ($code === 404) {
            $this->logFailedRequest($path, $responseArray, $loggingData);
            return null;
        }

        if ($code >= 400 && $code < 500) {
            $this->logFailedRequest($path, $responseArray, $loggingData);
            throw new Exception($responseArray["error"]["name"] .":". $responseArray["error"]["message"]." (Error-Code: ".$responseArray["error"]["code"].")" , $responseArray["error"]["description"]);
        }

        if ($code === 503) {
            $this->logFailedRequest($path, $responseArray, $loggingData);
            throw new Exception("service_unavailable", "Exceeded rate limit.");
        }

        throw new Exception("internal_server_error", "We are very sorry, but something went wrong.");

    }

    protected function logSuccessfulRequest($path, $response, $data)
    {
        if (strpos($path, '/task/progress') !== false) {
            // when on /task/progress
            $data = array_merge($data, array('task_id' => explode('/task/progress?id=', $path)[1]));
            if($response['is_erroneous']) {
                $this->logger->info('API request to /task/progress', $data);
            } else {
                $this->logger->debug('API request to /task/progress', $data);
            }

        } else {
            // when not on /task/progress
            $this->logger->debug('API request', $data);
        }
    }

    protected function logFailedRequest($path, $response, $data)
    {
        if (strpos($path, '/task/progress') !== false) {
            // when on /task/progress
            $this->logger->info('API request to /task/progress failed', $data);
        } else {
            // when not on /task/progress
            $this->logger->info('API request failed', $data);
        }
    }
}

?>
