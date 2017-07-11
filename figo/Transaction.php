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
 * Object representing one bank transaction on a certain bank account of the user
 */
class Transaction extends Base {

    protected $dump_attributes = array('visited');

    /** @var string Internal figo Connect transaction ID */
    public $transaction_id;

    /** @var string Internal figo Connect account ID */
    public $account_id;

    /** @var string Name of originator or recipient */
    public $name;

    /** @var string Account number of originator or recipient. */
    public $account_number;

    /** @var string Bank code of originator or recipient */
    public $bank_code;

    /** @var string Bank name of originator or recipient */
    public $bank_name;

    /** @var double Transaction amount */
    public $amount;

    /** @var string Three-character currency code */
    public $currency;

    /** @var DateTime Booking date */
    public $booking_date;

    /** @var DateTime Value date */
    public $value_date;

    /** @var string Purpose text */
    public $purpose;

    /** @var string Transaction type */
    public $type;

    /** @var string Booking text */
    public $booking_text;

    /** @var boolean This flag indicates whether the transaction is booked or pending */
    public $booked;

    /** @var DateTime Internal creation timestamp on the figo Connect server */
    public $creation_timestamp;

    /** @var DateTime Internal modification timestamp on the figo Connect server */
    public $modification_timestamp;

    /** @var boolean This flag indicates whether the transaction has already been marked as visited by the user */
    public $visited;

    /** @var array Contains additional information for PayPal transactions */
    public $additional_info;

    /** @var  Category[]*/
    public $categories;

    /** @var string BIC */
    public $bic;

    /** @var string IBAN */
    public $iban;

    /** @var string Booking key */
    public $booking_key;

    /** @var string Creditor ID */
    public $creditor_id;

    /** @var string Mandate reference */
    public $mandate_reference;

    /** @var string SEPA purpose code */
    public $sepa_purpose_code;

    /** @var string SEPA remittance info */
    public $sepa_remittance_info;

    /** @var string Text key addition */
    public $text_key_addition;

    /** @var string End to end reference */
    public $end_to_end_reference;

    /** @var string Customer reference */
    public $customer_reference;

    /** @var int Prima nota number */
    public $prima_nota_number;
}

?>
