<?php

$GLOBALS['log_config'] = require '/var/www/logs.lucacastelnuovo.nl/public_html/includes/config.php';

function log_sql_connect()
{
    $conn = new mysqli($GLOBALS['log_config']->database->host, $GLOBALS['log_config']->database->user, $GLOBALS['log_config']->database->password, $GLOBALS['log_config']->database->database);

    if ($conn->connect_error) {
        exit('Server error');
    } else {
        return $conn;
    }
}


//Close database connection
function log_sql_disconnect($conn)
{
    mysqli_close($conn);
}


//Execute sql query's
function log_sql_query($query, $assoc)
{
    $conn = log_sql_connect();

    $result = $conn->query($query);

    log_sql_disconnect($conn);

    if ($assoc) {
        return $result->fetch_assoc();
    } else {
        return $result;
    }
}


#################
# Fast funtions #
#################

// Select
function log_sql_select($table, $select, $where, $assoc = false) // sql_select('users', 'first_name,last_name', "user_id='1'", true)
{
    // Build query
    $where = ' WHERE ' . $where;
    $query = 'SELECT ' . $select . ' FROM ' . $table . ' ' . $where;

    // Execute query and return response
    return log_sql_query($query, $assoc);
}

// Insert
function log_sql_insert($table, $insert) // sql_insert('users', ['first_name' => 'piet'])
{
    $fields = array_keys($insert);

    // Build query
    $query = 'INSERT INTO ' . $table . " (" . implode(",", $fields) . ") VALUES('" . implode("','", $insert) . "')";

    // Execute query
    log_sql_query($query, false);
}


function log_clean_data($data)
{
    $conn = sql_connect();
    $data = $conn->escape_string($data);
    sql_disconnect($conn);

    $data = trim($data);
    $data = htmlspecialchars($data);
    $data = stripslashes($data);

    return $data;
}

function log_action($service_id, $action, $ip = null, $user_id = null, $client_id = null, $additional = null)
{
    $service_id = log_clean_data($service_id);
    $action = log_clean_data($action);
    $ip = log_clean_data($ip);
    $user_id = log_clean_data($user_id);
    $client_id = log_clean_data($client_id);
    $additional = log_clean_data($additional);
    $date = date("Y-m-d H:i:s");

    log_sql_insert('logs', [
        'services_id' => $service_id,
        'action' => $action,
        'date' => $date,
        'ip' => $ip,
        'user_id' => $user_id,
        'client_id' => $client_id,
        'additional' => $additional,
    ]);
}

/*

    require '/var/www/logs.lucacastelnuovo.nl/public_html/logs.php';

    log_action('SERVICE_ID', 'auth.login', $_SERVER["REMOTE_ADDR"], 'USER_ID', 'CLIENT_ID');

*/
