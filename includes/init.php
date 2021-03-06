<?php

session_start();

require_once __DIR__ . '/../vendor/autoload.php';

$GLOBALS['config'] = require $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/oauth.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/sql.php';

// External
require '/var/www/logs.lucacastelnuovo.nl/logs.php';


$provider = new OAuth([
    'clientID'                => $GLOBALS['config']->oauth->client_id,
    'clientSecret'            => $GLOBALS['config']->oauth->client_secret,
    'redirectUri'             => 'https://logs.lucacastelnuovo.nl/',
    'urlAuthorize'            => 'https://accounts.lucacastelnuovo.nl/auth/authorize',
    'urlAccessToken'          => 'https://accounts.lucacastelnuovo.nl/auth/token',
]);


function redirect($to, $alert = null)
{
    if (!empty($alert)) {
        alert_set($alert);
    }

    header('location: ' . $to);
    exit;
}


function alert_set($alert)
{
    $_SESSION['alert'] = $alert;
}


function alert_display()
{
    if (isset($_SESSION['alert']) && !empty($_SESSION['alert'])) {
        echo "<script>M.toast({html: \"{$_SESSION['alert']}\"});</script>";
        unset($_SESSION['alert']);
    }
}
