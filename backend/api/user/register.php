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

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    if ($stmt->fetch(PDO::FETCH_ASSOC)) {
        http_response_code(409); // Conflict
        echo json_encode(["error" => "Username already taken"]);
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute(['username' => $username, 'password' => $hashed_password]);

    http_response_code(201); // Created
    echo json_encode(["message" => "User registered successfully"]);
} else {
    http_response_code(405); // METHOD not allowed
    echo json_encode(["error" => "Only POST method is allowed"]);
}