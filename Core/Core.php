<?php

namespace Core;

use Core\Router;

class                           Core
{    
    private                     $_classParameters = array
    (
        // key must be class name, value must be the instance of object you want to give.
    );
    
    /*
     * @param                   Reflection $reflection : Reflection of the callback.
     * @param                   array $paramsURL : Parameters find on url route.
     * @return                  array $params : Paramters set on the callback call.
     * 
     * @description             Use Reflection class to know all parameters' features
     *                          and generate an array of paramèter whose will set on the callback.
     */
    private function            getParamsCallback($reflection, $paramsUrl)
    {
        $params = array();
        foreach ($reflection->getParameters() as $methodParam)
        {
            $paramClass = $methodParam->getClass();
            if ($paramClass !== null && array_key_exists(ltrim($paramClass->getName(), '/'), $this->_classParameters))
            {
                $params[] = $this->_classParameters[$paramClass->getName()];
            }
            else if (array_key_exists($methodParam->getName(), $paramsUrl))
            {
                $params[] = $paramsUrl[$methodParam->getName()];
            }
        }
        return $params;
    }
    
    /*
     * @param                   string $url : URL of the route
     * @param                   string $typeRequestHTTP : Type of HTTP request.
     * @param                   array $requestParams : HTTP parameters send to the Core\Request.
     * 
     * @description             Generate a new request using the URL in parameter
     *                          on the router to define the controller.
     */
    private function            executeRequest(string $url, string $typeRequestHTTP, array $requestParams = array())
    {
        $request = new Request($typeRequestHTTP, $requestParams);

        $controllerInfos = Router::getController($url, $request->getTypeHTTP());
        
        if ($controllerInfos['typeController'] == Router::CALLBACK_OBJECT)
        {
            if (!(class_exists($controllerInfos['controllerName']) && method_exists($controllerInfos['controllerName'], $controllerInfos['controllerMethod'])))
            {
                throw new \Exception("Controller or action not found !");
            }
            $reflection = new \ReflectionMethod($controllerInfos['controllerName'], $controllerInfos['controllerMethod']);
            $controller = new $controllerInfos['controllerName']();
            $callback = array($controller, $controllerInfos['controllerMethod']);
        }
        else
        {
            $callback = $controllerInfos['controllerCallback'];
            $reflection = new \ReflectionFunction($callback);
            $controller = new \Core\Controller();
        }
        
        $controller->setRequest($request);
        $this->_classParameters["Core\\Request"] = &$request;
        
        $response = call_user_func_array($callback, $this->getParamsCallback($reflection, $controllerInfos['params']));
        return $response;
    }
    
     /*
     * @description             Prepare route of the framework and execute client request.
     */
    public function             run()
    {
        Router::get('/', function ()
        {
            echo 'Welcome to my framework PHP';
        });
        
        include_once 'route.php';
        
        try
        {
           $this->executeRequest(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), $_SERVER['REQUEST_METHOD']);
        }
        catch (\Exception $ex)
        {
            echo $ex->getMessage();
        } 
    }
}
