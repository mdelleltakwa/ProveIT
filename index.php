<?php

require_once __DIR__ . '/config/config.php';

$controller = $_GET['controller'] ?? 'User';
$action = $_GET['action'] ?? 'login';

$controllerName = $controller . 'Controller';
$controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    if (class_exists($controllerName)) {
        $c = new $controllerName();
        if (method_exists($c, $action)) {
            $c->$action();
        } else {
            die("Action '$action' not found in '$controllerName'");
        }
    } else {
        die("Controller '$controllerName' not found");
    }
} else {
    die("Controller file not found: $controllerFile");
}
