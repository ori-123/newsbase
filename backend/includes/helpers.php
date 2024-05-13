<?php
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

function check_login() {
    session_start();

    if (!isset($_SESSION['user_id'])) {
        $_SESSION = array();
        session_destroy();
        header("Location: /login.php");
        exit();
    }
}