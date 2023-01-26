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
        
        $query = "SELECT * FROM users WHERE email = '$email'";
        $query_run = mysqli_query($conn, $query);
        
        $user_data = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
        
        if ( mysqli_num_rows($query_run) === 0 ) {
            send_error("Invalid email address.", null, 403);
        }
        
        if ( !password_verify($password, $user_data["password"]) ) {
            send_error("Invalid email address.", null, 403);
        }

        $user_id = $user_data["id"];
        $query = "SELECT * FROM sessions WHERE user_id = $user_id";
        $query_run = mysqli_query($conn, $query);

        if ( mysqli_affected_rows($conn) > 0 ) {
            $user_session = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
            if ( time() > $user_session["access_token_expiry"] ) {
                $query = "DELETE FROM sessions WHERE user_id = $user_id";
                $query_run = mysqli_query($conn, $query);
            }
            else {
                $return_data = [
                    "id" => $user_id,
                    "access_token" => $user_session["access_token"],
                    "access_token_expiry" => $user_session["access_token_expiry"],
                ];
        
                send("You are already logged in.", $return_data, 200);
            }
        }

        $access_token = base64_encode(hash("sha256", $user_data["email"], true));
        $access_token_expiry = time() + 3600;

        $query = "INSERT INTO sessions
            (access_token, access_token_expiry, user_id)
            VALUES ('$access_token', $access_token_expiry, $user_id)";
        $query_run = mysqli_query($conn, $query);
        
        if ( mysqli_affected_rows($conn) === 0 ) {
            send_error("Database problem", mysqli_error($conn), 500);
        }
        
        $return_data = [
            "id" => $user_id,
            "access_token" => $access_token,
            "access_token_expiry" => $access_token_expiry,
        ];

        send("Logged In", $return_data, 201);

    } catch (Exception $err) {
        send_error("Database problem", mysqli_error($conn), 500);
    }
}
else {
    send_error("Method not allowed.", null, 405);
}