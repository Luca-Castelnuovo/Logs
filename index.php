<?php

require $_SERVER['DOCUMENT_ROOT'] . '/includes/init.php';

if (isset($_GET['authenticate'])) {
    header('Location: ' . $provider->getAuthorizationUrl(['basic:read']));
    exit;
}

if (isset($_GET['code'])) {
    if (!$provider->checkState($_GET['state'])) {
        redirect('/?reset', 'Invalid State');
    } else {
        try {
            $access_token = $provider->getAccessToken('authorization_code', [
                'code' => $_GET['code']
            ]);

            if ($access_token->hasExpired()) {
                redirect('/?reset', 'Access Token invalid');
            }

            $allowed_users = json_decode(file_get_contents($GLOBALS['config']->oauth->allowed_users));
            if (!in_array($user['username'], $allowed_users)) {
                redirect('/?reset', 'Access Denied');
            }

            $_SESSION['logged_in'] = true;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['access_token'] = $access_token->getToken();

            redirect('/home', 'You are logged in');
        } catch (Exception $e) {
            redirect('/?reset', $e->getMessage());
        }
    }
}

if (isset($_GET['logout'])) {
    alert_set('You are logged out.');
    reset_session();
}

if (isset($_GET['reset'])) {
    reset_session();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    redirect('/home');
}
