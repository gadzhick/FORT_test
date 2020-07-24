<?php

$method = $_SERVER['REQUEST_METHOD'];
$formData = getFormData($method);

function getFormData($method) {
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;

    $data = array();
    $exploded = explode('&', file_get_contents('php://input'));

    foreach ($exploded as $pair) {
        $item = explode('=', $pair);
        if (count($item) == 2) {
            $data[urldecode($item[0])] = urldecode($item[1]);
        }
    }

    return $data;
}

$uri = $_SERVER['REQUEST_URI'];
$uri = rtrim($uri, '/');

$uri = str_replace($_SERVER['QUERY_STRING'], '', $uri);

$uri = explode('/', $uri);
$router = $uri[1];
$router = str_replace('?', '', $router);


require $router.'.php';
