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

    public static $CA_CERT_BUNDLE = array(
// Certificate:
//     Data:
//         Version: 3 (0x2)
//         Serial Number:
//             44:af:b0:80:d6:a3:27:ba:89:30:39:86:2e:f8:40:6b
//     Signature Algorithm: sha1WithRSAEncryption
//         Issuer: O=Digital Signature Trust Co., CN=DST Root CA X3
//         Validity
//             Not Before: Sep 30 21:12:19 2000 GMT
//             Not After : Sep 30 14:01:15 2021 GMT
//         Subject: O=Digital Signature Trust Co., CN=DST Root CA X3
//         Subject Public Key Info:
//             Public Key Algorithm: rsaEncryption
//                 Public-Key: (2048 bit)
//                 Modulus:
//                     00:df:af:e9:97:50:08:83:57:b4:cc:62:65:f6:90:
//                     82:ec:c7:d3:2c:6b:30:ca:5b:ec:d9:c3:7d:c7:40:
//                     c1:18:14:8b:e0:e8:33:76:49:2a:e3:3f:21:49:93:
//                     ac:4e:0e:af:3e:48:cb:65:ee:fc:d3:21:0f:65:d2:
//                     2a:d9:32:8f:8c:e5:f7:77:b0:12:7b:b5:95:c0:89:
//                     a3:a9:ba:ed:73:2e:7a:0c:06:32:83:a2:7e:8a:14:
//                     30:cd:11:a0:e1:2a:38:b9:79:0a:31:fd:50:bd:80:
//                     65:df:b7:51:63:83:c8:e2:88:61:ea:4b:61:81:ec:
//                     52:6b:b9:a2:e2:4b:1a:28:9f:48:a3:9e:0c:da:09:
//                     8e:3e:17:2e:1e:dd:20:df:5b:c6:2a:8a:ab:2e:bd:
//                     70:ad:c5:0b:1a:25:90:74:72:c5:7b:6a:ab:34:d6:
//                     30:89:ff:e5:68:13:7b:54:0b:c8:d6:ae:ec:5a:9c:
//                     92:1e:3d:64:b3:8c:c6:df:bf:c9:41:70:ec:16:72:
//                     d5:26:ec:38:55:39:43:d0:fc:fd:18:5c:40:f1:97:
//                     eb:d5:9a:9b:8d:1d:ba:da:25:b9:c6:d8:df:c1:15:
//                     02:3a:ab:da:6e:f1:3e:2e:f5:5c:08:9c:3c:d6:83:
//                     69:e4:10:9b:19:2a:b6:29:57:e3:e5:3d:9b:9f:f0:
//                     02:5d
//                 Exponent: 65537 (0x10001)
//         X509v3 extensions:
//             X509v3 Basic Constraints: critical
//                 CA:TRUE
//             X509v3 Key Usage: critical
//                 Certificate Sign, CRL Sign
//             X509v3 Subject Key Identifier: 
//                 C4:A7:B1:A4:7B:2C:71:FA:DB:E1:4B:90:75:FF:C4:15:60:85:89:10
//     Signature Algorithm: sha1WithRSAEncryption
//          a3:1a:2c:9b:17:00:5c:a9:1e:ee:28:66:37:3a:bf:83:c7:3f:
//          4b:c3:09:a0:95:20:5d:e3:d9:59:44:d2:3e:0d:3e:bd:8a:4b:
//          a0:74:1f:ce:10:82:9c:74:1a:1d:7e:98:1a:dd:cb:13:4b:b3:
//          20:44:e4:91:e9:cc:fc:7d:a5:db:6a:e5:fe:e6:fd:e0:4e:dd:
//          b7:00:3a:b5:70:49:af:f2:e5:eb:02:f1:d1:02:8b:19:cb:94:
//          3a:5e:48:c4:18:1e:58:19:5f:1e:02:5a:f0:0c:f1:b1:ad:a9:
//          dc:59:86:8b:6e:e9:91:f5:86:ca:fa:b9:66:33:aa:59:5b:ce:
//          e2:a7:16:73:47:cb:2b:cc:99:b0:37:48:cf:e3:56:4b:f5:cf:
//          0f:0c:72:32:87:c6:f0:44:bb:53:72:6d:43:f5:26:48:9a:52:
//          67:b7:58:ab:fe:67:76:71:78:db:0d:a2:56:14:13:39:24:31:
//          85:a2:a8:02:5a:30:47:e1:dd:50:07:bc:02:09:90:00:eb:64:
//          63:60:9b:16:bc:88:c9:12:e6:d2:7d:91:8b:f9:3d:32:8d:65:
//          b4:e9:7c:b1:57:76:ea:c5:b6:28:39:bf:15:65:1c:c8:f6:77:
//          96:6a:0a:8d:77:0b:d8:91:0b:04:8e:07:db:29:b6:0a:ee:9d:
//          82:35:35:10
        "-----BEGIN CERTIFICATE-----\n" .
        "MIIDSjCCAjKgAwIBAgIQRK+wgNajJ7qJMDmGLvhAazANBgkqhkiG9w0BAQUFADA/\n" .
        "MSQwIgYDVQQKExtEaWdpdGFsIFNpZ25hdHVyZSBUcnVzdCBDby4xFzAVBgNVBAMT\n" .
        "DkRTVCBSb290IENBIFgzMB4XDTAwMDkzMDIxMTIxOVoXDTIxMDkzMDE0MDExNVow\n" .
        "PzEkMCIGA1UEChMbRGlnaXRhbCBTaWduYXR1cmUgVHJ1c3QgQ28uMRcwFQYDVQQD\n" .
        "Ew5EU1QgUm9vdCBDQSBYMzCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEB\n" .
        "AN+v6ZdQCINXtMxiZfaQguzH0yxrMMpb7NnDfcdAwRgUi+DoM3ZJKuM/IUmTrE4O\n" .
        "rz5Iy2Xu/NMhD2XSKtkyj4zl93ewEnu1lcCJo6m67XMuegwGMoOifooUMM0RoOEq\n" .
        "OLl5CjH9UL2AZd+3UWODyOKIYepLYYHsUmu5ouJLGiifSKOeDNoJjj4XLh7dIN9b\n" .
        "xiqKqy69cK3FCxolkHRyxXtqqzTWMIn/5WgTe1QLyNau7Fqckh49ZLOMxt+/yUFw\n" .
        "7BZy1SbsOFU5Q9D8/RhcQPGX69Wam40dutolucbY38EVAjqr2m7xPi71XAicPNaD\n" .
        "aeQQmxkqtilX4+U9m5/wAl0CAwEAAaNCMEAwDwYDVR0TAQH/BAUwAwEB/zAOBgNV\n" .
        "HQ8BAf8EBAMCAQYwHQYDVR0OBBYEFMSnsaR7LHH62+FLkHX/xBVghYkQMA0GCSqG\n" .
        "SIb3DQEBBQUAA4IBAQCjGiybFwBcqR7uKGY3Or+Dxz9LwwmglSBd49lZRNI+DT69\n" .
        "ikugdB/OEIKcdBodfpga3csTS7MgROSR6cz8faXbauX+5v3gTt23ADq1cEmv8uXr\n" .
        "AvHRAosZy5Q6XkjEGB5YGV8eAlrwDPGxrancWYaLbumR9YbK+rlmM6pZW87ipxZz\n" .
        "R8srzJmwN0jP41ZL9c8PDHIyh8bwRLtTcm1D9SZImlJnt1ir/md2cXjbDaJWFBM5\n" .
        "JDGFoqgCWjBH4d1QB7wCCZAA62RjYJsWvIjJEubSfZGL+T0yjWW06XyxV3bqxbYo\n" .
        "Ob8VZRzI9neWagqNdwvYkQsEjgfbKbYK7p2CNTUQ\n" .
        "-----END CERTIFICATE-----"
); // end            

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
