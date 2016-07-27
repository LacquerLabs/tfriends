<?php
    
    class main extends controller {
        
        public $_use_db = true;
        public $_no_cache = true;
        public $_print_stats = true;

        public function landing_page($vars = null) {
            
            // redirect the user to their dashboard
            if(isset($_SESSION['user']['user_id']) ) {
                header("Location: ".$this->_base_url.'dashboard');
                $this->exit();
            }
            
            // create a view
            $view = new View();
            
            // add a template to the view
            $view->addTemplate('landing_page.html');
            
            // add another template
            // $view->addTemplate('show_variables.php');
            
            // set the header and footer
            // $view->setHeader( 'layout/header.php' );
            // $view->setFooter( 'layout/footer.php' );
            
            $vars['currentMenu'] = 'messages';

            $view->breadcrumb_add('Home',$this->_base_url);
            $view->breadcrumb_add('Messages',null, true);
 
            // set the variables
            $view->setGlobals( $this->_vars );
            $view->setVariables( $vars );
            
            // render the view (true shows header/footer, false does not)
            $view->render(true);
            
            $token = $this->get_crypto_token(32);
            echo strlen($token).' '.$token;

            // end the application
            $this->end();
        }
        
     }


