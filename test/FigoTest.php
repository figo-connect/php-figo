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

use figo\Session;
use figo\Connection;
use figo\Notification;
use figo\Payment;


class SessionTest extends PHPUnit_Framework_TestCase {

    protected static $api_endpoint;
    protected static $connection;
    protected static $email;
    protected static $fingerprints;
    protected static $password;
    protected static $session;

    protected $access_token;
    protected $account_id;

    public static function setUpBeforeClass()   {
        $fingerprints = explode(",", getenv("FIGO_SSL_FINGERPRINT"));
        $api_endpoint = getenv("FIGO_API_ENDPOINT");
        self::$connection = new Connection(getenv("FIGO_CLIENT_ID"), getenv("FIGO_CLIENT_SECRET"),
            "http://example.com/callback.php", $api_endpoint, $fingerprints);
        $name = "PHP SDK Test";
        self::$email = "php.sdk.".rand()."@figo.io";
        self::$password = "sdk_test_pass_".rand();
        self::$connection->create_user($name, self::$email, self::$password);
        $response = self::$connection->native_client_login(self::$email, self::$password);
        $access_token = $response["access_token"];
        self::$session = new Session($access_token, $api_endpoint, $fingerprints);
    }

    public static function tearDownAfterClass() {
        self::$session->remove_user();
    }

    public function test_credential_login() {

        $accounts = $this::$session->get_accounts();
        $this->assertEquals([], $accounts);
    }

    public function test_setup_account()    {
        $response = $this::$session->setup_bank_account("90090042", array("figo", "figo"), array());
        for($i = 0; $i <= 20; $i++)   {
            $task_state = $this::$session->get_task_state($response['task_token']);
            if($task_state['is_ended'] == true) {
                break;
            }
            $this->assertFalse($task_state['is_erroneous']);
            sleep(1);
        }
    }

    public function test_get_accounts()   {
        $accounts = $this::$session->get_accounts();
        $this->assertEquals(3, count($accounts));
        $this->account_id = $accounts[0]->account_id;
        $single_account = $this::$session->get_account($this->account_id);
        $this->assertEquals($this->account_id, $single_account->account_id);
        $this->assertNotNull($single_account->balance->balance);
        $this->assertNotNull($single_account->balance->balance_date);
    }

    public function test_list_transactions()
    {
        $accounts = $this::$session->get_accounts();
        $this->account_id = $accounts[0]->account_id;
        $transactions = $this::$session->get_transactions();
        $this->assertGreaterThan(0, $transactions);
        $transactions = $this::$session->get_transactions($this->account_id);
        $this->assertGreaterThan(0, $transactions);
    }

    public function test_list_all_standing_orders() {
        $standing_orders = $this::$session->get_standing_orders();
        $this->assertEquals(0, count($standing_orders));
    }

    public function test_list_securities()  {
        $options = array();
        $securities = $this::$session->get_securities($options);
        $this->assertGreaterThan(0, $securities);
        $this->account_id = $securities[0]->account_id;
        $options["account_id"] = $this->account_id;
        $securities = $this::$session->get_securities($options);
        $this->assertGreaterThan(0, $securities);
    }

    public function test_list_all_payments()    {
        $payments = $this::$session->get_payments();
        $this->assertEquals(0, count($payments));
    }

    public function test_list_account_payments()    {
        $accounts = $this::$session->get_accounts();
        $this->account_id = $accounts[0]->account_id;
        $payments = $this::$session->get_payments($this->account_id);
        $this->assertEquals(0, count($payments));
    }

    public function test_list_all_notification_subscriptions()  {
        $notifications = $this::$session->get_notifications();
        $this->assertEquals(0, count($notifications));
    }

    public function test_list_user_information()    {
        $user = $this::$session->get_user();
        $this->assertEquals($this::$email, $user->email);
    }

    public function test_missing_handling() {
        $this->assertNull($this::$session->get_account("WRONG"));
    }

    public function test_error_handling() {
         $this->setExpectedException('Exception');
         $this::$session->get_sync_url("random", "");
    }

    public function test_sync_url() {
        $sync_url = $this::$session->get_sync_url("http://example.com/callback.php", "qew");
        $this->assertGreaterThan(0, strlen($sync_url));
    }

    public function test_create_update_delete_notification() {
        $notification = $this::$session->add_notification(new Notification($this::$session, array("observe_key" => "/rest/transactions", "notify_uri" => "http://figo.me/test", "state" => "qwe")));
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "qwe");

        $notification->state = "asd";
        $this::$session->modify_notification($notification);

        $notification = $this::$session->get_notification($notification->notification_id);
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "asd");

        $this::$session->remove_notification($notification);
        $notification = $this::$session->get_notification($notification->notification_id);
        $this->assertNull($notification);
    }

    public function test_create_update_delete_payment() {
        $accounts = $this::$session->get_accounts();
        $this->account_id = $accounts[0]->account_id;
        $added_payment = $this::$session->add_payment(new Payment($this::$session, array("account_id" => $this->account_id, "type" => "Transfer", "account_number" => "4711951501", "bank_code" => "90090042", "name" => "figo", "purpose" => "Thanks for all the fish.", "amount" => 0.89)));
        $this->assertEquals($added_payment->account_id, $this->account_id);
        $this->assertEquals($added_payment->bank_name, "Demobank");
        $this->assertEquals($added_payment->amount, 0.89);

        $added_payment->amount = 2.39;
        $modified_payment = $this::$session->modify_payment($added_payment);
        $this->assertEquals($modified_payment->payment_id, $added_payment->payment_id);
        $this->assertEquals($modified_payment->account_id, $this->account_id);
        $this->assertEquals($modified_payment->bank_name, "Demobank");
        $this->assertEquals($modified_payment->amount, 2.39);

        $this::$session->remove_payment($modified_payment);
        $retrieved_payment = $this::$session->get_payment($modified_payment->account_id, $modified_payment->payment_id);
        $this->assertNull($retrieved_payment);
    }

    public function test_get_catalog_in_english()   {
        $response = $this::$connection->get_supported_payment_services(null, null, "en");
        $this->assertEquals("en", $response["banks"][0]["language"]["current_language"]);
    }

    public function test_get_catalog_unsupported_language()
    {
        $this->setExpectedException(
            \figo\Exception::class, 'Code: 1000, Unsupported language'
        );
        $this::$connection->get_supported_payment_services(null, null, 'fr');
    }

    public function test_get_bank() 
    {
        $accounts = $this::$session->get_accounts();
        $bank_id = $accounts[0]->bank_id;
        $bank = $this::$session->get_bank($bank_id);
        $this->assertNotNull($bank);
    }
}
?>
