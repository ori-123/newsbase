<?php

global $pdo;

use includes\Logger;

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

session_start();
check_login(); // Check if user is logged in, reroute to login page on failure.

if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    try {

        // Get input data from request
        parse_str(file_get_contents("php://input"), $put_data);
        $article_id = $put_data['article_id'];
        $new_url = isset($put_data['new_url']) ? $put_data['new_url'] : null;
        $new_title = isset($put_data['new_title']) ? $put_data['new_title'] : null;
        $new_img_url = isset($put_data['new_img_url']) ? $put_data['new_img_url'] : null;
        $new_description = isset($put_data['new_description']) ? $put_data['new_description'] : null;

        // Construct the SQL query dynamically based on provided fields
        $sql = "UPDATE articles SET ";
        $params = [];

        if ($new_url !== null) {
            $sql .= "url = :url, ";
            $params['url'] = $new_url;
        }
        if ($new_title !== null) {
            $sql .= "title = :title, ";
            $params['title'] = $new_title;
        }
        if ($new_img_url !== null) {
            $sql .= "image_url = :image_url, ";
            $params['image_url'] = $new_img_url;
        }
        if ($new_description !== null) {
            $sql .= "description = :description, ";
            $params['description'] = $new_description;
        }

        // Remove the trailing comma and space
        $sql = rtrim($sql, ", ");

        // Add WHERE clause to specify the article to update
        $sql .= " WHERE id = :article_id";

        // Prepare and execute the SQL query
        $stmt = $pdo->prepare($sql);

        // Bind parameters
        if ($new_url !== null) {
            $stmt->bindParam(':url', $params['url'], PDO::PARAM_STR);
        }
        if ($new_title !== null) {
            $stmt->bindParam(':title', $params['title'], PDO::PARAM_STR);
        }
        if ($new_img_url !== null) {
            $stmt->bindParam(':image_url', $params['image_url'], PDO::PARAM_STR);
        }
        if ($new_description !== null) {
            $stmt->bindParam(':description', $params['description'], PDO::PARAM_STR);
        }
        $stmt->bindParam(':article_id', $article_id, PDO::PARAM_INT);

        // Execute the statement
        $stmt->execute();

        http_response_code(200);
        echo json_encode(['message' => 'Article information updated successfully.']);
        Logger::info("Article information updated successfully.");
    } catch (PDOException $e) {
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to update article information: " . $e->getMessage()]);
        Logger::error("500, Failed to update article information: " . $e->getMessage());
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only PUT method is allowed"]);
    Logger::error("405, Only PUT method is allowed");
}
