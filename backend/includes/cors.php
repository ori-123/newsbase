<?php

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config/');
$dotenv->load();

$request_origin = $_ENV['FRONTEND_DOMAIN'];

// Handle preflight OPTIONS request
if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") {
    header("Access-Control-Allow-Origin: $request_origin"); // Allow requests from request origin specified in ENV
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS"); // Allow the specified HTTP methods
    header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization"); // Allow the specified headers
    exit();
}

// Regular CORS headers for non-preflight requests
header("Access-Control-Allow-Origin: $request_origin"); // Allow requests from request origin specified in ENV
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE"); // Allow the specified HTTP methods
header("Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token, Authorization"); // Allow the specified headers
