<?php

use includes\Logger;

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';
require_once '../../vendor/autoload.php';

global $pdo;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Get username and password from POST data
    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);

    // Validate that username and password are present
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Username and password are required"]);
        Logger::error("400, Username and password are required");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Hash password given in POST data and use it to create new user in database
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->bindParam('username', $username, PDO::PARAM_STR);
        $stmt->bindParam('password', $hashed_password, PDO::PARAM_STR);
        $stmt->execute();

        $pdo->commit();

        http_response_code(201); // Created
        echo json_encode(["message" => "User registered successfully"]);
        Logger::info("User registered successfully");
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to register user: " . $e->getMessage()]);
        Logger::error("500, Failed to register user: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    Logger::error("405, Only POST method is allowed");
}
