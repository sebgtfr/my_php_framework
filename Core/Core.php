<?php

namespace Core;

use Core\Router;

class                           Core
{    
    private                     $_classParameters = array
    (
        // key must be class name, value must be the instance of object you want to give.
    );
    
    public function run()
    {
        Router::connect('/', ['controller' => 'app', 'action' => 'index']);        
        
        include_once 'route.php';
        
        $controllerInfos = Router::get(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
        
        if (class_exists($controllerInfos['controllerName']) &&
            method_exists($controllerInfos['controllerName'], $controllerInfos['controllerMethod']))
        {
            $params = array();
            $reflection = new \ReflectionMethod($controllerInfos['controllerName'], $controllerInfos['controllerMethod']);
            
            $controller = new $controllerInfos['controllerName'];
            $this->_classParameters["Core\\Request"] = $controller->getRequest();
            
            foreach ($reflection->getParameters() as $methodParam)
            {
                $paramClass = $methodParam->getClass();
                if ($paramClass !== null && array_key_exists(ltrim($paramClass->getName(), '/'), $this->_classParameters))
                {
                    $params[] = $this->_classParameters[$paramClass->getName()];
                }
                else if (array_key_exists($methodParam->getName(), $controllerInfos['params']))
                {
                    $params[] = $controllerInfos['params'][$methodParam->getName()];
                }
            }                        
            call_user_func_array(array($controller ,$controllerInfos['controllerMethod']), $params);
        }
        else
        {
            header("HTTP/1.0 404 Not Found");
        }
    }
}
