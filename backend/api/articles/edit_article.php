<?php

use includes\Logger;

// Include necessary files and start session
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

global $pdo;

// Start session and check login status
session_start();

// Check if user is logged in
if (!check_login()) {
    http_response_code(401); // Unauthorized
    echo json_encode(["error" => "User needs to log in to continue"]);
    Logger::backend_error('401, User needs to log in to continue');
    exit();
}

// Check if the request method is PUT
if ($_SERVER["REQUEST_METHOD"] === "PUT") {
    try {
        // Get JSON data from the request body
        $put_data = json_decode(file_get_contents("php://input"), true);

        // Extract data from the JSON object
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

        // Set response code and message
        http_response_code(200);
        echo json_encode(['message' => 'Article information updated successfully.']);
        Logger::backend_info("Article information updated successfully.");
    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to update article information: " . $e->getMessage()]);
        Logger::backend_error("500, Failed to update article information: " . $e->getMessage());
    }
} else {
    // Respond with error for unsupported HTTP method
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only PUT method is allowed"]);
    Logger::backend_error("405, Only PUT method is allowed");
}
