<?php

use Monolog\Logger;

$config = [
'production' => true,
'timezone' => 'Asia/Tehran',
'default' => ['controller' => 'site', 'action' => 'index'],
'log' => ['level' => Logger::DEBUG, 'dir' => APPDIR.'logs']
];

$config['log']['handler'] = new Monolog\Handler\RotatingFileHandler("{$config['log']['dir']}/runtime.log", $config['log']['level']);

return $config;
