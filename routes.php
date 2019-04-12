<?php

namespace Core;

Router::get('/user', 'UserController/me');
Router::get('/user/$id:[0-9]+', 'UserController/show');