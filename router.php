<?php
$routes = [];
function route($action, Closure $callback)
{
    global $routes;
    $action = trim($action, '/');
    $routes[$action] = $callback;
}
function dispatch($action)
{
    global $routes;
    $action = trim($action, '/');
    $callback = $routes[$action];
    echo call_user_func($callback);
}