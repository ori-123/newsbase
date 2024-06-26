<?php

global $pdo;

use includes\Logger;

// Include necessary files and start session
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

session_start();

// Check if user is logged in
if (!check_login()) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User needs to log in to continue"]);
    Logger::backend_error('401, User needs to log in to continue');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    try {
        $user_id = $_SESSION['user_id'];

        // Retrieve the JSON data from the request body
        $json_data = file_get_contents('php://input');
        $put_data = json_decode($json_data, true);

        // Check if JSON data was successfully decoded
        if ($put_data === null) {
            http_response_code(400); // Bad request
            echo json_encode(["error" => "Invalid JSON data"]);
            exit();
        }

        // Get input data from decoded JSON data
        $current_password = $put_data['current_password'];
        $new_username = isset($put_data['new_username']) ? $put_data['new_username'] : null;
        $new_password = isset($put_data['new_password']) ? $put_data['new_password'] : null;

        // Retrieve the hashed password associated with the user from the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "User not found"]);
            Logger::backend_error("User not found");
            exit();
        }

        $hashed_password_from_db = $user['password'];

        // Compare the current password provided by the user with the hashed password from the database
        if (password_verify($current_password, $hashed_password_from_db)) {
            // Passwords match
            // Update user information

            // Check if new username is provided
            if ($new_username) {
                $stmt = $pdo->prepare("UPDATE users SET username = :new_username WHERE id = :user_id");
                $stmt->bindParam(':new_username', $new_username, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Check if new password is provided
            if ($new_password) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
                $stmt->bindParam(':new_password', $hashed_new_password, PDO::PARAM_STR);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            http_response_code(200);
            echo json_encode(['message' => 'User information updated successfully.']);
            Logger::backend_info("User information updated successfully");
        } else {
            // Incorrect password
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Incorrect password"]);
            Logger::backend_error("401, Incorrect password");
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to update user information: " . $e->getMessage()]);
        Logger::backend_error("500, Failed to update user information: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only PUT method is allowed"]);
    Logger::backend_error("405, Only PUT method is allowed");
}
