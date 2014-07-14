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
 * Abstract base class for model objects
 */
class Base {

    protected $session;

    protected $dump_attributes = array();

    /**
     * Constructor
     *
     * @param Session figo <code>Session</code> object
     * @param array use keys of this associative array for model object creation
     */
    public function __construct($session, array $map) {
        $this->session = $session;

        foreach ($map as $key => $value) {
            $this->$key = $value;
            if ($key === "status" && is_array($value)) {
                $this->$key = new SynchronizationStatus($session, $value);
            } elseif ($key === "balance" && is_array($value)) {
                $this->$key = new AccountBalance($session, $value);
            } elseif (substr($key, -5) === "_date" || substr($key, -10) === "_timestamp") {
                $this->$key = new \DateTime($value, new \DateTimeZone("UTC"));
            } else {
                $this->$key = $value;
            }
        }
    }

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

?>
