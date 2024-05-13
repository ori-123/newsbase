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
    $user_id = sanitize_input($_SESSION['user_id']);

    $stmt = $pdo->prepare("INSERT INTO articles (user_id, title, url, description, image_url) 
                                        VALUES (:user_id, :title, :url, :description, :image_url)");
    $stmt->execute(['title' => $title, 'url' => $url, 'description' => $description, 'image_url' => $image_url]);

    http_response_code(201); // Created
    echo json_encode(["message" => "Article saved successfully"]);
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
}