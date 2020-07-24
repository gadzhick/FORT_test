<?php
/**
 * Функция получения продукта по ID
 * @param $id
 * Идентификатор продукта
 * @param $pdo
 * Объект подключения к БД
 * @return Exception[]|PDOException[]  ошибка при работе с БД
 * @return Object объект с ответом
 */
    function get_product_by_id($id, $pdo){
        $stmt = $pdo->prepare("SELECT * FROM product WHERE id=?");
        try {
            $stmt->execute([$id]);
        } catch (PDOException $e){
            return array('message'=>$e);
        }
        return $stmt->fetch(PDO::FETCH_LAZY);
    }

/**
 * Функция получения всех продуктов
 * @param $pdo
 * Объект подключения к БД
 * @return Exception[]|PDOException[] ошибка при работе с БД
 * @return Object объект с ответом
 */
    function get_products($pdo){
        $stmt = $pdo->prepare("SELECT * FROM product");
        try {
            $stmt->execute();
        } catch (PDOException $e){
            return array('message'=>$e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

/**
 * Функция добавления продукта
 * @param $data
 * Массив с данными для добавления
 * @param $pdo
 * Объект подключения к БД
 * @return Exception[]|PDOException[] ошибка при работе с БД
 * @return int ID нового продукта
 */
    function add_product($data, $pdo){
        $stmt = $pdo -> prepare("INSERT product SET name=:name, price=:price, description=:description");
        $stmt->bindParam(':name', $data['name']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':description', $data['description']);
        try {
            $stmt->execute();
        } catch (PDOException $e){
            return array('message'=>$e);
        }
        return $pdo->lastInsertId();
    }

/**
 * Функция удаления продукта
 * @param $id
 * Идентификатор продукта
 * @param $pdo
 * Объект подключения к БД
 * @return Exception[]|PDOException[]  ошибка при работе с БД
 * @return int
 */
    function delete_product($id, $pdo){
        $stmt = $pdo->prepare("DELETE FROM product WHERE id=?");
        try {
            $stmt->execute([$id]);
        } catch (PDOException $e){
            return array('message'=>$e);
        }
        return 1;
    }

/**
 * Функция поиска продукта
 * @param $searchString
 * Массив с данными для поиска
 * @param $pdo
 * Объект подключения к БД
 * @return Exception[]|PDOException[] ошибка при работе с БД
 * @return Object объект с ответом
 */
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

        try {
            $stmt->execute();
        } catch (PDOException $e){
            return array('message'=>$e);
        }
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

/**
 * Функция отправки запроса
 *
 * @param $result
 * Ответ сервера при успешном запросе
 * @param $httpCode
 * Код ответа сервера
 * @param $message
 * Сообщение при ошибке
 */
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
        }
        echo json_encode(array($result, JSON_UNESCAPED_UNICODE));
    }