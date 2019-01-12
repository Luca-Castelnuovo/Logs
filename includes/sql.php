<?php

//Connect to database
function sql_connect()
{
    $conn = new mysqli($GLOBALS['config']->database->host, $GLOBALS['config']->database->user, $GLOBALS['config']->database->password, $GLOBALS['config']->database->database);

    if ($conn->connect_error) {
        exit('Server error');
    } else {
        return $conn;
    }
}


//Close database connection
function sql_disconnect($conn)
{
    mysqli_close($conn);
}


//Execute sql query's
function sql_query($query, $assoc)
{
    $conn = sql_connect();

    $result = $conn->query($query);

    sql_disconnect($conn);

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
function sql_select($table, $select, $where, $assoc = false) // sql_select('users', 'first_name,last_name', "user_id='1'", true)
{
    // Build query
    $where = ' WHERE ' . $where;
    $query = 'SELECT ' . $select . ' FROM ' . $table . ' ' . $where;

    // Execute query and return response
    return sql_query($query, $assoc);
}

// Insert
function sql_insert($table, $insert) // sql_insert('users', ['first_name' => 'piet'])
{
    $fields = array_keys($insert);

    // Build query
    $query = 'INSERT INTO ' . $table . " (" . implode(",", $fields) . ") VALUES('" . implode("','", $insert) . "')";

    // Execute query
    sql_query($query, false);
}
