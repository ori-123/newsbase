<?php

use includes\Logger;

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

global $pdo;

if ($_SERVER["REQUEST_METHOD"] === "DELETE" && isset($_GET["id"])) {
    // Get article id and user id from GET data and SESSION respectively
    $article_id = sanitize_input($_GET["id"]);
    $user_id = sanitize_input($_SESSION['user_id']);

    try {
        $pdo->beginTransaction();

        // Find article in database
        $get_stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :article_id AND user_id = :user_id");
        $get_stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);
        $get_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $get_stmt->execute();
        $article = $get_stmt->fetch(PDO::FETCH_ASSOC);

        // If article is found, delete
        if ($article) {
            $delete_stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
            $delete_stmt->bindParam(':id', $article_id, PDO::PARAM_INT);
            $delete_stmt->execute();

            $pdo->commit();

            http_response_code(200); // OK
            echo json_encode(["message" => "Article deleted successfully"]);
            Logger::backend_info("Article deleted successfully");
        } else {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "You do not have permission to delete this article or it does not exist."]);
            Logger::backend_error("403, You do not have permission to delete this article or it does not exist.");
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to delete article: " . $e->getMessage()]);
        Logger::backend_error("500, Failed to delete article: " . $e->getMessage());
    }
} else {
    http_response_code(405); // METHOD not allowed
    echo json_encode(["error" => "Only DELETE method is allowed"]);
    Logger::backend_error("405, Only DELETE method is allowed");
}