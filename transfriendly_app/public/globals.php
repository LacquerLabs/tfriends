<?php

    // debugging global
    define('app_debug', true);

    // Framework Globals
    define('ROOT', __DIR__ .'/');
    define('FRAMEWORK', __DIR__ .'/rednail_framework/');
    define('MODELS', __DIR__ .'/app/models/');
    define('CONTROLLERS', __DIR__ .'/app/controllers/');
    define('VIEWS', __DIR__ .'/app/views/');
     
    // sessions database
    define('use_db_sessions', false);
    define('db_session_host', db_host);
    define('db_session_user', db_user);
    define('db_session_password', db_password);
    define('db_session_database', db_database);
    define('db_session_table', 'session_data');
   
    if(app_debug) {
        ini_set('error_reporting', E_ALL);
        ini_set('display_errors', '1');
    } else {
        ini_set('error_reporting', E_NONE);
        ini_set('display_errors', '0');
    }

