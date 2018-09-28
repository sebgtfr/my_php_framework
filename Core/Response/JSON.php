<?php

namespace Core\Response;

use Core\Response\Response;

class                           JSON extends Response
{
    static public function      decode($value)
    {
        if ($value instanceof JSON)
        {
            return $value->rawDatas();
        }
        return json_decode($value);
    }
    
    static public function      encode($value)
    {
        if ($value instanceof JSON)
        {
            $value = $value->rawDatas();
        }
        return json_encode($value);
    }
    
    public function             serialize()
    {
        $this->_http->header('Content-Type', 'application/json');
        return JSON::encode($this->_datas);
    }
}