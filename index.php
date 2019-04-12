<?php

define("BASE_URI", str_replace('\\', '/', substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT']))));
define("ROOTPATH", __DIR__);

require_once 'Core/autoload.php';

$app = new Core\Core();
$app->run();