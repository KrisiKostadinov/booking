<?php

header('Content-Type: application/json;charset=utf-8');

function dd($value) {
    ?>
        <pre><?= var_dump($value) ?></pre>
    <?php
}

function send($message, $data = [], $code = 200) {
    http_response_code($code);
    $data = [
        "success" => true,
        "message" => $message,
        "data" => $data,
    ];

    echo json_encode($data);
    exit;
}

function send_error($message, $stack = null, $code = 404) {
    http_response_code($code);
    $data = [
        "success" => false,
        "message" => $message,
        "stack" => $stack,
    ];
    
    echo json_encode($data);
    exit;
}