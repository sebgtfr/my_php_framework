<?php

namespace Core;

Router::get('/user/', 'UserController/index');
Router::get('/user/$id:[0-9]*', 'UserController/me');