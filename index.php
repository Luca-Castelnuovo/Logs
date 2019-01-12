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

            $user = $provider->authenticatedRequest(
                'GET',
                'https://api.lucacastelnuovo.nl/user/',
                $access_token->getToken()
            );

            $allowed_users = json_decode(file_get_contents($GLOBALS['config']->oauth->allowed_users));
            if (!in_array($user['username'], $allowed_users)) {
                redirect('/?reset', 'Access Denied');
            }

            $_SESSION['logged_in'] = true;
            $_SESSION['ip'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['id'] = $user['id'];

            log_action('5', 'auth.login', $_SERVER["REMOTE_ADDR"], $user['id']);
            redirect('/home', 'You are logged in');
        } catch (Exception $e) {
            redirect('/?reset', $e->getMessage());
        }
    }
}

if (isset($_GET['logout'])) {
    if ($_SESSION['logged_in']) {
        log_action('5', 'auth.logout', $_SERVER["REMOTE_ADDR"], $_SESSION['id']);
    }

    alert_set('You are logged out');
    reset_session();
}

if (isset($_GET['reset'])) {
    if ($_SESSION['logged_in']) {
        log_action('5', 'auth.reset', $_SERVER["REMOTE_ADDR"], $_SESSION['id']);
    }

    reset_session();
}

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in']) {
    redirect('/home');
}

?>
<!DOCTYPE html>
<html>

<head>
    <!-- Config -->
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <link rel="manifest" href="/site.webmanifest"></link>
    <title>Login || Logs</title>

    <!-- SEO -->
    <link href="https://logs.lucacastelnuovo.nl" rel="canonical">
    <meta content="A system to store and access all my logs" name="description">

    <!-- Icons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">

    <!-- Styles -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.lucacastelnuovo.nl/general/css/materialize.css">
</head>

<body>
<div class="row">
    <div class="col s12 m8 offset-m2 l4 offset-l4">
        <div class="card">
            <div class="card-action blue accent-4 white-text">
                <h3>Logs</h3>
            </div>
            <div class="card-content">
                <div class="row center">
                    <a class="waves-effect waves-light btn-large blue accent-4" href="?authenticate">
                        Login with LTC
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.lucacastelnuovo.nl/general/js/materialize.js"></script>
    <?= alert_display() ?>
</body>

</html>
