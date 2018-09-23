<?php

namespace Core;

use Core\Request;

class                           Controller
{
    protected                   $request;

    public function             __construct()
    {
        $this->request = new Request();
    }
    
    public function             getRequest()
    {
        return $this->request;
    }
}