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
 * Global configuration
 */
class Config {

    /** @var string figo Connect server address. This should be the full base url of the API */
    public static $API_ENDPOINT = "https://api.figo.me/v3";

    /** @var string figo Connect SSL/TLS certificate fingerprints */
    public static $VALID_FINGERPRINTS = array("CD:F3:D3:26:27:89:91:B9:CD:AE:4B:10:6C:96:81:B7:EB:B3:38:10:C4:72:37:6A:4D:9C:84:B7:B3:DC:D6:8D",
                                              "79:B2:A2:93:00:85:3B:06:92:B1:B5:F2:24:79:48:58:3A:A5:22:0F:C5:CD:E9:49:9A:C8:45:1E:DB:E0:DA:50");
    /**
     * @var string User agent used for API requests
     */
    public static $USER_AGENT = 'php_figo';
    /**
     * @var string Version of this SDK, used in user agent for API requests
     */
    public static $SDK_VERSION = '3.0.1';
}

?>
