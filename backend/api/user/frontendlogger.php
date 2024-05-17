<?php

require_once '../../includes/database.php';
require_once '../../includes/helpers.php';
require_once '../../includes/cors.php';
require_once '../../vendor/autoload.php';

use includes\Logger;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $json_data = file_get_contents('php://input');
    $log_data = json_decode($json_data, true);

    if ($log_data['level'] === 'error') {
        Logger::frontend_error($log_data['message']);
    } else {
        Logger::frontend_info($log_data['message']);
    }
}