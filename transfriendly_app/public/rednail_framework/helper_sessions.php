<?php

    require_once(FRAMEWORK.'class_session.php');
    $session_handler = new my_session_handler;
    $session_handler->set_connection(   db_session_host,
                                        db_session_user,
                                        db_session_password,
                                        db_session_database);
    $session_handler->set_session_table(db_session_table);
    
    session_set_save_handler(array($session_handler, 'open'),
                             array($session_handler, 'close'),
                             array($session_handler, 'read'),
                             array($session_handler, 'write'),
                             array($session_handler, 'destroy'),
                             array($session_handler, 'gc'));
    ini_set('session.gc_maxlifetime','3600');
    ini_set('session.cache_expire','60');
    session_cache_expire(60);
    session_cache_limiter('public');
	session_start();
