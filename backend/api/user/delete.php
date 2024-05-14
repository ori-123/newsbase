<?php

use includes\Logger;

global $pdo;
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

session_start();
check_login(); // Check if user is logged in, reroute to login page on failure.

if ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    try {
        // Get user id from SESSION
        $user_id = sanitize_input($_SESSION['user_id']);

        // Delete user from database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        // Destroy session (log out user) and reroute to login page
        $_SESSION = array();
        session_destroy();

        http_response_code(200);
        echo json_encode(['message' => 'User removed successfully.']);
        Logger::info('User removed successfully');
        header("Location: /login");
        exit();
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to delete user: " . $e->getMessage()]);
        Logger::error("500, Failed to delete user: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only DELETE method is allowed"]);
    Logger::error("405, Only DELETE method is allowed");
}
