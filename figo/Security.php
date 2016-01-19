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
class Security extends Base {

    protected $dump_attributes = array("name", "isin", "wkn", "currency", "quantity", "amount", "amount_original_currency", "exchange_rate", "price", "price_currency", "purchase_price", "purchase_price_currency", "visited");

    /** @var string Internal figo Connect security ID */
    public $security_id;

    /** @var string Internal figo Connect account ID */
    public $account_id;

    /** @var string Name of the security */
    public $name;

    /** @var string International Securities Identification Number */
    public $isin;

    /** @var string Wertpapierkennnummer (if available) */
    public $wkn;

    /** @var string Three-character currency code when measured in currency (and not pieces) */
    public $currency;

    /** @var string Number of pieces or value */
    public $quantity;

    /** @var float Monetary value in account currency */
    public $amount;

    /** @var float Monetary value in trading currency */
    public $amount_original_currency;

    /** @var float Exchange rate between trading and account currency */
    public $exchange_rate;

    /** @var float current price */
    public $price;

    /** @var string Currency of current price */
    public $price_currency;

    /** @var float Purchase price */
    public $purchase_price;

    /** @var String Currency of purchase price */
    public $purchase_price_currency;

    /** @var Boolean This flag indicates whether the security has already been marked as visited by the user */
    public $visited;

    /** @var DateTime Trading Internal creation timestamp on the figo Connect server */
    public $creation_timestamp;

    /** @var DateTime Internal modification timestamp on the figo Connect server */
    public $modification_timestamp;
}

?>