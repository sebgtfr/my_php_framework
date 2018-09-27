<?php

namespace Core;

class                           Router
{
    private static              $routes = array();
    
    const                       CALLBACK_FUNCTION = 0;
    const                       CALLBACK_OBJECT = 1;
    
    /**
     * @params                  array $routes : Array of all routes file by HTTP request type.
     * 
     * @description             Get all route know by the router file by HTTP request type.
     */
    static public function      getRoutes()
    {
        $routes = array();
        foreach (self::$routes as $typeURL => $route)
        {
            $routes[$typeURL] = array();
            foreach ($route as $url => $params)
            {
                foreach (array_keys($params) as $urlParams)
                {
                    if (!empty($urlParams))
                    {
                        $urlParams = "/{$urlParams}";
                    }
                    $routes[$typeURL][] = "{$url}{$urlParams}";
                }
            }
        }
        return $routes;
    }

    /**
     * @params                  string $url : url route need to split.
     * 
     * @description             Slit the route url define in parameter in order to
     *                          get the resource side and the parameters side.
     */
    static private function     splitResourceAndParams(string $url)
    {        
        $aResourceParams = array('resource' => null, 'params' => null);
        if (($pos = strpos($url, '$')) !== FALSE)
        {
            if ($pos === 0)
            {
                throw new \ParseError("Resource of the url isn't found !", 404);
            }
            $aResourceParams['resource'] = ($url === '/') ? '/' : rtrim(substr($url, 0, $pos), '/');
            $aResourceParams['params'] = rtrim(substr($url, $pos), '/');
        }
        else
        {
            $aResourceParams['resource'] = ($url === '/') ? '/' : rtrim($url, '/');
        }
        return $aResourceParams;
    }
    
    /**
     * @param                   string $url : url of the route
     * @param                   string $route : controller route format "[ObjectName]/[MethodName]".
     * @param                   callable $route : any function use as a callback.
     * @param                   string $typeRequestHTTP : HTTP type of the request.
     */
    static public function      connect(string $url, $route, string $typeRequestHTTP)
    {
        $typeRequestHTTP = strtoupper($typeRequestHTTP); // Make sure that Request is uppercase
        $aResourceParams = Router::splitResourceAndParams($url);
        
        if (!array_key_exists($typeRequestHTTP, self::$routes))
        {
            self::$routes[$typeRequestHTTP] = array();
        }
        if (!array_key_exists($aResourceParams['resource'], self::$routes[$typeRequestHTTP]))
        {
            self::$routes[$typeRequestHTTP][$aResourceParams['resource']] = array();
        }
        self::$routes[$typeRequestHTTP][$aResourceParams['resource']][$aResourceParams['params']] = $route;
    }
    
    /**
     * @description             Alias of Router::connect with HTTP type request as "GET".
     */
    static public function      get(string $url, $route)
    {
        Router::connect($url, $route, "GET");
    }
    
    /**
     * @description             Alias of Router::connect with HTTP type request as "POST".
     */
    static public function      post(string $url, $route)
    {
        Router::connect($url, $route, "POST");
    }
    
    /**
     * @description             Alias of Router::connect with HTTP type request as "PUT".
     */
    static public function      put(string $url, $route)
    {
        Router::connect($url, $route, "PUT");
    }
    
    /**
     * @description             Alias of Router::connect with HTTP type request as "DELETE".
     */
    static public function      delete(string $url, $route)
    {
        Router::connect($url, $route, "DELETE");
    }
    
    /**
     * @param                   array $response : Array fill with url parameters, use for generate final response.
     * @param                   string $controllerRoute : Name of the route's callback or name of the controller object with his method's action
     * @param                   Closure $controllerRoute : instance of the route's callback
     * @return                  array $response : Datas informations of the route and his controller.
     * 
     * @description             prepare router's response filling array with route and controller's datas.
     */
    static private function     setController(array $response, $controllerRoute)
    {
        if (is_string($controllerRoute))
        {
            $response['typeController'] = self::CALLBACK_OBJECT;
            $controller = explode('/', $controllerRoute);
            $lenController = count($controller);
            
            if ($lenController == 1 && is_callable($controller[0]))
            {
                $response['typeController'] = self::CALLBACK_FUNCTION;
                $response['controllerCallback'] = $controller[0];
            }
            else if ($lenController == 2)
            {
                $response['typeController'] = self::CALLBACK_OBJECT;
                $response['controllerName'] = "\\src\\Controller\\" . ucfirst($controller[0]);
                $response['controllerMethod'] = $controller[1];
            }
            else
            {
                throw new \ParseError("Route undefined !");
            }
        }
        else if (is_callable($controllerRoute))
        {
            $response['typeController'] = self::CALLBACK_FUNCTION;
            $response['controllerCallback'] = $controllerRoute;
        }
        else
        {
            throw new \ParseError("Route undefined !");
        }
        return $response;
    }
    
    /**
     * @param                   string $url : URL of the controller's route.
     * @param                   string $typeRequestHTTP : Type of the HTTP's request.
     * @return                  array $response : Datas informations of the route and his controller.
     * 
     * @description             Search route and controller's datas using the URL and the HTTP type of the request.
     */
    static public function      getController(string $url, string $typeRequestHTTP)
    {
        $typeRequestHTTP = strtoupper($typeRequestHTTP); // Make sure that Request is uppercase
        if (array_key_exists($typeRequestHTTP, self::$routes))
        {   
            $resource = '';
            $aFragUrl = explode('/', trim($url, '/'));
            $nbFragUrl = count($aFragUrl);
            $response = array('params' => null);
            for ($i = 0; $i < $nbFragUrl; ++$i)
            {
                $resource .= "/{$aFragUrl[$i]}";
                if (array_key_exists($resource, self::$routes[$typeRequestHTTP]))
                {
                    foreach (self::$routes[$typeRequestHTTP][$resource] as $controllerParams => $controllerRoute)
                    {
                        $response['params'] = array();
                        $params = (empty($controllerParams)) ? array() : explode('/', $controllerParams);
                        $nbParams = count($params);
                        if ($nbParams === ($nbFragUrl - ($i + 1)))
                        {
                            if ($nbParams)
                            {
                                $j = $i + 1;
                                foreach ($params as $param)
                                {
                                    $aParam = explode(':', $param);
                                    if ($aParam[0][0] !== '$')
                                    {
                                        if ($aParam[0] !== $aFragUrl[$j])
                                        {
                                            break;
                                        }
                                    }
                                    else if (!array_key_exists(1, $aParam) || preg_match("/^{$aParam[1]}$/", $aFragUrl[$j]))
                                    {
                                        $response['params'][ltrim($aParam[0], '$')] = $aFragUrl[$j];
                                    }
                                    else
                                    {
                                        break;
                                    }
                                    ++$j;
                                }
                                if ($j === $nbFragUrl)
                                {
                                    return self::setController($response, $controllerRoute);
                                }
                            }
                            else
                            {
                                return self::setController($response, $controllerRoute);
                            }
                        }
                    }
                }
            }
        }
        throw new \Exception("Route not found");
    }

}