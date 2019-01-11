<?php

#################
# Validate data #
#################

// Clean data
function clean_data($data)
{
    $conn = sql_connect();
    $data = $conn->escape_string($data);
    sql_disconnect($conn);

    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}


########
# CSRF #
########

// Generate random string
function gen($length)
{
    $length = $length / 2;
    return bin2hex(random_bytes($length));
}


// Set CSRF
function csrf_gen()
{
    if (isset($_SESSION['CSRFtoken'])) {
        return $_SESSION['CSRFtoken'];
    } else {
        $_SESSION['CSRFtoken'] = gen(32);
        return $_SESSION['CSRFtoken'];
    }
}


// Validate CSRF
function csrf_val($CSRFtoken, $redirect = '/')
{
    if (!isset($_SESSION['CSRFtoken'])) {
        redirect($redirect, 'CSRF Error');
    }

    if (!(hash_equals($_SESSION['CSRFtoken'], $CSRFtoken))) {
        redirect($redirect, 'CSRF Error');
    } else {
        unset($_SESSION['CSRFtoken']);
    }
}


###########
# Session #
###########

function loggedin()
{
    if ((!$_SESSION['logged_in']) || ($_SESSION['ip'] != $_SERVER['REMOTE_ADDR']) || (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > 1800))) {
        redirect('/?reset', 'Please login');
    } else {
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}


function reset_session()
{
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
    }

    session_destroy();
    session_start();

    redirect('/', $alert);
}
