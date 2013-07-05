php-figo [![Build Status](https://secure.travis-ci.org/figo-connect/php-figo.png)](https://travis-ci.org/figo-connect/php-figo)
========

PHP bindings for the figo Connect API: http://developer.figo.me

Usage
=====

First, you've to add this to your [`composer.json`](http://getcomposer.org/) dependencies:

```json
"require": {
  "figo/figo": "1.*"
}
```

and run

```bash
composer update
```

Now you can create a new session and access data:

```php
require_once "vendor/autoload.php";

use figo\Session;

$session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

// Print out list of account numbers and balances.
$accounts = $session->get_accounts();
foreach ($accounts as $account) {
    $balance = $account->get_balance();
    print($account->account_number."\n");
    print($balance->balance."\n");
}

// Print out the list of all transaction originators/recipients of a specific account.
$account = $session->get_account("A1.1");
$transactions = $account->get_transactions();
foreach ($transactions as $transaction) {
    print($transaction->name."\n");
}
```

It is just as simple to allow users to login through the API:

```php
require_once "vendor/autoload.php";

use figo\Session;
use figo\Connection;
use Eloquent\Liftoff\Launcher;

$connection = new Connection("<client ID>", "<client secret>", "http://my-domain.org/redirect-url");

function start_login() {
    $launcher = new Launcher;
    $launcher->launch($connection->login_url("qweqwe"));
}

function process_redirect($authorization_code, $state) {
    // Handle the redirect URL invocation from the initial start_login call.

    // Ignore bogus redirects.
    if ($state !== "qweqwe") {
        return;
    }

    // Trade in authorization code for access token.
    $token_dict = $connection->obtain_access_token($authorization_code);

    // Start session.
    $session = new Session($token_dict["access_token"]);
  
    // Print out list of account numbers.
    $accounts = $session->get_accounts();
    foreach ($accounts as $account) {
        print($account->account_number."\n");
    }
}
```

You can find more documentation in the `doc/` folder.
