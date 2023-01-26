<?php

include_once("../functions.php");
include_once("../dbconn.php");

if ( $_SERVER["REQUEST_METHOD"] === "POST" ) {
    $input = json_decode(file_get_contents("php://input"));
    
    $id = mysqli_real_escape_string($conn, $input->id);
    $name = mysqli_real_escape_string($conn, $input->name);
    $title = mysqli_real_escape_string($conn, $input->title);
    $type = mysqli_real_escape_string($conn, $input->type);
    $city = mysqli_real_escape_string($conn, $input->city);
    $address = mysqli_real_escape_string($conn, $input->address);
    $distance = $input->distance;
    $description = mysqli_real_escape_string($conn, $input->description);
    $cheapest_price = $input->cheapest_price;
    $rating = $input->rating;
    $featured = $input->featured;
    
    if ( empty($name) || empty($type) || empty($title) || empty($city) || empty($address) || empty($distance) || empty($description) || empty($cheapest_price) ) {
        send_error("All fields with * are required.", null, 400);
    }
        
    try {
        
        if ( empty($id) ) {
            $query = "INSERT INTO
                hotels (name, type, title, city, address, distance, description, cheapest_price, featured)
                VALUES ('$name', '$type', '$title', '$city',
                '$address', $distance, '$description',
                $cheapest_price, $featured)";
            
            $query_run = mysqli_query($conn, $query);

            if ( mysqli_affected_rows($conn) === -1 ) {
                send_error("Database problem", mysqli_error($conn), 500);
            }
        
            $return_data = $input;
            $return_data->id = mysqli_insert_id($conn);
            
            send("Created Successfully!", $return_data, 201);
        }
        else {
            $query = "SELECT id FROM hotels WHERE id = $id";
            $query_run = mysqli_query($conn, $query);

            if ( mysqli_num_rows($query_run) === 0 ) {
                send_error("Invalid hotel id.", null, 500);
            }

            $query = "UPDATE hotels SET
                name='$name',
                title='$title',
                type='$type',
                city='$city',
                distance=$distance,
                description='$description',
                rating=$rating,
                cheapest_price=$cheapest_price,
                featured=$featured WHERE id = $id";
            
            $query_run = mysqli_query($conn, $query);

            if ( mysqli_affected_rows($conn) === -1 ) {
                send_error("Database problem", mysqli_error($conn), 500);
            }

            $query = "SELECT * FROM hotels WHERE id = $id";
            $query_run = mysqli_query($conn, $query);

            $hotel_data = mysqli_fetch_array($query_run, MYSQLI_ASSOC);
            send("Updated Successfully!", $hotel_data, 200);
        }
    } catch (Exception $err) {
        send_error("Database problem", $err->getMessage(), 500);
    }
}
else {
    send_error("Method not allowed.", null, 405);
}