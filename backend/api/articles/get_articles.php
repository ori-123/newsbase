<?php
session_start();

use includes\Logger;

global $pdo;
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

// Check if user is logged in
if (!check_login()) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User needs to log in to continue"]);
    Logger::backend_error('401, User needs to log in to continue');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        //Get user id from session and use it to select all articles from the user
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200); // OK
        echo json_encode($articles);
        Logger::backend_info("Articles received successfully.");

    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to retrieve articles from the database: " . $e->getMessage()]);
        Logger::backend_error("500 Failed to retrieve articles from the database: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only GET method is allowed"]);
    Logger::backend_error("405, Only GET method is allowed");
}
