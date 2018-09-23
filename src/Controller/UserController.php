<?php

namespace src\Controller;

use Core\Controller;

class                           UserController extends Controller
{
    public function             indexAction()
    {
        echo __METHOD__;
    }
    
    public function             meAction($id)
    {
        echo __METHOD__;
        echo "id = $id";
    }
}