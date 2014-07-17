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
 * Object representing an user
 */
class User extends Base {
    
    protected $dump_attributes = array("name", "address", "send_newsletter", "language");

    /** @var string Internal figo Connect user ID */
    public $user_id;

    /** @var string First and last name */
    public $name;

    /** @var string Email address */
    public $email;

    /** @var array Postal address for bills, etc. */
    public $address;

    /** @var boolean This flag indicates whether the email address has been verified */
    public $verified_email;

    /** @var boolean This flag indicates whether the user has agreed to be contacted by email */
    public $send_newsletter;

    /** @var string Two-letter code of preferred language */
    public $language;

    /** @var boolean This flag indicates whether the figo Account plan is free or premium */
    public $premium;

    /** @var DateTime Timestamp of premium figo Account expiry */
    public $premium_expires_on;

    /** @var string Provider for premium subscription or Null of no subscription is active */
    public $premium_subscription;

    /** @var DateTime Timestamp of figo Account registration */
    public $join_date;
}
