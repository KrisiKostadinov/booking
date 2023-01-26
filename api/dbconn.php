<?php

$host = "localhost";
$user = "root";
$password = "root";
$dbname = "booking";

$conn = mysqli_connect($host, $user, $password, $dbname);

if ( mysqli_connect_error() ) {
    die("Database error: " . mysqli_connect_error());
}