<?php

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

global $pdo;

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = sanitize_input($_POST["title"]);
    $url = sanitize_input($_POST["url"]);
    $description = sanitize_input($_POST["description"]);
    $image_url = sanitize_input($_POST["image_url"]);

    if (!isset($_SESSION['user_id'])) {
        $_SESSION = array();
        session_destroy();
        header("Location: /login.php");
        exit();
    }

    $user_id = sanitize_input($_SESSION['user_id']);

    $pdo->beginTransaction();

    try {
        $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, url, description, image_url) 
                                        VALUES (:user_id, :title, :url, :description, :image_url)");
        $stmt->execute(['user_id' => $user_id, 'title' => $title, 'url' => $url, 'description' => $description, 'image_url' => $image_url]);

        $pdo->commit();

        http_response_code(201); // Created
        echo json_encode(["message" => "Article saved successfully"]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to save article."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
}