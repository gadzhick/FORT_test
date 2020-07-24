<?php
    include "functions.php";
/**
 * Выбор условий
 *
 * В зависимости от метода запроса и входных данных ПО выдаст разные значения
 */
    if($method === "GET" && isset($formData['id'])){
        if (is_numeric($formData['id'])){
            $product = get_product_by_id($formData['id'], $pdo);
            $result = array();

            if (!isset($product['message'])) {
//            foreach ($product as $key=>$value){
//                $result[$key]=$value;
//            }
//            unset($result['queryString']);
                $result['id']=$product->id;
                $result['name']=$product->name;
                $result['price']=$product->price;
                $result['description']=$product->description;

                    sendResponse($result, 200, '');
            } else {

                sendResponse('',404, $product['message']);
            }
        }
        sendResponse('', 401, 'ID isn`t numeric');
    }

    elseif ($method === "GET" && (isset($formData['name']) || isset($formData['description']))){
        $searchString = array();
        $result = array();
        foreach ($formData as $key=>$value){
            $searchString[$key]=$value;
            $value=urldecode($value);
        }
        $products = search_product($searchString, $pdo);
        if (!isset($product['message'])) {
            foreach ($products as $key=>$value){
                $result[$key]=$value;
            }
            sendResponse($result, 200, '');
        }
        else {

            sendResponse('',404,$products['message']);
        }
    }

    elseif ($method === "GET" && (!isset($formData['name']) || !isset($formData['description']) || !isset($formData['id']))){
        $products = get_products($pdo);
        $result = array();
        if (!isset($product['message'])) {
            foreach ($products as $key=>$value){
                $result[$key]=$value;
            }
            sendResponse($result, 200, '');
        } else {

            sendResponse('',404,$product['message']);
        }
    }

    elseif ($method === "POST" && isset($formData)){
        $id = add_product($formData, $pdo);
        if (!isset($product['message'])) {
            sendResponse(array('id'=>$id), 200, '');
        } else {
            sendResponse('',404,$product['message']);
        }
    }

    elseif ($method === "DELETE" && isset($formData['id'])){
        if (is_numeric($formData['id'])) {
          $product = delete_product($formData['id'], $pdo);
            if (!isset($product['message'])) {
                sendResponse(array('id' => $formData['id']), 200, '');
            } else {
                sendResponse('', 404, $product['message']);
            }
        } else sendResponse('', 401, 'ID isn`t numeric');
    }

    else sendResponse('', 401,"Bad Request");

