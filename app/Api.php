<?php

declare(strict_types=1);

namespace Api;

require 'vendor/autoload.php';

abstract class Api
{
    public $requestParams = [];
  
    public function __construct() {
        header("Access-Control-Allow-Orgin: *");
        header("Access-Control-Allow-Methods: *");
        header("Content-Type: application/json");
 
        $this->requestParams = $_REQUEST;
    }
 
    public function run() {

        $dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
            $r->addRoute('POST', '/api/login', 'login');
            $r->addRoute('POST', '/api/user/register', 'register');
            $r->addRoute('GET', '/api/user/get/{id:\d+}', 'getUser');
        });
        
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        $uri = rawurldecode($uri);
        
        $routeInfo = $dispatcher->dispatch($httpMethod, $uri);

        switch ($routeInfo[0]) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $this->response('API Not Found', 404);
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
                $allowedMethods = $routeInfo[1];
                $this->response('Method Not Allowed', 405);
                break;
            case \FastRoute\Dispatcher::FOUND:
                $handler = $routeInfo[1];
                $vars = $routeInfo[2];
                return $this->{$handler}($vars);
        }
    }
 
    protected function response($data, $status = 500) {
        header("HTTP/1.1 " . $status . " " . $this->requestStatus($status));
        return json_encode($data);
    }
 
    private function requestStatus($code) {
        $status = array(
            200 => 'OK',
            403 => 'Forbidden',
            422 => 'Unprocessable Entity',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            500 => 'Internal Server Error',
        );
        return ($status[$code])?$status[$code]:$status[500];
    }
}