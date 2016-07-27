<?php
    
    class rickety_facebook {
       
        public $_fb_access_token;
        public $_fb_user_profile;
        public $_fb_logout_url;
        public $_fb_login_url;

        public function __construct( $vars = null ) {
            $this->_base_url = $this->get_base_url();
            @list($this->_route,$this->_query) = explode('?',trim(str_replace($this->get_base_directory(), '', $_SERVER['REQUEST_URI']), '/'));
            $this->_full_url = $this->_base_url.$this->_route;

            require_once(FRAMEWORK.'libs/facebook-php-sdk/src/facebook.php');
 
            if(isset($vars['vars_query']['fb_logout']) && $vars['vars_query']['fb_logout'] == 'true' ) $fb_logout = true;
            else $fb_logout = false;

            if(isset($vars['vars_query']['code'])) $fb_code = $vars['vars_query']['code'];
            else $fb_code = false;

            if(isset($vars['vars_query']['state'])) $fb_state = $vars['vars_query']['state'];
            else $fb_state = false;

            $this->init_facebook($fb_code, $fb_state, $fb_logout);
        }
        
        public function __destruct() {
 
        }

        /* facebook functions */
        public function init_facebook($fb_code = null, $fb_state = null, $fb_logout = false ) {
       
            $this->_facebook = new Facebook(array(
                                       'appId'  => fb_app_id,
                                       'secret' => fb_app_secret,
                                       ));

            if($fb_logout) {
                setcookie('fbs_'.$this->_facebook->getAppId(), '', time()-100);
                session_destroy();
                header('Location: '.$this->_full_url);
                exit();
            }

            // See if there is a user from a cookie
            $user = $this->_facebook->getUser();
            if(!empty($fb_code)) {
                // We got a facebook code!  we're going to login here
                try {
                    $this->_facebook->api('oauth/access_token', array(
                            'client_id'     => fb_app_id,
                            'client_secret' => fb_app_secret,
                            'type'          => 'client_cred',
                            'code'          => $fb_code,
                        ));

                    // Proceed knowing you have a logged in user who's authenticated.
                    $this->_fb_access_token = $this->_facebook->getAccessToken();

                    // get the users profile
                    // probable should be a helper function and not done here
                    $this->_fb_user_profile = $this->_facebook->api('/me');

                    // get the facebook logout URL
                    $params = array();
                    // $params = array( 'next' => $this->_base_url.'fb_logout/' );
                    $this->_fb_logout_url = $this->_facebook->getLogoutUrl($params);

                    return true;
                    exit();
                    // why are we redirecting here?
                    header('Location: '.$this->_full_url);
                    exit();
                } catch (FacebookApiException $e) {
                    // couldn't get teh facebook token
                    echo '<pre> oauth token '.htmlspecialchars(print_r($e, true)).'</pre>';
                    $user = null;
                    exit();
                }
            } else if ($user) {
                // echo '<pre>got fb user</pre>';
                try {
                    // Proceed knowing you have a logged in user who's authenticated.
                    $this->_fb_access_token = $this->_facebook->getAccessToken();
                    $this->_fb_user_profile = $this->_facebook->api('/me');
                    $params = array();
                    $params = array( 'next' => $this->_base_url.'fb_logout/' );
                    $this->_fb_logout_url = $this->_facebook->getLogoutUrl($params);
                } catch (FacebookApiException $e) {
                    echo '<pre> if user '.htmlspecialchars(print_r($e, true)).'</pre>';
                    $user = null;
                    exit();
                }
            } else {
                // echo '<pre>no fb user</pre>';

                // user is not logged in
                $this->_fb_access_token = false;
                $this->_fb_logout_url = false;
                $params = array(
                    'scope' => 'email,read_stream,read_stream,user_status',
                    'redirect_uri' => $this->_full_url,
                    );
        
                $loginUrl = $this->_facebook->getLoginUrl($params);
                $this->_fb_login_url = $this->_facebook->getLoginUrl($params);
            }
        }
        
        /* end facebook functions */

        function get_base_directory() {
            /* returns /myproject/index.php */
            $path = $_SERVER['PHP_SELF'];
            
            /*
             * returns an array with:
             * Array (
             *  [dirname] => /myproject/
             *  [basename] => index.php
             *  [extension] => php
             *  [filename] => index
             * )
             */
            $path_parts = pathinfo($path);
            $directory = $path_parts['dirname'];
            /*
             * If we are visiting a page off the base URL, the dirname would just be a "/",
             * If it is, we would want to remove this
             */
            $directory = ($directory == "/") ? "" : $directory;
            return $directory;
        }

        function get_base_url() {
            /* First we need to get the protocol the website is using */
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';
            
            /* returns /myproject/index.php */
            $path = $_SERVER['PHP_SELF'];
            
            /*
             * returns an array with:
             * Array (
             *  [dirname] => /myproject/
             *  [basename] => index.php
             *  [extension] => php
             *  [filename] => index
             * )
             */
            $path_parts = pathinfo($path);
            $directory = $path_parts['dirname'];
            /*
             * If we are visiting a page off the base URL, the dirname would just be a "/",
             * If it is, we would want to remove this
             */
            $directory = ($directory == "/") ? "" : $directory.'/';
            
            /* Returns localhost OR mysite.com */
            $host = $_SERVER['HTTP_HOST'];
            
            return $protocol . $host . $directory;
        }

    }
