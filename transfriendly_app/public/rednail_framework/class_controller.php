<?php
    
    class controller {
       
        public $_authenticated;
        public $_vars;
        
        public $_logged_in;
        
        public $_full_url;
        public $_base_url;
        public $_route;
        public $_query;
        
        public $_facebook;
        
        public $_no_cache;
        public $_use_db;
        public $_use_fb;

        public $_print_stats;

        private $_time_start;
        private $_time_end;

        public function __construct( $vars = null ) {
            // get the start time
            $this->_time_start = microtime(true);
            
            // init the DB if we're going to use it
            if($this->_use_db) $this->init_db();

            // get the base URL requested
            $this->_base_url = $this->get_base_url();
            @list($this->_route,$this->_query) = explode('?',trim(str_replace($this->get_base_directory(), '', $_SERVER['REQUEST_URI']), '/'));
            $this->_full_url = $this->_base_url.$this->_route;

            // check for user authentication
            if(isset($_SESSION['user'])) {
                $model_user = $this->new_model('model_user');
                if(isset($_SESSION['user']['user_id'])) {
                    $user_id = $_SESSION['user']['user_id'];
                    $user = $model_user->get_user_by_id($user_id);
                    if($user!==false) {
                        $this->_vars['auth_user'] = $user;
                        $this->_authenticated = true;
                    }
                }
            }
            
            // send off the cache headers if specified
            if($this->_no_cache) $this->no_cache();
            
        }
        
        public function __destruct() {
            $this->_time_end = microtime(true);
            $time_took = $this->_time_end - $this->_time_start;

            if($this->_print_stats) {
                echo '<span class="app_stats">class('.get_parent_class($this).'->'.get_class($this).')';
                echo ' memory('.$this->memory_usage_format().')';
                echo ' time('.$time_took.'sec)</span>';
            }
        }

        /* model inclusion helper */
        public function new_model($model_name) {
            if( @require_once(MODELS.$model_name.'.php') ) {
                // start up the model object
                $model_controller = new $model_name($this->_database);
                return $model_controller;
             } else {
                echo '<h1> controller does not exist </h1><pre>';
                print_r($info);
                echo '</pre>';
            }
        }
        
        /* database functions */
        public function init_db() {
            @require_once(FRAMEWORK.'class_database.php');
            $this->_database = new database(constant('db_user'),
                                            constant('db_password'),
                                            constant('db_host'));
            $this->_database->connect(constant('db_database'));
        }

        /* simple viewless rendering functions */
        public function render_json($vars) {
             $data = json_encode($vars);
            // header no cache
            header('Cache-Control: no-cache, must-revalidate'); // HTTP/1.1
            header('Expires: Sat, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            if(array_key_exists('callback', $_GET)){
                // header type javascript
                header('Content-Type: text/javascript; charset=utf8');
                $callback = $_GET['callback'];
                echo $callback.'('.$data.');';
            }else{
                // header type json
                header('Content-Type: application/json; charset=utf8');
                echo $data;
            }
            $this->_print_stats = false;
        }
        /* end simple viewless rendering functions */

        /* other helper functions */
        public function get_query( $match ) {
            if(isset($_GET[$match])) return $_GET[$match];
            else return false;
        }

        public function get_post( $match ) {
            if(isset($_POST[$match])) return $_POST[$match];
            else return false;
        }
        
        public function end() {
            exit();
        }
        
        function get_base_host() {
            $protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https://' : 'http://';
            $host = $_SERVER['HTTP_HOST'];
            return $protocol . $host;
        }
        
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

        public function no_cache() {
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT"); 
            header("Cache-Control: no-store, no-cache, must-revalidate"); 
            header("Cache-Control: post-check=0, pre-check=0", false);
            header("Pragma: no-cache");
        }

        public function memory_usage_format() { 
            $mem_usage = memory_get_peak_usage(true); 
            
            if ($mem_usage < 1024) 
                $reply = $mem_usage." B"; 
            elseif ($mem_usage < 1048576) 
                $reply = round($mem_usage/1024,2)." KB"; 
            else 
                $reply = round($mem_usage/1048576,2)." MB"; 
                
            return $reply;
        } 

        public function get_crypto_token($length){
            $token = "";
            // $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $codeAlphabet = "AaBbCcDdFfGgHhJjKkMmNnPpRrSsTtUuWwXxYyZz";
            $codeAlphabet = "ABCDFGHJKMNPRSTUWXYZ";
            $codeAlphabet.= "0123456789";
            for($i=0;$i<$length;$i++){
                $token .= $codeAlphabet[$this->crypto_rand_secure(0,strlen($codeAlphabet))];
            }
            return $token;
        }
        public function crypto_rand_secure($min, $max) {
            $range = $max - $min;
            if ($range < 0) return $min; // not so random...
            $log = log($range, 2);
            $bytes = (int) ($log / 8) + 1; // length in bytes
            $bits = (int) $log + 1; // length in bits
            $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
            do {
                $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
                $rnd = $rnd & $filter; // discard irrelevant bits
            } while ($rnd >= $range);
            return $min + $rnd;
        }
    }
    
    
    
