<?php
    class application {
        
        public $route;
        
        public function __construct( $vars = null ) {
            
            require_once(FRAMEWORK.'class_router.php');

            require_once(FRAMEWORK.'class_model.php');
            require_once(FRAMEWORK.'class_view.php');
            require_once(FRAMEWORK.'class_controller.php');
            
            require_once(FRAMEWORK.'class_auth_email.php');
            
            $this->route = new Router;
        }
    
    }