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
     * @param string $path the URL path on the server
     * @param array $data this optional associative array will be used as JSON-encoded POST content.
     * @param string $method the HTTP method
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
     * Retrieve the current figo Account
     *
     * @return User 'User' object for the current figo Account
     */
    public function get_user() {
        $response = $this->query_api("/rest/user");
        return (is_null($response) ? null : new User($this, $response));
    }

    /**
     * Modify the current figo Account
     *
     * @param User modified user object to be saved
     * @return User 'User' object for the updated figo Account
     */
    public function modify_user($user) {
        $response = $this->query_api("/rest/user", $user->dump(), "PUT");
        return (is_null($response) ? null : new User($this, $response));
    }


    /**
     * Re-send verification E-Mail
     *
     * @return array
     */
    public function resend_verification() {
        return $this->query_api('/rest/user/resend_verification',null, 'POST');
    }


    /**
     * Delete the current figo account
     *
     * NOTE: this has an immidiate effect and you will not be able to interact with the account afterward
     */
    public function remove_user() {
        $this->query_api("/rest/user", null, "DELETE");
    }


    /**
     * Poll the task progress
     *
     * @param String $task_token given Task Token
     * @param array $options Optional Options:
     *           - `pin` - Submit PIN. If this parameter is set, then the parameter save_pin must be set, too.
     *           - `continue` - This flag signals to continue after an error condition or to skip a PIN or challenge-response entry
     *           - `save_pin` - This flag indicates whether the user has chosen to save the PIN on the figo Connect server
     *           - `response` - Submit response to challenge.
     *
     * @return array an array of <code>Account</code> objects, one for each account the user has
     *         granted the app access
     */
    public function get_task_state($task_token, $options=array()) {

        $transfer_options = array(
            'id' => $task_token,
            'save_pin' => isset($options['save_pin']) ? $options['save_pin'] : 0,
            'continue' => isset($options['continue']) ? $options['continue'] : 0,
        );

        $response = $this->query_api("/task/progress?id=". $task_token, $transfer_options, 'POST');

        return $response;
    }

    /**
     * Cancel Task
     * @param String $task_token given Task Token
     *
     * @return void
     */
    public function cancel_task($task_token) {
        return $this->query_api("/task/cancel?id=" . $task_token, array('id' => $task_token), 'POST');
    }



    /**
     * Retrieve list of accounts
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
     * Retrieve a specific account
     *
     * @param string account_id ID of the account to be retrieved
     * @return Account account object
     */
    public function get_account($account_id) {
        $response = $this->query_api("/rest/accounts/".$account_id);
        return (is_null($response) ? null : new Account($this, $response));
    }

    /**
     * Modify an account
     *
     * @param Account the modified account to be saved
     * @return Account 'Account' object for the updated account returned by server
     */
    public function modify_account($account) {
        $response = $this->query_api("/rest/accounts/".$account->account_id, $account->dump(), "PUT");
        return (is_null($response) ? null : new Account($this, $response));
    }

    /**
     * Remove an account
     *
     * @param string Account to be removed or its ID
     */
    public function remove_account($account_or_id) {
        if (is_string($account_or_id)) {
            $this->query_api("/rest/accounts/".$account_or_id, null, "DELETE");
        } else {
            $this->query_api("/rest/accounts/".$account_or_id->account_id, null, "DELETE");
        }
    }

    /**
     * Retrieve balance and account limits
     *
     * @param string ID of the account to be retrieved
     * @return AccountBalance 'AccountBalance' object for the respective account
     */
    public function get_account_balance($account_id) {
        $response = $this->query_api("/rest/accounts/".$account_id."/balance");
        return (is_null($response) ? null : new AccountBalance($this, $response));
    }

    /**
     * Modify balance or account limits
     *
     * @param string ID of the account to be modified
     * @param AccountBalance modified AccountBalance object to be saved
     * @return AccountBalance 'AccountBalance' object for the updated account as returned by the server
     */
    public function modify_account_balance($account_id, $account_balance) {
        $response = $this->query_api("/rest/accounts/".$account_id."/balance", $account_balance->dump(), "PUT");
        return (is_null($response) ? null : new AccountBalance($this, $response));
    }

    /**
     * Retrieve list of transactions (for one account or all)
     *
     * @param string  $account_id      ID of the account of which the transactions should be retrieved or null for all accounts
     * @param mixed   $since           this parameter can either be a transaction ID or a date
     * @param integer $count           limit the number of returned transactions
     * @param integer $offset          which offset into the result set should be used to determin the first transaction to return (useful in combination with count)
     * @param boolean $include_pending this flag indicates whether pending transactions should be included in the
     *        response; pending transactions are always included as a complete set, regardless of
     *        the `since` parameter
     * @return array an array of <code>Transaction</code> objects, one for each transaction of the user
     */
    public function get_transactions($account_id = null, $since = null, $count = 1000, $offset = 0, $include_pending = false) {
        $data = array("count" => $count, "offset" => $offset, "include_pending" => $include_pending ? "1" : "0");
        if (!is_null($since))
            $data["since"] = is_a($since, "\DateTime") ? $since->format("Y-m-d") : $since;

        if (is_null($account_id)) {
            $response = $this->query_api("/rest/transactions?".http_build_query($data));
        } else {
            $response = $this->query_api("/rest/accounts/".$account_id."/transactions?".http_build_query($data));
        }

        $result = array();
        foreach ($response["transactions"] as $entry) {
            array_push($result, new Transaction($this, $entry));
        }
        return $result;
    }

    /**
     * Retrieve a specific standing Order.
     *
     * @param String $standing_order_id Id of standing Order to retrieve
     * @param boolean $cents whetever to show the balance in cents
     *
     * @return StandingOrder
     */
    public function get_standing_order($standing_order_id, $cents=false) {
        $response = $this->query_api('/rest/standing_orders/'.$standing_order_id, array('cents' => $cents),'GET');
        return (is_null($response) ? null : new StandingOrder($this, $response));
    }


    /**
     * Get all standing orders.
     *
     * @param bool $cents
     * @return array
     */
    public function get_standing_orders($cents = false) {
        $response = $this->query_api("/rest/standing_orders", array('cents' => $cents), 'GET');
        $result = array();
        foreach ($response["standing_orders"] as $account) {
            array_push($result, new StandingOrder($this, $account));
        }
        return $result;
    }



    /**
     * Modify transaction for a transaction_id, one account or all Accounts. Only visited param can be change.
     *
     * @param int $transaction_id
     * @param int $account_id
     * @param boolean $visited
     *
     * @return array
     */
    public function modify_transaction(Transaction $transaction) {
        if ($transaction->transaction_id && $transaction->account_id) {
            return $response = $this->query_api("/rest/accounts/".$transaction->account_id."/transactions/".$transaction->transaction_id, $transaction->dump(), "PUT");
        } else {
            throw new Exception("Not a valid Transaction Object: transaction_id or account_id is missing");
        }
        return $response;
    }

    /**
     * Modify transaction for a transaction_id, one account or all Accounts. Only visited param can be change.
     *
     * @param String $account_id Optional, can be null
     * @param boolean $visited
     *
     * @return void
     */
    public function modify_transactions($account_id = null, $visited) {
        $data = array('visited' => $visited);
        if ($account_id) {
            $response = $this->query_api("/rest/accounts/".$account_id."/transactions", $data, "PUT");
        } else {
            $response = $this->query_api("/rest/transactions", $data, "PUT");
        }
    }

    /**
     * Retrieve a specific transaction
     *
     * @param string ID of the account on which the transaction occured
     * @param string ID of the transaction to be retrieved
     * @return Transaction a `Transaction` object representing the transaction to be retrieved
     */
    public function get_transaction($account_id, $transaction_id) {
        $response = $this->query_api("/rest/accounts/".$account_id."/transactions/".$transaction_id);
        return (is_null($response) ? null : new Transaction($this, $response));
    }

    /**
     * Remove a specific transaction
     *
     * @param string ID of the account on which the transaction occured
     * @param string ID of the transaction to be removed
     */
    public function remove_transaction($account_id, $transaction_id) {
        $this->query_api("/rest/accounts/".$account_id."/transactions/".$transaction_id, null, "DELETE");
    }

    /**
     * Retrieve a specific bank
     *
     * @param string ID of the bank to be retrieved
     * @return Bank a 'Bank' object representing the bank to be retrieved
     */
    public function get_bank($bank_id) {
        $response = $this->query_api("/rest/banks/".$bank_id);
        return (is_null($response) ? null : new Bank($this, $response));
    }

    /**
     * Modify a bank
     *
     * @param Bank modified bank object to be saved
     * @return Bank 'Bank' object for the updated bank
     */
    public function modify_bank($bank) {
        $response = $this->query_api("/rest/banks/".$bank->bank_id, $bank->dump(), "PUT");
        return (is_null($response) ? null : new Bank($this, $response));
    }

    /**
     * Remove the stored PIN for a bank (if there was one)
     *
     * @param string ID of the bank whose PIN should be removed or its ID
     */
    public function remove_bank_pin($bank_or_id) {
        if (is_string($bank_or_id)) {
            $response = $this->query_api("/rest/banks/".$bank_or_id."/remove_pin", null, "POST");
        } else {
            $response = $this->query_api("/rest/banks/".$bank_or_id->bank_id."/remove_pin", null, "POST");
        }
    }

    /**
     * Request the URL a user should open in the web browser to start the synchronization process
     *
     * @param string the user will be redirected to this URL after the process completes
     * @param string this string will be passed on through the complete synchronization process
     *        and to the redirect target at the end. It should be used to validated the authenticity of
     *        the call to the redirect URL
     * @return string the URL to be opened by the user
     */
    public function get_sync_url($redirect_uri, $state) {
        $data = array("redirect_uri" => $redirect_uri, "state" => $state);
        $response = $this->query_api("/rest/sync", $data, "POST");
        return "https://".Config::$API_ENDPOINT."/task/start?id=".$response["task_token"];
    }




    /**
     * Start a synchronization task and return its token id
     *
     * @param string the user will be redirected to this URL after the process completes
     * @param string this string will be passed on through the complete synchronization process
     *        and to the redirect target at the end. It should be used to validated the authenticity of
     *        the call to the redirect URL
     * @return string the token id of the taks
     */
    public function start_sync($redirect_uri, $state) {
        $data = array("redirect_uri" => $redirect_uri, "state" => $state);
        $response = $this->query_api("/rest/sync", $data, "POST");
        return $response["task_token"];
    }




    /**
     * Retrieve all notifications
     *
     * @return array an array of <code>Notification</code> objects, one for each registered notification
     */
    public function get_notifications() {
        $response = $this->query_api("/rest/notifications");
        $notifications = array();
        foreach ($response["notifications"] as $notification) {
            array_push($notifications, new Notification($this, $notification));
        }
        return $notifications;
    }

    /**
     * Retrieve a specific notification
     *
     * @param string account_id ID of the notification to be retrieved
     * @return Notification notification object
     */
    public function get_notification($notification_id) {
        $response = $this->query_api("/rest/notifications/".$notification_id);
        return (is_null($response) ? null : new Notification($this, $response));
    }

    /**
     * Register a new notification.
     *
     * @param Notification new notification to be created. It should have no notification_id set
     * @return Notification newly created <code>Notification</code> object
     */
    public function add_notification($notification) {
        $response = $this->query_api("/rest/notifications", $notification->dump(), "POST");
        return (is_null($response) ? null : new Notification($this, $response));
    }

    /**
     * Modify an existing notification.
     *
     * @param Notification modified notification object
     * @return Notification 'Notification' object for the modified notification
     */
    public function modify_notification($notification) {
        $response = $this->query_api("/rest/notifications/".$notification->notification_id, $notification->dump(), "PUT");
        return (is_null($response) ? null : new Notification($this, $response));
    }

    /**
     * Unregister notification.
     *
     * @param Notification notification_or_id object which should be deleted or its ID
     *
     * @return void
     */
    public function remove_notification($notification_or_id) {
        if(is_string($notification_or_id)) {
            $this->query_api("/rest/notifications/".$notification_or_id, null, "DELETE");
        } else {
            $this->query_api("/rest/notifications/".$notification_or_id->notification_id, null, "DELETE");
        }
    }

    /**
     * Retrieve all payments (of all or one account)
     *
     * @param string ID of the account of which to retrieve the payments
     * @return array an array of <code>Payment</code> objects, one for each payment
     */
    public function get_payments($account_id = null) {
        if (is_null($account_id)) {
            $response = $this->query_api("/rest/payments");
        } else {
            $response = $this->query_api("/rest/accounts/".$account_id."/payments");
        }

        $result = array();
        foreach ($response["payments"] as $entry) {
            array_push($result, new Payment($this, $entry));
        }
        return $result;
    }

    /**
     * Retrieve a specific payment
     *
     * @param string ID of the account on which the payment is to be found
     * @param string ID of the payment to be retrieved
     * @return Payment payment object
     */
    public function get_payment($account_id, $payment_id) {
        $response = $this->query_api("/rest/accounts/".$account_id."/payments/".$payment_id);
        return (is_null($response) ? null : new Payment($this, $response));
    }

    /**
     * Create a new payment
     *
     * @param Payment payment to be created. It should not have its payment_id set
     * @return Payment newly created <code>Payment</code> object
     */
    public function add_payment($payment) {
        $response = $this->query_api("/rest/accounts/".$payment->account_id."/payments", $payment->dump(), "POST");
        return (is_null($response) ? null : new Payment($this, $response));
    }

    /**
     * Modify payment.
     *
     * @param Payment modified payment object to be saved
     * @return Payment 'Payment' object for the updated payment
     */
    public function modify_payment($payment) {
        $response = $this->query_api("/rest/accounts/".$payment->account_id."/payments/".$payment->payment_id, $payment->dump(), "PUT");
        return (is_null($response) ? null : new Payment($this, $response));
    }

    /**
     * Delete payment.
     *
     * @param string ID of the account on which the payment can be found
     * @param string ID of the payment to be deleted (or null when using a payment instance as first parameter)
     *
     * @return void
     */
    public function remove_payment($account_id_or_payment, $payment_id=null) {
        if(is_string($account_id_or_payment)) {
            if(is_string($payment_id)) {
                $this->query_api("/rest/accounts/".$account_id_or_payment."/payments/".$payment_id, null, "DELETE");
            } else {
                throw new Exception('invalid_request', 'Missing payment_id parameter');
            }
        } else {
            $this->query_api("/rest/accounts/".$account_id_or_payment->account_id."/payments/".$account_id_or_payment->payment_id, null, "DELETE");
        }
    }

    /**
     * Retrieve securities of one or all accounts
     *
     * @param String $account_id  Id of the Account the security belongs to
     * @param String $security_id Id of the security to retrieve
     *
     * return Figo\Security
     */
    public function get_security($account_id, $security_id) {
        $response = $this->query_api("/rest/accounts/".$account_id."/securities/".$security_id);
        return (is_null($response) ? null : new Security($this, $response));
    }

    /**
     * Retrieve securities of one or all accounts.
     *
     * @param Array $options further options (all are optional)
    - **account_id** (`String`) - ID of the account for which to retrieve the securities
    - **accounts** (`Array`) - filter the securities to be only from these accounts
    - **since** (`Date`) - ISO date filtering the returned securities by their creation or last modification date
    - **since_type** (`String`) - defines hot the `since` will be interpreted: `traded`, `created` or `modified`
    - **count** (`Number`) - limit the number of returned transactions
    - **offset** (`Number`) - offset into the implicit list of transactions
     *
     * @return array
     */
    public function get_securities($options) {
        if (isset($options['account_id'])) {
            $account_id = $options['account_id'];
            unset($options['account_id']);
            $response = $this->query_api("/rest/accounts/".$account_id."/securities", $options, 'GET');
        } else {
            $response = $this->query_api("/rest/securities", $options, 'GET');
        }
        $result = array();
        foreach ($response["securities"] as $entry) {
            array_push($result, new Security($this, $entry));
        }

        return $result;
    }

    /**
     * Modify a Security.
     *
     * @param $account_id  ID of the account the security belongs to
     * @param $security_id ID of the security to change
     * @param $visited     a bit showing whether the user has already seen this security or not
     *
     * @return array
     */
    public function modify_security($account_id, $security_id, $visited) {
        $response = $this->query_api("/rest/accounts/".$account_id."/securities/" . $security_id ,array('visited' => $visited), 'PUT');
        return $response;
    }


    /**
     * Modify securities of one or all accounts.
     *
     * @param $account_id optional Account Id
     * @param $visited
     *
     * @return void
     */
    public function modify_securities($account_id=null, $visited) {
        if ($account_id) {
            $response = $this->query_api("/rest/accounts/".$account_id."/securities", array('visited' => $visited), 'PUT');
        } else {
            $response = $this->query_api("/rest/securities", array('visited' => $visited) , 'PUT');
        }
    }

    /**
     * Submit payment to bank server
     *
     * @param Payment payment to be submitted
     * @param string TAN scheme ID of user-selected TAN scheme
     * @param string Any kind of string that will be forwarded in the callback response message
     * @param string At the end of the submission process a response will be sent to this callback URL
     * @return string the URL to be opened by the user for the TAN process
     */
    public function submit_payment($payment, $tan_scheme_id, $state, $redirect_uri=null) {
        $data = array("tan_scheme_id" => $tan_scheme_id, "state" => $state);
        if (!is_null($redirect_uri)) {
            $data['redirect_uri'] = $redirect_uri;
        }

        $response = $this->query_api("/rest/accounts/".$payment->account_id."/payments/".$payment->payment_id."/submit", $data, "POST");
        if (is_null($response)) {
            return  null;
        } else {
            return "https://".Config::$API_ENDPOINT."/task/start?id=".$response["task_token"];
        }
    }
}

?>