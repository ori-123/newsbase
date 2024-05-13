<?php

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

session_start();
check_login();

global $pdo;

if ($_SERVER["REQUEST_METHOD"] === "DELETE" && isset($_GET["id"])) {
    $article_id = sanitize_input($_GET["id"]);
    $user_id = sanitize_input($_SESSION['user_id']);

    try {
        $pdo->beginTransaction();

        $get_stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :article_id AND user_id = :user_id");
        $get_stmt->bindParam('article_id', $article_id, PDO::PARAM_INT);
        $get_stmt->bindParam('user_id', $user_id, PDO::PARAM_INT);
        $get_stmt->execute();
        $article = $get_stmt->fetch(PDO::FETCH_ASSOC);

        if ($article) {
            $delete_stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id");
            $delete_stmt->execute();

            $pdo->commit();

            http_response_code(200); // OK
            echo json_encode(["message" => "Article deleted successfully"]);
        } else {
            http_response_code(403); // Forbidden
            echo json_encode(["error" => "You do not have permission to delete this article or it does not exist."]);
        }
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to delete article: " . $e->getMessage()]);
    }
} else {
    http_response_code(405); // METHOD not allowed
    echo json_encode(["error" => "Only DELETE method is allowed"]);
}