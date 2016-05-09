<?php
/**
 * Created by PhpStorm.
 * User: D. Manavi
 */

function db_execute($query) {
    // get database connection
    $connection = db_get_connection();

    // get result
    mysqli_query($connection, $query);

    // return number of affected rows
    return mysqli_affected_rows($connection);
}

function db_select($query) {
    // get database connection
    $connection = db_get_connection();

    // execute query and return to the caller
    return mysqli_query($connection, $query);
}

function db_select_scalar($query) {

    // get database connection
    $connection = db_get_connection();

    // get result
    $result = mysqli_query($connection, $query);

    // fetch first row
    $output = $result->fetch_row();

    // close database connection
    db_close_database($connection);

    // return first cell of the row
    return $output[0];
}

function db_get_connection() {

    // try to connect to the database
    $connection = mysqli_connect('localhost', 'root', '', 'Taskboard');

    // if connection failed, throw error
    if (!$connection) { die('Could not connect: '.mysqli_error($connection));}

    /* change character set to utf8 */
    if (!mysqli_set_charset($connection, "utf8")) {

        die('Error setting character set utf8: '.mysqli_error($connection));
    }

    // return created connection
    return $connection;
}

function db_close_database($connection) {

    mysqli_close($connection);
}