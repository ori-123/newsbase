<?php

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

use includes\Logger;

global $pdo;

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve the JSON data from the request body
    $json_data = file_get_contents('php://input');
    $data = json_decode($json_data, true);

    // Check if JSON data was successfully decoded
    if ($data === null) {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }

    // Get username and password from decoded JSON data
    $username = sanitize_input($data["username"]);
    $password = sanitize_input($data["password"]);

    // Validate that username and password are present
    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Username and password are required"]);
        Logger::error("400, Username and password are required");
        exit();
    }

    try {
        $pdo->beginTransaction();

        // Find user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // If user is found, verify password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            session_regenerate_id(true);

            $pdo->commit();

            http_response_code(200); // OK
            echo json_encode(["message" => "User authenticated successfully"]);
            Logger::info("User authenticated successfully");
        } else {
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Invalid username or password"]);
            Logger::error("401, Invalid username or password");
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to authenticate user: " . $e->getMessage()]);
        Logger::error("500, Failed to authenticate user: " . $e->getMessage());
    }
} else {
    http_response_code(405); // METHOD not allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    Logger::error("405, Only POST method is allowed");
}