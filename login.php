<?php
declare(strict_types=1);

use TCM\Controllers\AuthController;

require_once __DIR__ . '/src/bootstrap.php';

$auth = new AuthController();

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $auth->login();
} else {
    $auth->showLogin();
}
