<?php

$main = require_once(__DIR__.'/main.php');

$config = [
'default' => ['controller' => 'cli']
];

return array_merge($main, $config);
