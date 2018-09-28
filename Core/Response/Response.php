<?php

namespace Core\Response;

use Core\HTTP;

class                           Response
{
    protected                   $_datas;
    protected                   $_http;
    
    public function             __construct($datas, int $codeHTTP = 200, array $headerHTTP = array())
    {
        $this->_datas = $datas;
        $this->_http = new HTTP($codeHTTP, $headerHTTP);
    }
    
    public function             rawDatas()
    {
        return $this->_datas;
    }
    
    public function             serialize()
    {
        $this->_http->header('Content-Type', 'text/plain');
        return $this->_datas;
    }
    
    public function             render()
    {
        $print = $this->serialize();
        $this->_http->send();
        if (!empty($print))
        {
            echo $print;
        }
    }
}
