<?php

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

global $pdo;

session_start();
check_login();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $title = sanitize_input($_POST["title"]);
    $url = sanitize_input($_POST["url"]);
    $description = sanitize_input($_POST["description"]);
    $image_url = sanitize_input($_POST["image_url"]);
    $user_id = sanitize_input($_SESSION['user_id']);



    try {
        $pdo->beginTransaction();

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
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to save article: " . $e->getMessage()]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
}