<?php

namespace Core;

class                           Request
{
    static private              $_isAlreadyParse = false;
    
    private                     $_typeHTTP;
    private                     $_params;

    public function             __construct(string $typeHTTP, array $requestParam)
    {
        $this->_typeHTTP = strtoupper($typeHTTP);
        $this->_params = $requestParam;
        if (self::$_isAlreadyParse === false)
        {
            $parse = "parse{$this->_typeHTTP}";
            if (method_exists($this, $parse))
            {
                $this->$parse();
            }
            else
            {
                $this->parseFromInput();
            }
            self::$_isAlreadyParse = true;
        }
    }
    
    public function             getTypeHTTP()
    {
        return $this->_typeHTTP;
    }
    
    public function             isAJAX()
    {
        return boolval(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
    
    public function             input($keys = null)
    {
        if ($keys === null)
        {
            return $this->_params;
        }
        if (is_string($keys))
        {
            return $this->_params[$keys];
        }
        if (is_array($keys))
        {
            $input = array();
            foreach ($keys as $key)
            {
                $input[$key] = $this->_params[$key];
            }
            return $input;
        }
        return null;
    }
    
    public function             has($keys)
    {
        if (is_string($keys))
        {
            return boolval(array_key_exists($keys, $this->_params));
        }
        else if (is_array($keys))
        {
            return boolval(array_intersect($keys, array_keys($this->_params)) === $keys);
        }
        return false;
    }
    
    public function             __get(string $key)
    {
        return $this->_params[$key];
    }
    
    /**
     * Parse datas' functions
     */
    
    private function            parse(array $array)
    {
        foreach ($array as $key => $value)
        {
            $this->_params[$key] = trim(stripslashes(htmlspecialchars($value)));
        }        
    }
    
    private function            parseGET()
    {
        $this->parse($_GET);
    }
    
    private function            parsePOST()
    {
        $this->parse($_POST);
    }
    
    private function            parseFromInput()
    {
        $inputData = file_get_contents('php://input');
        $datas = array();
        parse_str($inputData, $datas);
        $this->parse($datas);
    }
}