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
 * Object representing a payment
 */
class Payment extends Base {

    protected $dump_attributes = array("type", "name", "account_number", "bank_code", "amount", "currency", "purpose");

    /** @var string Internal figo Connect payment ID */
    public $payment_id;

    /** @var string Internal figo Connect account ID */
    public $account_id;

    /** @var string Payment type */
    public $type;

    /** @var string Name of creditor or debtor */
    public $name;

    /** @var string Account number of creditor or debtor */
    public $account_number;

    /** @var string Bank code of creditor or debtor */
    public $bank_code;

    /** @var string Bank name of creditor or debtor */
    public $bank_name;

    /** @var string Icon of creditor or debtor bank */
    public $bank_icon;

    /** @var dictionary Icon of the creditor or debtor bank in other resolutions */
    public $bank_additional_icons;

    /** @var float Order amount */
    public $amount;

    /** @var string Three-character currency code */
    public $currency;

    /** @var string Purpose text */
    public $purpose;

    /** @var DateTime Timestamp of submission to the bank server */
    public $submission_timestamp;

    /** @var DateTime Internal creation timestamp on the figo Connect server */
    public $creation_timestamp;

    /** @var DateTime Internal modification timestamp on the figo Connect server */
    public $modification_timestamp;

    /** @var string Transaction ID. This field is only set if the payment has been matched to a transaction */
    public $transaction_id;
}
