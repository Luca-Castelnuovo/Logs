<?php

require '/var/www/logs.lucacastelnuovo.nl/public_html/includes/init.php';

function log_action($service_id, $action, $ip = null, $user_id = null, $client_id = null, $additional = null)
{
    $service_id = clean_data($service_id);
    $action = clean_data($action);
    $ip = clean_data($ip);
    $user_id = clean_data($user_id);
    $client_id = clean_data($client_id);
    $additional = clean_data($additional);
    $date = date("Y-m-d H:i:s");

    sql_insert('logs', [
        'services_id' => $service_id,
        'action' => $action,
        'date' => $date,
        'ip' => $ip,
        'user_id' => $user_id,
        'client_id' => $client_id,
        'additional' => $additional,
    ]);
}
