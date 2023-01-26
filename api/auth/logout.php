<?php

include_once("../functions.php");
include_once("../dbconn.php");

if ( $_SERVER["REQUEST_METHOD"] === "DELETE" ) {

    $access_token = $_SERVER["HTTP_AUTHORIZATION"];

    $query = "SELECT * FROM sessions WHERE access_token = '$access_token'";
    $query_run = mysqli_query($conn, $query);

    if ( mysqli_num_rows($query_run) === 0 ) {
        $stack = mysqli_error($conn) ? mysqli_error($conn) : null;
        send_error("You are already logged out", $stack, 403);
    }

    $query = "DELETE FROM sessions WHERE access_token = '$access_token'";
    mysqli_query($conn, $query);

    send("Logged out");
}
else {
    send_error("Method not allowed.", null, 405);
}