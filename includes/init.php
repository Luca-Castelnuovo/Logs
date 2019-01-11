<?php

session_start();


$GLOBALS['config'] = require $_SERVER['DOCUMENT_ROOT'] . '/includes/config.php';

require $_SERVER['DOCUMENT_ROOT'] . '/includes/oauth.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/security.php';
require $_SERVER['DOCUMENT_ROOT'] . '/includes/sql.php';


$provider = new OAuth([
    'clientID'                => $GLOBALS['config']->oauth->client_id,
    'clientSecret'            => $GLOBALS['config']->oauth->client_secret,
    'redirectUri'             => 'https://logs.lucacastelnuovo.nl/',
    'urlAuthorize'            => 'https://accounts.lucacastelnuovo.nl/auth/authorize',
    'urlAccessToken'          => 'https://accounts.lucacastelnuovo.nl/auth/token',
]);


// API response
function response($success, $status_code = 200, $message = null, $extra = null)
{
    $output = ["success" => $success];

    if (isset($message) && !empty($message)) {
        if ($success) {
            $output = array_merge($output, ["message" => $message]);
        } else {
            $output = array_merge($output, ["error" => $message]);
        }
    }

    if (!empty($extra)) {
        $output = array_merge($output, $extra);
    }

    header('Content-Type: application/json');
    http_response_code($status_code);

    echo json_encode($output);
    exit;
}


//Redirect user
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
    if (isset($_SESSION)) {
        $_SESSION['alert'] = $alert;
    } else {
        response(false, 'Session not started.');
    }
}


function alert_display()
{
    if (isset($_SESSION)) {
        if (isset($_SESSION['alert']) && !empty($_SESSION['alert'])) {
            echo "<script>M.toast({html: \"{$_SESSION['alert']}\"});</script>";
            unset($_SESSION['alert']);
        }
    }
}
