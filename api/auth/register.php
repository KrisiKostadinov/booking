<?php

include_once("../functions.php");
include_once("../dbconn.php");

if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
    
    $input = json_decode(file_get_contents("php://input"));
    $email = $input->email;
    $password = $input->password;

    if ( empty($email) || empty($password) ) {
        send_error("Email and password are required.", null, 400);
    }
    
    if ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        send_error("Invalid email address.", null, 400);
    }
    
    try {
        $query = "SELECT id FROM users WHERE email '$email'";
        $query_run = mysqli_query($conn, $query);
        
        if ( mysqli_affected_rows($conn) > 0 ) {
            send_error("This email address already exists.", null, 409);
        }
        
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        
        $query = "INSERT INTO users (email, password) VALUES ('$email', '$password_hash')";
        $query_run = mysqli_query($conn, $query);
        
        if ( mysqli_affected_rows($conn) === 0 ) {
            send_error("Database problem", mysqli_error($conn), 500);
        }
        
        $query = "SELECT id, email FROM users WHERE email = '$email'";
        $query_run = mysqli_query($conn, $query);

        $user_data = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
        send("User Created", $user_data, 201);
    } catch (Exception $err) {
        send_error("Database problem", mysqli_error($conn), 500);
    }
}
else {
    send_error("Method not allowed.", null, 405);
}