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
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        http_response_code(200);
        echo json_encode(["message" => "User authenticated successfully"]);
    } else {
        http_response_code(401);
        echo json_encode(["error" => "Invalid username or password"]);
    }
} else {
    http_response_code(405);
    echo json_encode(["error" => "Only POST method is allowed"]);
}