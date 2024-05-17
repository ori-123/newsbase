<?php

use includes\Logger;

// Include necessary files and start session
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

global $pdo;

session_start();

// Check if user is logged in
if (!check_login()) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User needs to log in to continue"]);
    Logger::backend_error('401, User needs to log in to continue');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get article details from JSON data and user id from SESSION
    $json_data = file_get_contents('php://input');
    $article_data = json_decode($json_data, true);

    // Check if JSON data was successfully decoded
    if ($article_data === null) {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Invalid JSON data"]);
        exit();
    }

    $title = sanitize_input($article_data["title"]);
    $url = sanitize_input($article_data["url"]);
    $description = sanitize_input($article_data["description"]);
    $image_url = sanitize_input($article_data["image_url"]);
    $user_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Insert new article into database
        $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, url, description, image_url) 
                                        VALUES (:user_id, :title, :url, :description, :image_url)");

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $title, PDO::PARAM_STR);
        $stmt->bindValue(':url', $url, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);
        $stmt->bindValue(':image_url', $image_url, PDO::PARAM_STR);
        $stmt->execute();

        $pdo->commit();

        http_response_code(201); // Created
        echo json_encode(["message" => "Article saved successfully"]);
        Logger::backend_info("Article saved successfully");
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to save article: " . $e->getMessage()]);
        Logger::backend_error("500, Failed to save article: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    Logger::backend_error("405, Only POST method is allowed");
}
