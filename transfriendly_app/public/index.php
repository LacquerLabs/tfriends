<?php
/*
 * NanoPHP web bootstrap
 *
 */

// Define APPDIR constant to use in NanoPHP libraries
define('APPDIR', __DIR__.'/../');

// Require composer autoloader
require("../vendor/autoload.php");

// Get main config file for the basic application
$config = require(__DIR__.'/../config/main.php');

// Create an application instance and start!
(new nanophp\Libraries\Application($config))->run();
