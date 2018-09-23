<?php

namespace Core;

class                           Router
{
    private static              $routes = array();
    
    static private function     splitResourceAndParams($url)
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
    
    static public function      connect($url, $route)
    {
        $aResourceParams = Router::splitResourceAndParams($url);
        
        if (!array_key_exists($aResourceParams['resource'], self::$routes))
        {
            self::$routes[$aResourceParams['resource']] = array();
        }
        self::$routes[$aResourceParams['resource']][$aResourceParams['params']] = $route;
    }
    
    static public function      get($url)
    {
        $resource = '';
        $aFragUrl = explode('/', trim($url, '/'));
        $nbFragUrl = count($aFragUrl);
        $response = array('controllerName' => null, 'controllerMethod' => null, 'params' => null);
        for ($i = 0; $i < $nbFragUrl; ++$i)
        {
            $resource .= "/{$aFragUrl[$i]}";
            if (array_key_exists($resource, self::$routes))
            {
                foreach (self::$routes[$resource] as $controllerParams => $controllerRoute)
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
                                $response['controllerName'] = "\\src\\Controller\\" . ucfirst($controllerRoute['controller']) . "Controller";
                                $response['controllerMethod'] = "{$controllerRoute['action']}Action";
                                return $response;
                            }
                        }
                        else
                        {
                            $response['controllerName'] = "\\src\\Controller\\" . ucfirst($controllerRoute['controller']) . "Controller";
                            $response['controllerMethod'] = "{$controllerRoute['action']}Action";
                            return $response;
                        }
                    }
                }
            }
        }
        return null;
    }

}