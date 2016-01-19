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
class Task extends Base {

    protected $dump_attributes = array("account_id", "message", "is_waiting_for_pin", "is_waiting_for_response", "is_erroneous", "is_ended", "challenge");

    /** @var string Account ID of currently processed accoount */
    public $account_id;

    /** @var string Status message or error message for currently processed amount */
    public $message;

    /** @var string The figo Connect server is waiting for PIN */
    public $is_waiting_for_pin;

    /** @var bool The figo Connect server is waiting for a response to the parameter challenge */
    public $is_waiting_for_response;

    /** @var bool An error occured and the figo Connect server is waiting for continuation */
    public $is_erroneous;

    /** @var bool The communication with a bank server has been completed */
    public $is_ended;

    /** @var float Monetary value in account currency */
    public $challenge;

}

?>