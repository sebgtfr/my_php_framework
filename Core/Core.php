<?php

namespace Core;

use Core\Router;
use Core\Response\Response;

class                           Core
{    
    /*
     * @param                   Reflection $reflection : Reflection of the callback.
     * @param                   array $paramsURL : Parameters find on url route.
     * @return                  array $params : Paramters set on the callback call.
     * 
     * @description             Use Reflection class to know all parameters' features
     *                          and generate an array of paramèter whose will set on the callback.
     */
    static private function     getParamsCallback($reflection, array $paramsUrl, array $classParameters)
    {
        $params = array();
        foreach ($reflection->getParameters() as $methodParam)
        {
            $paramClass = $methodParam->getClass();
            if ($paramClass !== null && array_key_exists(($className = ltrim($paramClass->getName(), '/')), $classParameters))
            {
                $params[] = $classParameters[$className];
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
     * @param                   string $methodHTTP : Type of HTTP request.
     * @param                   array $requestParams : HTTP parameters send to the Core\Request.
     * 
     * @description             Generate a new request using the URL in parameter
     *                          on the router to define the controller.
     */
    static public function      executeRequest(string $url, string $methodHTTP = 'GET', array $requestParams = array())
    {
        $request = new Request($methodHTTP, $requestParams);
        
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
        $classParameters = array(get_class($request) => &$request);
        $response = call_user_func_array($callback, self::getParamsCallback($reflection, $controllerInfos['params'], $classParameters));
        return (($response instanceof Response) ? $response : new Response($response));
    }
    
     /*
     * @description             Prepare route of the framework and execute client request.
     */
    public function             run()
    {
        Router::get('/', function ()
        {
            TemplateEngine::parse('User');
            exit;
            return 'Welcome to my framework PHP';
        });
        
        include_once 'routes.php';
        
        try
        {
            $response = self::executeRequest(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), $_SERVER['REQUEST_METHOD']);
        }
        catch (\Exception $ex)
        {
            $response = new Response($ex->getMessage(), 500);
        } 
        $response->render();
    }
}
