<?php
require_once "vendor/autoload.php";

use figo\Session;

$session = new Session("ASHWLIkouP2O6_bgA2wWReRhletgWKHYjLqDaqb0LFfamim9RjexTo22ujRIP_cjLiRiSyQXyt2kM1eXU2XLFZQ0Hro15HikJQT_eNeT_9XQ");

// Print out list of account numbers and balances.
$accounts = $session->get_accounts();
foreach ($accounts as $account) {
    print($account->account_number."\n");
    print($account->balance->balance."\n");
}

// Print out the list of all transaction originators/recipients of a specific account.
$account = $session->get_account("A1.1");
$transactions = $account->get_transactions();
foreach ($transactions as $transaction) {
    print($transaction->name."\n");
}

?>
