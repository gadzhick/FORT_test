<?php
/**
 * Точка входа
 */
require_once 'ProductsApi.php';
try {
    $api = new ProductsApi();
    echo $api->run($pdo);
} catch (Exception $e) {
    echo json_encode(Array('message' => $e->getMessage()));
}