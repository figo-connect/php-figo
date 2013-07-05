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

    /** @var string Account type; one of the constants of the <code>AccountType</code> object */
    public $type;

    /** @var string Account icon URL */
    public $icon;

    /** @var boolean This flag indicates whether the balance of this account is added to the total balance of accounts */
    public $in_total_balance;

    /** @var boolean This flag indicates whether this account is only shown as preview for an unpaid premium plan */
    public $preview;

    /** @var SynchronizationStatus Synchronization status object */
    public $status;

    /**
     * Request balance of this account
     *
     * @return AccountBalance account balance object
     */
    public function get_balance() {
        $response = $this->session->query_api("/rest/accounts/".$this->account_id."/balance");
        return new AccountBalance($this, $response);
    }

    /**
     * Request list of transactions of this account
     *
     * @param mixed this parameter can either be a transaction ID or a date
     * @param string do only return transactions which were booked after the start transaction ID
     * @param integer limit the number of returned transactions
     * @param boolean this flag indicates whether pending transactions should be included in the 
     *        response; pending transactions are always included as a complete set, regardless of
     *        the `since` parameter
     * @return array an array of <code>Transaction</code> objects, one for each transaction of this account
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
        $response = $this->session->query_api("/rest/accounts/".$this->account_id."/transactions?".http_build_query($data));
        $transactions = array();
        foreach ($response["transactions"] as $transaction) {
            array_push($transactions, new Transaction($this, $transaction));
        }
        return $transactions;
    }

    /**
     * Request specific transaction
     *
     * @param string ID of the transaction to be retrieved
     * @return Transaction transaction object
     */
    public function get_transaction($transaction_id) {
        $response = $this->session->query_api("/rest/accounts/".$this->account_id."/transactions/".$transaction_id);
        return (is_null($response) ? null : new Transaction($this, $response));
    }

}

?>
