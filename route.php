<?php

namespace Core;

Router::connect('/user/', ['controller' => 'user', 'action' => 'index']);
Router::connect('/user/$id:[0-9]*', ['controller' => 'user', 'action' => 'me']);