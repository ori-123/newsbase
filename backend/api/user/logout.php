<?php

session_start();

require_once '../../vendor/autoload.php';
require_once '../../includes/cors.php';

if (isset($_SESSION['user_id'])) {
    $_SESSION = array();
    session_destroy();
}

exit();
