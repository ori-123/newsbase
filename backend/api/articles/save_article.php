<?php

use includes\Logger;

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

global $pdo;

session_start();
check_login(); // Check if user is logged in, reroute to login page on failure.

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get article details from POST data and user id from SESSION
    $title = sanitize_input($_POST["title"]);
    $url = sanitize_input($_POST["url"]);
    $description = sanitize_input($_POST["description"]);
    $image_url = sanitize_input($_POST["image_url"]);
    $user_id = sanitize_input($_SESSION['user_id']);



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
        Logger::info("Article created successfully");
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to save article: " . $e->getMessage()]);
        Logger::error("500, Failed to save article: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
    Logger::error("405, Only POST method is allowed");
}