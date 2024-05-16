<?php

require_once 'cors.php';
function sanitize_input($data)
{
    return htmlspecialchars(trim($data));
}

function check_login() {
    session_start();
    return isset($_SESSION['user_id']);
}