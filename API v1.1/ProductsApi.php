<?php

require_once 'connect.php';
require_once 'Api.php';

/**
 * Class ProductsApi
 */
class ProductsApi extends Api
{
    /**
     * Название Api
     * @var string
     */
    public $apiName = 'product';

    /**
     * Функция получения всех продуктов
     * @param $pdo
     * Объект подключения к БД
     * @return array|string
     */
    protected function indexAction($pdo)
    {
        $stmt = $pdo->prepare("SELECT * FROM product WHERE 1");
        try {
            $stmt -> execute();
        } catch (PDOException $e){
            return $this->response(array('message'=>$e), 404);
        }

        $result =  $stmt -> fetchAll(PDO::FETCH_OBJ);
        if ($result=='') {
            return $this->response($result, 200);
        }
        return $this->response(array('message'=>'Data not found'), 404);
    }

    /**
     * Функяиц получения продукта по ID
     * @param $pdo
     * Объект подключения к БД
     * @return array|string
     */
    protected function viewAction($pdo)
    {
        $id = $this->requestUri[0];
        if (is_numeric($id)){
            $stmt = $pdo->prepare("SELECT * FROM product WHERE id=:id");
            $stmt-> bindParam(':id',$id);
            try {
                $stmt->execute();
            } catch (PDOException $e){
                return $this->response(array('message'=>$e), 404);
            }
            $result = $stmt->fetchAll(PDO::FETCH_OBJ);
            if ($result=='') {
                return $this->response($result, 200);
            }
            return $this->response(array('message'=>'Data not found'), 404);
        }
        return $this->response("Invalid ID", 400);
    }

    /**
     * Функция поиска продукта по имени или по описанию
     * @param $pdo
     * Объект подключения к БД
     * @return array|string
     */
    protected function searchAction($pdo)
    {
        $query=array();
        foreach ($this->requestParams as $key=>$value){
            if($key === "name" OR $key === "description"){
                array_push($query, "$key LIKE :$key");
                $this->requestParams[$key] = urldecode($value);
                $this->requestParams[$key] = "%".$value."%";
            }
        }
        if (count($query)>1){
            $queryString = join(" OR ", $query);
        } else {
            $queryString=$query[0];
        }

        $queryString = "SELECT * FROM product WHERE $queryString";

        $stmt = $pdo->prepare($queryString);

        foreach ($this->requestParams as $key=> $value){
            if ($key === "name" OR $key === "description"){
                $stmt->bindParam(":$key", $value);
            }
        }

        try {
            $stmt->execute();
        } catch (PDOException $e){
            return $this->response(array('message'=>$e), 404);
        }

        $result = $stmt->fetchAll(PDO::FETCH_OBJ);
        if ($result=='') {
            return $this->response($result, 200);
        }
        return $this->response(array('message'=>'Data not found'), 404);
    }

    /**
     * Функция создания нового продукта
     * @param $pdo
     * Объект подключения к БД
     * @return array|string
     */
    protected function createAction($pdo)
    {
        $stmt = $pdo -> prepare("INSERT product SET name=:name, price=:price, description=:description");
        $stmt->bindParam(':name', $this->requestParams['name']);
        $stmt->bindParam(':price', $this->requestParams['price']);
        $stmt->bindParam(':description', $this->requestParams['description']);
        try {
            $stmt->execute();
        } catch (PDOException $e){
            return $this->response(array('message'=>$e));
        }
        $id = $pdo->lastInsertId();
        return $this->response(array('id'=>$id), 200);
    }

    /**
     * Удаление продукта по ID
     * @param $pdo
     * Объект подключения к БД
     * @return array|string
     */
    protected function deleteAction($pdo)
    {
        $id = $this->requestUri[0];
        $stmt = $pdo->prepare("DELETE FROM product WHERE id=?");
        try {
            $stmt->execute([$id]);
        } catch (PDOException $e){
            return $this->response(array('message'=>$e));
        }
        return $this->response(array('data'=>"Data deleted"), 200);
    }
}