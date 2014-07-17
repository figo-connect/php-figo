<?php
require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/../figo/Connection.php';
require_once __DIR__.'/../figo/Session.php';

$app = new Silex\Application();
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array('twig.path' => __DIR__.'/views'));

$CLIENT_ID = "CaESKmC8MAhNpDe5rvmWnSkRE_7pkkVIIgMwclgzGcQY";
$CLIENT_SECRET = "STdzfv0GXtEj_bwYn7AgCVszN1kKq5BdgEIKOM_fzybQ";
$connection = new Figo\Connection($CLIENT_ID, $CLIENT_SECRET, "http://localhost:3000/callback");

$app->get('/', function() use($app, $connection) {
    # check whether the user is logged in
    if (!$app['session']->has('figo_token')) {
        return $app->redirect($connection->login_url("qweqwe", "accounts=ro transactions=ro balance=ro user=ro"));
    }

    $session = new Figo\Session($app['session']->get('figo_token'));
    return $app['twig']->render('index.twig', array('transactions' => $session->get_transactions(),
                                                    'accounts' => $session->get_accounts(),
                                                    'current_account' => null,
                                                    'user' => $session->get_user()));
});


$app->get('/callback', function() use($app, $connection) {
    # authenticate the call
    if($app['request']->query->get('state') != "qweqwe") {
        throw new Exception("Bogus redirect, wrong state");
    }

    # trade in authentication code for access token
    $token_dict = $connection->obtain_access_token($app['request']->query->get("code"));

    # store the access token in our session
    $app['session']->set('figo_token', $token_dict['access_token']);

    return $app->redirect("/");
});

$app->get('/logout', function() use($app) {
    $app['session']->remove('figo_token');
    return $app->redirect('/');
});


$app->get('/{account_id}', function($account_id) use($app, $connection){
    # check whether the user is logged in
    if (!$app['session']->has('figo_token')) {
        return $app->redirect($connection->login_url("qweqwe", "accounts=ro transactions=ro balance=ro user=ro"));
    }

    $session = new Figo\Session($app['session']->get('figo_token'));
    return $app['twig']->render('index.twig', array('transactions' => $session->get_transactions($account_id),
                                                    'accounts' => $session->get_accounts(),
                                                    'current_account' => $session->get_account($account_id),
                                                    'user' => $session->get_user()));
});

$app->run();

?>
