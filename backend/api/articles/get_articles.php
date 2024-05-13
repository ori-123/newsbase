<?php

use includes\Logger;

global $pdo;
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

session_start();
check_login();

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {
        $user_id = $_SESSION['user_id'];

        $stmt = $pdo->prepare("SELECT * FROM articles WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();

        $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($articles) {
            http_response_code(200); // OK
            echo json_encode($articles);
            Logger::info("Articles received successfully.");
        } else {
            http_response_code(404); // Not Found
            echo json_encode(["error" => "No articles found for the user."]);
            Logger::error("404, No articles found for the user.");
        }
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to retrieve articles from the database: " . $e->getMessage()]);
        Logger::error("500 Failed to retrieve articles from the database: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only GET method is allowed"]);
    Logger::error("405, Only GET method is allowed");
}
