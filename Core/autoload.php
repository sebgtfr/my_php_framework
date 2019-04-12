<?php

spl_autoload_register(function ($classname)
{
    $filename = ROOTPATH . "/" . str_replace('\\', '/', $classname) . ".php";
    if (file_exists($filename))
    {
        require_once $filename;
    }
}, true, true);