<?php

namespace Core\Response;

use Core\Response\Response;

class                           Redirect extends Response
{    
    public function             render()
    {
        $this->_http->header('Location', "{$_SERVER['REQUEST_SCHEME']}://{$_SERVER['HTTP_HOST']}/" . trim($this->_datas, '/'));
        $this->_http->send();
    }
}