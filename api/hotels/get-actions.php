<?php

include_once("../functions.php");
include_once("../dbconn.php");

if ( $_SERVER["REQUEST_METHOD"] === "DELETE" ) {

    $id = $_GET["id"];

    if ( empty($id) ) {
        send_error("Invalid hotel id.", null, 400);
    }

    $query = "SELECT id FROM hotels WHERE id = $id";
    $query_run = mysqli_query($conn, $query);

    if ( mysqli_affected_rows($conn) === 0 ) {
        send_error("Invalid hotel id.", null, 400);
    }

    $query = "DELETE FROM hotels WHERE id = $id";
    $query_run = mysqli_query($conn, $query);

    if ( mysqli_affected_rows($conn) === 0 ) {
        send_error("Database problem", mysqli_error($conn), 500);
    }

    send("Deleted Successfully!");
}
else if ( $_SERVER["REQUEST_METHOD"] === "GET" ) {
    
    $id = $_GET["id"];

    if ( empty($id) ) {
        send_error("Invalid hotel id.", null, 400);
    }

    $query = "SELECT * FROM hotels WHERE id = $id";
    $query_run = mysqli_query($conn, $query);

    if ( mysqli_affected_rows($conn) === 0 ) {
        send_error("Invalid hotel id.", null, 400);
    }

    $hotel_data = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
    send("", $hotel_data);
}
else {
    send_error("Method not allowed.", null, 405);
}