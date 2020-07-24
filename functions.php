<?php
    function get_product_by_id($id, $pdo){
        $stmt = $pdo->prepare("SELECT * FROM product WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_LAZY);
    }

    function get_products($pdo){
        $stmt = $pdo->prepare("SELECT * FROM product");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    function add_product($data, $pdo){
        $stmt = $pdo -> prepare("INSERT product SET name=:name, price=:price, description=:description");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->execute();
        return $pdo->lastInsertId();
    }

    function delete_product($id, $pdo){
        $stmt = $pdo->prepare("DELETE FROM product WHERE id=?");
        $stmt -> execute([$id]);
    }

    function search_product($searchString, $pdo){
        $query=array();
        foreach ($searchString as $key=>$value){
            if($key === "name" OR $key === "description"){
                array_push($query, "$key LIKE :$key");
                $searchString[$key] = "%".$value."%";
            }
        }
        if (count($query)>1){
            $queryString = join(" OR ", $query);
        } else {
            $queryString=$query[0];
        }

        $queryString = "SELECT * FROM product WHERE $queryString";

        $stmt = $pdo->prepare($queryString);

        foreach ($searchString as $key=> $value){
            if ($key === "name" OR $key === "description"){
                $stmt->bindParam(":$key", $value);
            }
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    function sendResponse ($result, $httpCode, $message){
        switch ($httpCode){
            case 400:
                header ('HTTP/1.0 400 Bad Request');
                $result = array('error'=>$message);
                break;
            case 404:
                header ('HTTP/1.0 404 Not Found');
                $result = array('error'=> $message);
                break;
            case 401:
                header('HTTP/1.0 401 Unauthorized');
                $result = array('error' => $message);
                break;
        }
        echo json_encode(array($result, JSON_UNESCAPED_UNICODE));
    }