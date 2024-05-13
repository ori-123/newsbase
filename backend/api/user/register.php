<?php
require_once '../../includes/database.php';
require_once '../../includes/helpers.php';

global $pdo;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = sanitize_input($_POST["username"]);
    $password = sanitize_input($_POST["password"]);

    if (empty($username) || empty($password)) {
        http_response_code(400); // Bad request
        echo json_encode(["error" => "Username and password are required"]);
        exit();
    }

    try {
        $pdo->beginTransaction();

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
        $stmt->execute(['username' => $username, 'password' => $hashed_password]);

        $pdo->commit();

        http_response_code(201); // Created
        echo json_encode(["message" => "User registered successfully"]);
    } catch (PDOException $e) {
        $pdo->rollBack();
        http_response_code(500); // Internal Server Error
        echo json_encode(["error" => "Failed to register user. Please try again later."]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
}
