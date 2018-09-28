<?php

namespace src\Controller;

use Core\Controller;
use Core\Response\Redirect;
use Core\Response\JSON;

class                           UserController extends Controller
{
    public function             me()
    {
        return new Redirect("/user/42");
    }
    
    public function             show($id)
    {
        return new JSON(array('id' => $id));
    }
}