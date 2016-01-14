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
 * Represents a non user-bound connection to the figo Connect API
 */
class Connection {

    private $client_id;
    private $client_secret;
    private $redirect_uri;

    /**
     * Constructor
     *
     * @param string the client ID
     * @param string the client secret
     * @param string redirect URI
     */
    public function __construct($client_id, $client_secret, $redirect_uri = null) {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
        $this->redirect_uri = $redirect_uri;
    }

    /**
     * Helper method for making a OAuth 2.0 request
     *
     * @param string the URL path on the server
     * @param array this optional associative array will be used as url-encoded POST content.
     * @return array JSON response
     */
    public function query_api($path, array $data = null, $method='POST') {
        $data = is_null($data) ? "" : http_build_query($data);

        $headers = array("Authorization"  => "Basic ".base64_encode($this->client_id.":".$this->client_secret),
                         "Content-Type"   => "application/x-www-form-urlencoded",
                         "Content-Length" => strlen($data));

        $request = new HttpsRequest();
        return $request->request($path, $data, $method, $headers);
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
        return "https://".Config::$API_ENDPOINT."/auth/code?".http_build_query($data);
    }



    /**
     * Retrieve list of supported banks, credit cards, other payment services
     *
     * @param String $service      filter the type of service to request (optional): `banks`, `services` or everything (default)
     * @param String $country_code the country code the service comes from
     *
     * @return array
     */
    public function get_supported_payment_services($service=null) {
        switch ($service) {
            case "banks":
                $response = $this->query_api("/catalog/banks", null, "GET");
                break;
            case "services":
                $response = $this->query_api("/catalog/services", null, "GET");
                break;
            default:
                $response = $this->query_api("/catalog", null, "GET");
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
        return $this->query_api("/auth/token", $data);
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
        $this->query_api("/auth/revoke?".http_build_query($data));
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
        $response = $this->query_api("/auth/user", $data);
        return $response["recovery_password"];
    }


}

?>