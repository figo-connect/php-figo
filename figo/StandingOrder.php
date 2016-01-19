<?php
/*
 * Copyright (c) 2016 figo GmbH
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
class StandingOrder extends Base {

    protected $dump_attributes = array("type", "name", "account_number", "bank_code", "amount", "currency", "purpose");

    /** @var string Internal figo Connect standing ID */
    public $standing_order_id;

    /** @var string Internal figo Connect account ID */
    public $account_id;

    /** @var string First execution date of the standing order */
    public $first_execution_date;

    /** @var string The day the standing order gets executed */
    public $execuction_day;

    /** @var string The interval the standing order gets executed (possible values are weekly, monthly, two monthly, quarterly, half yearly and yearly) */
    public $interval;

    /** @var string Name of recipient */
    public $name;

    /** @var string Account number recipient */
    public $account_number;

    /** @var string Bank code of recipient */
    public $bank_code;

    /** @var string Bank name of recipient */
    public $bank_name;

    /** @var float Standing order amount */
    public $amount;

    /** @var string Three-character currency code */
    public $currency;

    /** @var string Purpose text */
    public $purpose;

    /** @var DateTime Internal creation timestamp on the figo Connect server */
    public $creation_timestamp;

    /** @var DateTime Internal modification timestamp on the figo Connect server */
    public $modification_timestamp;

}
