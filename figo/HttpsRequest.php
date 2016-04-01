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

/**
 * HTTPS request class with certificate authentication and enhanced error handling
 */
class HttpsRequest {

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

        $fp = stream_socket_client("tls://".Config::$API_ENDPOINT.":443/", $errno, $errstr, 60, STREAM_CLIENT_CONNECT, $context);
        if (!$fp) {
            throw new Exception("socket_error", $errstr);
        }
        stream_set_timeout($fp, 60);

        // Verify fingerprint of server SSL/TLS certificate.
        $options = stream_context_get_options($context);
        if (isset($options["ssl"]) && isset($options["ssl"]["peer_certificate"])) {
            $certificate = $options["ssl"]["peer_certificate"];
            openssl_x509_export($certificate, $certificate);
            $fingerprint = sha1(base64_decode(preg_replace("/-.*/", "", $certificate)));
            $fingerprint = implode(":", str_split(strtoupper($fingerprint), 2));
            if (!in_array($fingerprint, Config::$VALID_FINGERPRINTS)) {
                fclose($fp);
                throw new Exception("ssl_error", "SSL/TLS certificate fingerprint mismatch.");
            }
        }

        // Setup common HTTP headers.
        $headers["Host"] = Config::$API_ENDPOINT;
        $headers["Accept"] = "application/json";
        $headers["User-Agent"] = "php-figo";
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

        // Evaluate HTTP response.
        if ($code >= 200 && $code < 300) {
            if (strlen($body) === 0) {
                return array();
            } else {
                $obj = json_decode($body, true);
                if (is_null($obj)) {
                    throw new Exception("json_error", "Cannot decode JSON object.");
                } else {
                    return $obj;
                }
             }
        } elseif ($code === 400) {
             $err = json_decode($body, true);
             if (is_null($err)) {
                 throw new Exception("json_error", "Cannot decode JSON object.");
             } else {
                 throw new Exception($err["error"]["name"] .":". $err["error"]["message"]." (Error-Code: ".$err["error"]["code"].")" , $err["error"]["description"]);
             }
        } elseif ($code === 401) {
            throw new Exception("unauthorized", "Missing, invalid or expired access token.");
        } elseif ($code === 403) {
            throw new Exception("forbidden", "Insufficient permission.");
        } elseif ($code === 404) {
            return null;
        } elseif ($code === 405) {
            throw new Exception("method_not_allowed", "Unexpected request method.");
        } elseif ($code === 503) {
            throw new Exception("service_unavailable", "Exceeded rate limit.");
        } else {
            throw new Exception("internal_server_error", "We are very sorry, but something went wrong.");
        }
    }
}

?>
