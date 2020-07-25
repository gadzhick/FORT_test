<?php

/**
 * Class Api
 */
abstract class Api
{
    public $apiName = '';

    protected $method = '';

    public $requestUri = [];
    public $requestParams = [];

    protected $action = ''; //Название метода для выполнения

    /**
     * Api constructor.
     * @throws Exception
     */
    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");

        //Разделяем строку с GET параметрами и Uri
        $this->requestUri = explode('?', trim($_SERVER['REQUEST_URI'],'/'));
        //Массив GET параметров разделенных слешем
        $this->requestUri = explode('/', $this->requestUri[0]);
        $this->requestParams = $_REQUEST;

        //Определение метода запроса
        $this->method = $_SERVER['REQUEST_METHOD'];
        if ($this->method == 'POST' && array_key_exists('HTTP_X_HTTP_METHOD', $_SERVER)) {
            if ($_SERVER['HTTP_X_HTTP_METHOD'] == 'DELETE') {
                $this->method = 'DELETE';
            } else {
                throw new Exception("Unexpected Header");
            }
        }
    }

    /**
     * Функция выполнения запроса
     * @param $pdo
     * @return mixed
     */
    public function run($pdo) {
        if(array_shift($this->requestUri) !== $this->apiName){
            throw new RuntimeException('API Not Found', 404);
        }

        $this->action = $this->getAction();

        if (method_exists($this, $this->action)) {
            return $this->{$this->action}($pdo);
        } else {
            throw new RuntimeException('Invalid Method', 405);
        }
    }

    /**
     * Функция ответа сервера
     * @param array $data
     * @param int $status
     * @return false|string
     */
    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }

    /**
     * Функция выбора статуса ответа
     * @param $code
     * @return string
     */
    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            400 => 'Bad Request',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }

    /**
     * Функция выбора функции для ответа
     * @return string|null
     */
    protected function getAction()
    {
        $method = $this->method;
        switch ($method) {
            case 'GET':
                if( $this->requestUri[0] =='search'){
                    return 'searchAction';
                } elseif (is_numeric($this->requestUri[0])){
                    return 'viewAction';
                } else {
                    return 'indexAction';
                }
                break;
            case 'POST':
                return 'createAction';
                break;
            case 'DELETE':
                return 'deleteAction';
                break;
            default:
                return null;
        }
    }

    abstract protected function indexAction($pdo);
    abstract protected function viewAction($pdo);
    abstract protected function createAction($pdo);
    abstract protected function searchAction($pdo);
    abstract protected function deleteAction($pdo);
}