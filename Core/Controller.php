<?php

namespace Core;

use Core\Request;

class                           Controller
{
    public                      $request;
    
    public function             setRequest(Request &$request)
    {
        $this->request = $request;
    }
}