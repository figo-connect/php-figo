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
 * Object representing one bank account of the user
 */
class Account extends Base {

    protected $dump_attributes = array("name", "owner", "auto_sync");

    /** @var string Internal figo Connect account ID */
    public $account_id;

    /** @var string Internal figo Connect bank ID */
    public $bank_id;

    /** @var string Account name */
    public $name;

    /** @var string Account owner */
    public $owner;

    /** @var boolean This flag indicates whether the account will be automatically synchronized */
    public $auto_sync;

    /** @var string Account number */
    public $account_number;

    /** @var string Bank code */
    public $bank_code;

    /** @var string Bank name */
    public $bank_name;

    /** @var string Three-character currency code */
    public $currency;

    /** @var string IBAN */
    public $iban;

    /** @var string BIC */
    public $bic;

    /** @var string Account type */
    public $type;

    /** @var string Account icon URL */
    public $icon;

    /** @var dict Account icon in other resolutions */
    public $additional_icons;

    /** @var boolean This flag indicates whether the balance of this account is added to the total balance of accounts */
    public $in_total_balance;

    /** @var SynchronizationStatus Synchronization status object */
    public $status;

    /** @var AccountBalance balance of this account */
    public $balance;

    /**
     * Retrieve list of transactions of this account
     *
     * @param mixed this parameter can either be a transaction ID or a date
     * @param integer limit the number of returned transactions
     * @param integer which offset into the result set should be used to determin the first transaction to return (useful in combination with count)
     * @param boolean this flag indicates whether pending transactions should be included in the
     *        response; pending transactions are always included as a complete set, regardless of
     *        the `since` parameter
     * @return array an array of <code>Transaction</code> objects, one for each transaction of this account
     */
    public function get_transactions($since = null, $count = 1000, $offset = 0, $include_pending = false) {
        return $this->session->get_transactions($this->account_id, $since, $count, $offset, $include_pending);
    }

    /**
     * Retrieve specific transaction
     *
     * @param string ID of the transaction to be retrieved
     * @return Transaction transaction object
     */
    public function get_transaction($transaction_id) {
        return $this->session->get_transaction($this->account_id, $transaction_id);
    }

    /**
     * Retrieve Bank correspoding to this account
     *
     * @return Bank bank object
     */
    public function get_bank() {
        return $this->session->get_bank($this->bank_id);
    }

    /**
     * Retrieve all payments on this account
     *
     * @return array an array of <code>Payment</code> objects
     */
    public function get_payments() {
        return $this->session->get_payments($this->account_id);
    }

    /**
     * Retrieve a specific payment on this account
     *
     * @param string ID of the payment to be retrieved
     * @return Payment payment object
     */
    public function get_payment($payment_id) {
        return $this->session->get_payment($this->account_id, $payment_id);
    }
}

?>
