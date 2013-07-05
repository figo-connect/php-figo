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


class FigoTest extends PHPUnit_Framework_TestCase {

    public function test_accounts() {
        $session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

        $accounts = $session->get_accounts();
        $this->assertGreaterThan(0, count($accounts));

        $account = $session->get_account("A1.1");
        $this->assertEquals($account->account_id, "A1.1");

        $account = $session->get_account("A1.2");
        $this->assertEquals($account->account_id, "A1.2");

        $balance = $account->get_balance();
        $this->assertFalse(is_null($balance->balance));
        $this->assertFalse(is_null($balance->balance_date));

        $transactions = $account->get_transactions();
        $this->assertGreaterThan(0, count($transactions));
    }

    public function test_global_transactions() {
        $session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

        $transactions = $session->get_transactions();
        $this->assertGreaterThan(0, count($transactions));
    }

    public function test_global_notifications() {
        $session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

        $notifications = $session->get_notifications();
        $this->assertGreaterThanOrEqual(0, count($notifications));
    }

    public function test_sync_url() {
        $session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

        $sync_url = $session->get_sync_url("qwe", "qew");
        $this->assertGreaterThan(0, strlen($sync_url));
    }

    public function test_create_update_delete_notification() {
        $session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

        $notification = $session->add_notification("/rest/transactions", "http://figo.me/test", "qwe");
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "qwe");

        $notification->state = "asd";
        $session->modify_notification($notification);

        $notification = $session->get_notification($notification->notification_id);
        $this->assertEquals($notification->observe_key, "/rest/transactions");
        $this->assertEquals($notification->notify_uri, "http://figo.me/test");
        $this->assertEquals($notification->state, "asd");

        $session->remove_notification($notification);
        $notification = $session->get_notification($notification->notification_id);
        $this->assertNull($notification);
    }
}

?>
