<?php

use includes\Logger;

global $pdo;
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

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        //Get user id from session and article id from request
        $user_id = $_SESSION['user_id'];
        $article_id = $_GET['id'];

        $stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = :user_id AND id = :article_id");
        $stmt->bindValue('user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindValue('article_id', $article_id, PDO::PARAM_INT);
        $stmt->execute();

        $article = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($article) {
            http_response_code(200); // OK
            echo json_encode($article);
            Logger::backend_info("Article received successfully.");
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "Article not found."]);
            Logger::backend_error("404, Article not found.");
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to retrieve article from the database: " . $e->getMessage()]);
        Logger::backend_error("500 Failed to retrieve article from the database: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only GET method is allowed"]);
    Logger::backend_error("405, Only GET method is allowed");
}