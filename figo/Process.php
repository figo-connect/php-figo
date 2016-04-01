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
class Process {

    protected $dump_attributes = array("email", "password", "redirect_uri", "state", "steps");

    /** @var string The email of the existing user to use as context or the new user to create beforehand */
    public $email;

    /** @var string The password of the user existing or new user */
    public $password;

    /** @var string The authorization code will be sent to this callback URL */
    public $redirect_uri;

    /** @var string Any kind of string that will be forwarded in the callback response message */
    public $state;

    /** @var string A list of steps definitions */
    public $steps;

    public function dump() {
        $result = array();
        foreach ($this->dump_attributes as $attribute) {
            if (!is_null($this->$attribute)) {
                $result[$attribute] = $this->$attribute;
            }

        }
        return $result;
    }

}

