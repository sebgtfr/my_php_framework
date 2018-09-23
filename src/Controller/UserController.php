<?php

namespace src\Controller;

use Core\Controller;

class                           UserController extends Controller
{
    public function             index()
    {
        echo __METHOD__;
    }
    
    public function             me($id)
    {
        echo __METHOD__ . PHP_EOL;
        echo "id = $id";
    }
}