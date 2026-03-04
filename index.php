<?php
require_once __DIR__ . '/vendor/autoload.php';
session_start();
require_once __DIR__ . '/controllers/AuthController.php';
$controller = new AuthController();
$action = $_GET['action'] ?? 'login';

switch($action) {
    case 'register': $controller->register(); break;
    case 'login': $controller->login(); break;
    case 'verify': $controller->verify(); break;
    case 'otp': $controller->otp(); break;
    case 'home': $controller->home(); break;
    case 'logout': $controller->logout(); break;
    default: $controller->login(); break;
}

?>