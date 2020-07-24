<?php
    include "functions.php";
    if($method === "GET" && isset($formData['id'])){
        if (is_numeric($formData['id'])){
            $product = get_product_by_id($formData['id'], $pdo);
            $result = array();
//            foreach ($product as $key=>$value){
//                $result[$key]=$value;
//            }
//            unset($result['queryString']);
            $result['id']=$product->id;
            $result['name']=$product->name;
            $result['price']=$product->price;
            $result['description']=$product->description;
            echo json_encode($result, JSON_UNESCAPED_UNICODE);
        }
        return "Wrong ID";
    }

    elseif ($method === "GET" && (isset($formData['name']) || isset($formData['description']))){
        $searchString = array();
        $result=array();
        foreach ($formData as $key=>$value){
            $searchString[$key]=$value;
            $value=urldecode($value);
        }
        $products = search_product($searchString, $pdo);
        foreach ($products as $key=>$value){
            $result[$key]=$value;
        }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    elseif ($method === "GET" && (!isset($formData['name']) || !isset($formData['description']) || !isset($formData['id']))){
        $products = get_products($pdo);
        $result=array();
        foreach ($products as $key=>$value){
                $result[$key]=$value;
            }
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    elseif ($method === "POST" && isset($formData)){
        $id = add_product($formData, $pdo);
        echo json_encode(array('message'=>'Successfull add product with id '.$id));
    }

    elseif ($method === "DELETE" && isset($formData['id'])){
        delete_product($formData['id'], $pdo);
        echo json_encode(array('message'=>'Successfull delete product with id '.$formData['id']));
    }

