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

require_once("utils.php");

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Represents a non user-bound connection to the figo Connect API
 */
class Connection {
    /**
     * @var LoggerInterface
     */
    protected $logger;
    private $client_id;
    private $client_secret;
    private $redirect_uri;
    /**
     * @var null API endpoint
     */
    private $apiEndpoint;
    /**
     * @var array Fingerprints for API endpoint
     */
    private $fingerprints;

    /**
     * Constructor
     *
     * @param string the client ID
     * @param string the client secret
     * @param string redirect URI
     * @param string $apiEndpoint Custom API endpoint
     * @param array $fingerprints Fingerprints for custom API endpoint
     */
    public function __construct($client_id, $client_secret, $redirect_uri = null, $apiEndpoint = null, array $fingerprints = null) {
        // set default values
        $this->logger = new NullLogger();
        $this->apiEndpoint = Config::$API_ENDPOINT;
        $this->fingerprints = Config::$VALID_FINGERPRINTS;

        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;

        if ($apiEndpoint) {
            $this->apiEndpoint = $apiEndpoint;
        }
        $this->apiUrl = parse_api_endpoint($this->apiEndpoint);

        if ($fingerprints) {
            $this->fingerprints = $fingerprints;
        }
    }

    /**
     * Set Logger
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Helper method for making a OAuth 2.0 request
     *
     * @param string the URL path on the server
     * @param array this optional associative array will be used as url-encoded POST content.
     * @param string $method GET or POST Requests
     * @param string $encode http_build_query or json_encode
     *
     * @return array JSON response
     */
    public function query_api($path, array $data = null, $method='POST', $encode='http_build_query', $language = 'de') {
        if ($encode != 'http_build_query') {
            $data = is_null($data) ? "" : json_encode($data);
            $content_type = "application/json";
        } else {
            $data = is_null($data) ? "" : http_build_query($data);
            $content_type = "application/x-www-form-urlencoded";
        }

        $headers = array("Authorization"  => "Basic ".base64_encode($this->client_id.":".$this->client_secret),
                         "Content-Length" => strlen($data));
        if (strlen($data) > 0) {
            $headers["Content-Type"] = $content_type;
        }

        $request = new HttpsRequest($this->apiUrl['host'], $this->fingerprints, $this->logger);
        $path = $this->apiUrl['path'] . $path;
        return $request->request($path, $data, $method, $headers, $language);
    }

    /**
     * Get the URL a user should open in the web browser to start the login process
     *
     * When the process is completed, the user is redirected to the URL provided to
     * the constructor and passes on an authentication code. This code can be converted
     * into an access token for data access.
     *
     * @param string this string will be passed on through the complete login
     *        process and to the redirect target at the end. It should be used to
     *        validated the authenticity of the call to the redirect URL
     * @param string scope of data access to ask the user for, e.g. <code>accounts=ro</code>
     * @return string the URL to be opened by the user
     */
    public function login_url($state, $scope = null) {
        $data = array("response_type" => "code",
                      "client_id"     => $this->client_id,
                      "state"         => $state);
        if (!is_null($this->redirect_uri)) {
            $data["redirect_uri"] = $this->redirect_uri;
        }
        if (!is_null($scope)) {
            $data["scope"] = $scope;
        }
        return $this->apiEndpoint."/auth/code?".http_build_query($data);
    }



    /**
     * Retrieve list of supported banks, credit cards, other payment services
     *
     * @param String $service      filter the type of service to request (optional): `banks`, `services` or everything (default)
     * @param String $country_code the country code the service comes from
     *
     * @return array
     */
    public function get_supported_payment_services($service=null, $countryCode = null, $language = 'de') {
        switch ($service) {
            case "banks":

                $url = '/catalog/banks';

                if($countryCode) {
                    $url = $url . '/' . $countryCode;
                }

                $response = $this->query_api($url, null, "GET", "http_build_query", $language);
                break;
            case "services":
                $response = $this->query_api("/catalog/services", null, "GET", "http_build_query", $language);
                break;
            default:
                $response = $this->query_api("/catalog", null, "GET", "http_build_query", $language);
        }
        return $response;
    }

    /**
     * Exchange authorization code or refresh token for access token.
     *
     * @param string either the authorization code received as part of the call to the
     *        redirect URL at the end of the logon process, or a refresh token
     * @param string scope of data access to ask the user for, e.g. <code>accounts=ro</code>
     * @return array associative array with the keys <code>access_token</code>, <code>refresh_token</code>
     *         and <code>expires</code>, as documented in the figo Connect API specification
     */
    public function obtain_access_token($authorization_code_or_refresh_token, $scope = null) {
        // Authorization codes always start with "O" and refresh tokens always start with "R".
        if ($authorization_code_or_refresh_token[0] === "O") {
            $data = array("grant_type" => "authorization_code", "code" => $authorization_code_or_refresh_token);
            if (!is_null($this->redirect_uri)) {
                $data["redirect_uri"] = $this->redirect_uri;
            }
        } elseif ($authorization_code_or_refresh_token[0] === "R") {
            $data = array("grant_type" => "refresh_token", "refresh_token" => $authorization_code_or_refresh_token);
            if (!is_null($scope)) {
                $data["scope"] = $scope;
            }
        } else {
            throw new Exception("invalid_token", "invalid code/token");
        }
        return $this->query_api("/auth/token", $data, "POST", "json_encode");
    }

    /**
     * Native client login with figo Account credentials.
     *
     * @param string the figo Account email address
     * @param string the figo Account password
     * @param string scope of data access to ask the user for, e.g. <code>accounts=ro</code>
     * @return array associative array with the keys <code>access_token</code>, <code>refresh_token</code>
     *         and <code>expires</code>, as documented in the figo Connect API specification
     */
    public function native_client_login($username, $password, $scope = null) {
        $data = array("grant_type" => "password", "username" => $username, "password" => $password, "scope" => $scope);
        return $this->query_api("/auth/token", $data, "POST", "json_encode");
    }


    /**
     * Revoke refresh token or access token.
     *
     * This action has immediate effect, i.e. you will not be able use that token anymore after this call.
     *
     * @param string access or refresh token to be revoked
     */
    public function revoke_token($refresh_token_or_access_token) {
        $data = array("token" => $refresh_token_or_access_token);
        $this->query_api("/auth/revoke", $data, "POST", "json_encode");
    }

    /**
     * Create a Process
     * Example Usage:
     *   $process = new \figo\Process();
         $process->email = 'my_email@example.com';
         $process->password = 'my_password';
         $process->state = 'First_step';
         $process->steps =   array(
            array(
                'options' => json_decode('{}'),
                'type' => 'figo.steps.account.create',
            ),
            array(
                'options' => array(
                'account_number' => '100100100',
                'amount' =>  99,
                'bank_code' => "82051000",
                'currency' => "EUR",
                'name' => "Figo GmbH",
                'purpose' => "Yearly contribution",
                'type' => "Transfer",
            ),
            'type' => 'figo.steps.payment.submit',
            )
      );
        $return = $connection->create_process($process);
     *
     *
     * @param Process $process Figo Process
     *
     * @return array
     */
    public function create_process(Process $process) {
        return $this->query_api("/client/process", $process->dump(), "POST", "json_encode");
    }


    /**
     * Create a new figo Account
     *
     * @param string First and last name
     * @param string Email address; It must obey the figo username & password policy
     * @param string New figo Account password; It must obey the figo username & password policy
     * @param string Two-letter code of preferred language
     * @param boolean This flag indicates whether the user has agreed to be contacted by email
     * @return string Auto-generated recovery password
     */
    public function create_user($name, $email, $password, $language='de') {
        $data = array('name' => $name, 'email' => $email, 'password' => $password, 'language' => $language, 'affiliate_client_id' => $this->client_id);
        $response = $this->query_api("/auth/user", $data, "POST", "json_encode");
        return $response["recovery_password"];
    }

    /**
     * credential login
     *
     * @param $username
     * @param $password
     * @param null $device_name
     * @param null $device_type
     * @param null $device_udid
     * @param null $scope
     * @return array
     */
    public function credential_login($username, $password, $device_name = null, $device_type = null, $device_udid = null, $scope = null)
    {
        return $this->native_client_login($username, $password, $scope);
    }


}

?>
