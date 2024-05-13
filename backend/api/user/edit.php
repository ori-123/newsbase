<?php

use includes\Logger;

global $pdo;
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

session_start();
check_login();

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    try {
        $user_id = $_SESSION['user_id'];

        // Get input data from request
        parse_str(file_get_contents("php://input"), $put_data);
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
            Logger::error("User not found");
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
            Logger::info("User information updated successfully");
        } else {
            // Incorrect password
            http_response_code(401); // Unauthorized
            echo json_encode(["error" => "Incorrect password"]);
            Logger::error("401, Incorrect password");
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to update user information: " . $e->getMessage()]);
        Logger::error("500, Failed to update user information: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only PUT method is allowed"]);
    Logger::error("405, Only PUT method is allowed");
}
