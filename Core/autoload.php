<?php

spl_autoload_register(function ($classname)
{
    $filename = "{$_SERVER["DOCUMENT_ROOT"]}/" . BASE_URI . "/" . str_replace('\\', '/', $classname) . ".php";
    if (file_exists($filename))
    {
        require_once $filename;
    }
}, true, true);