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


class FigoTest extends PHPUnit_Framework_TestCase {

    protected $sut;

    protected function setUp() {
        $this->sut = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");
    }

    public function test_accounts() {
        $accounts = $this->sut->get_accounts();
        $this->assertGreaterThan(0, count($accounts));

        $account = $this->sut->get_account("A1.1");
        $this->assertEquals($account->account_id, "A1.1");

        $account = $this->sut->get_account("A1.2");
        $this->assertEquals($account->account_id, "A1.2");
        $this->assertNotNull($account->balance->balance);
        $this->assertNotNull($account->balance->balance_date);

        $transactions = $account->get_transactions();
        $this->assertGreaterThan(0, count($transactions));

        $payments = $account->get_payments();
        $this->assertGreaterThanOrEqual(0, count($payments));
    }

    public function test_global_transactions() {
        $transactions = $this->sut->get_transactions();
        $this->assertGreaterThan(0, count($transactions));
    }

    public function test_global_notifications() {
        $notifications = $this->sut->get_notifications();
        $this->assertGreaterThanOrEqual(0, count($notifications));
    }

    public function test_global_payments() {
        $payments = $this->sut->get_payments();
        $this->assertGreaterThanOrEqual(0, count($payments));
    }

    public function test_missing_handling() {
        $this->assertNull($this->sut->get_account("A1.42"));
    }

    public function test_error_handling() {
         $this->setExpectedException('Exception');
         $this->sut->get_sync_url("http://localhost:3003/", "");
    }

    public function test_sync_url() {
        $sync_url = $this->sut->get_sync_url("qwe", "qew");
        $this->assertGreaterThan(0, strlen($sync_url));
    }

    public function test_user() {
        $this->assertEquals($this->sut->get_user()->email, "demo@figo.me");
    }

    public function test_create_update_delete_notification() {
        $notification = $this->sut->add_notification(new Notification($this->sut, array("observe_key" => "/rest/transactions", "notify_uri" => "http://figo.me/test", "state" => "qwe")));
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "qwe");

        $notification->state = "asd";
        $this->sut->modify_notification($notification);

        $notification = $this->sut->get_notification($notification->notification_id);
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "asd");

        $this->sut->remove_notification($notification);
        $notification = $this->sut->get_notification($notification->notification_id);
        $this->assertNull($notification);
    }

    public function test_create_update_delete_payment() {
        $added_payment = $this->sut->add_payment(new Payment($this->sut, array("account_id" => "A1.1", "type" => "Transfer", "account_number" => "4711951501", "bank_code" => "90090042", "name" => "figo", "purpose" => "Thanks for all the fish.", "amount" => 0.89)));
        $this->assertEquals($added_payment->account_id, "A1.1");
        $this->assertEquals($added_payment->bank_name, "Demobank");
        $this->assertEquals($added_payment->amount, 0.89);

        $added_payment->amount = 2.39;
        $modified_payment = $this->sut->modify_payment($added_payment);
        $this->assertEquals($modified_payment->payment_id, $added_payment->payment_id);
        $this->assertEquals($modified_payment->account_id, "A1.1");
        $this->assertEquals($modified_payment->bank_name, "Demobank");
        $this->assertEquals($modified_payment->amount, 2.39);

        $this->sut->remove_payment($modified_payment);
        $retrieved_payment = $this->sut->get_payment($modified_payment->account_id, $modified_payment->payment_id);
        $this->assertNull($retrieved_payment);
    }
}

?>
