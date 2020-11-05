<?php
require __DIR__ . '/../bootstrap.php';

function authenticated($routeFound)
{
    if (!$routeFound['protected']) {
        return true;
    }

    if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
        return false;
    }

    $authHeader = $_SERVER['HTTP_AUTHORIZATION'];
    preg_match('/Bearer\s(\S+)/', $authHeader, $matches);

    if (!isset($matches[1])) {
        return false;
    }

    $token = $matches[1];

    return (new Src\Controllers\AuthController())->authenticate($token);
}

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestMethod = $_SERVER['REQUEST_METHOD'];

$routeFound = false;
foreach ($routes as $route) {
    if ($route['method'] == $requestMethod && preg_match($route['expression'], $uri)) {
        $routeFound = $route;
        break;
    }
}

if (!$routeFound) {
    header("HTTP/1.1 404 Not Found");
    exit(json_encode(['message' => 'Not Found']));
}

$controllerName = $routeFound['controller'];
$methodName = $routeFound['controller_method'];

if (!authenticated($routeFound)) {
    header("HTTP/1.1 401 Unauthorized");
    exit(json_encode(['message' => 'Unauthorized']));
}

$namespace = "Src\\Controllers";
$controller = "$namespace\\$controllerName";

$response = (new $controller)->$methodName();

echo json_encode($response);
