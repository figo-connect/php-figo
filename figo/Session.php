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
 * Represents a user-bound connection to the figo Connect API and allows access to the user's data
 */
class Session {

    private $access_token;

    /**
     * Constructor
     *
     * @param string the access token
     */
    public function __construct($access_token) {
        $this->access_token = $access_token;
    }

    /**
     * Helper method for making a REST request
     *
     * @param string the URL path on the server
     * @param array this optional associative array will be used as JSON-encoded POST content.
     * @param string the HTTP method
     * @return array JSON response
     */
    public function query_api($path, array $data = null, $method = "GET") {
        $data = is_null($data) ? "" : json_encode($data);

        $headers = array("Authorization"  => "Bearer ".$this->access_token,
                         "Content-Type"   => "application/json",
                         "Content-Length" => strlen($data));

        $request = new HttpsRequest();
        return $request->request($path, $data, $method, $headers);
    }

    /**
     * Request list of accounts
     *
     * @return array an array of <code>Account</code> objects, one for each account the user has 
     *         granted the app access
     */
    public function get_accounts() {
        $response = $this->query_api("/rest/accounts");
        $accounts = array();
        foreach ($response["accounts"] as $account) {
            array_push($accounts, new Account($this, $account));
        }
        return $accounts;
    }

    /**
     * Request specific account
     *
     * @param string account_id ID of the account to be retrieved
     * @return Account account object
     */
    public function get_account($account_id) {
        $response = $this->query_api("/rest/accounts/".$account_id);
        return (is_null($response) ? null : new Account($this, $response));
    }

    /**
     * Request list of transactions
     *
     * @param mixed this parameter can either be a transaction ID or a date
     * @param string do only return transactions which were booked after the start transaction ID
     * @param integer limit the number of returned transactions
     * @param boolean this flag indicates whether pending transactions should be included in the 
     *        response; pending transactions are always included as a complete set, regardless of
     *        the `since` parameter
     * @return array an array of <code>Transaction</code> objects, one for each transaction of the user
     */
    public function get_transactions($since = null, $start_id = null, $count = 1000, $include_pending = false) {
        $data = array();
        if (!is_null($since)) {
            $data["since"] = is_a($since, "\DateTime") ? $since->format("Y-m-d") : $since;
        }
        if (!is_null($start_id)) {
            $data["start_id"] = $start_id;
        }
        $data["count"] = $count;
        $data["include_pending"] = $include_pending ? "1" : "0";
        $response = $this->query_api("/rest/transactions?".http_build_query($data));
        $transactions = array();
        foreach ($response["transactions"] as $transaction) {
            array_push($transactions, new Transaction($this, $transaction));
        }
        return $transactions;
    }

    /**
     * Request the URL a user should open in the web browser to start the synchronization process
     *
     * @param string the user will be redirected to this URL after the process completes
     * @param string this string will be passed on through the complete synchronization process
     *        and to the redirect target at the end. It should be used to validated the authenticity of
     *        the call to the redirect URL
     * @param boolean this flag indicates whether notifications should be sent
     * @param integer if this parameter is set, only those accounts will be synchronized, which have 
     *        not been synchronized within the specified number of minutes
     * @return string the URL to be opened by the user
     */
    public function get_sync_url($redirect_uri, $state, $disable_notifications = false, $if_not_synced_since = 0) {
        $data = array("redirect_uri"          => $redirect_uri,
                      "state"                 => $state,
                      "disable_notifications" => $disable_notifications,
                      "if_not_synced_since"   => $if_not_synced_since);
        $response = $this->query_api("/rest/sync", $data, "POST");
        return "https://".Config::$API_ENDPOINT."/task/start?id=".$response["task_token"];
    }

    /**
     * Request list of registered notifications
     *
     * @return array an array of <code>Notification</code> objects, one for each registered notification
     */
    public function get_notifications() {
        $response = $this->query_api("/rest/notifications");
        $notifications = array();
        foreach ($response["notifications"] as $notification) {
            array_push($notifications, new Account($this, $notification));
        }
        return $notifications;
    }

    /**
     * Request specific notification
     *
     * @param string account_id ID of the notification to be retrieved
     * @return Notification notification object
     */
    public function get_notification($notification_id) {
        $response = $this->query_api("/rest/notifications/".$notification_id);
        return (is_null($response) ? null : new Notification($this, $response));
    }

    /**
     * Register notification.
     *
     * @param string one of the notification keys specified in the figo Connect API specification
     * @param string notification messages will be sent to this URL
     * @param string any kind of string that will be forwarded in the notification message
     * @return Notification newly created <code>Notification</code> object
     */
    public function add_notification($observe_key, $notify_uri, $state) {
      $data = array("observe_key" => $observe_key,
                    "notify_uri"  => $notify_uri,
                    "state"       => $state);
      $response = $this->query_api("/rest/notifications", $data, "POST");
      return new Notification($this, $response);
    }

    /**
     * Modify notification.
     *
     * @param Notification modified notification object
     */
    public function modify_notification($notification) {
      $data = array("observe_key" => $notification->observe_key,
                    "notify_uri"  => $notification->notify_uri,
                    "state"       => $notification->state);
      $this->query_api("/rest/notifications/".$notification->notification_id, $data, "PUT");
    }

    /**
     * Unregister notification.
     *
     * @param Notification notification object which should be deleted
     */
    public function remove_notification($notification) {
      $this->query_api("/rest/notifications/".$notification->notification_id, null, "DELETE");
    }

}

?>
