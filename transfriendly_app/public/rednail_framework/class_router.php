<?php

    class router {
        
        private $_routes = array();
        
        /**
         * Builds a collection of internal URL's to look for
         * @param type $uri
         */
        public function add( $url, $controller = null, $method = null ) {
            $url = trim($url, '/');
            $path = $this->get_url_components($url);
            $new_route = $this->build_url_array( $path, $controller, $method );
            $this->_routes = array_merge_recursive($this->_routes, $new_route);
        }
        
        /*
         * generates the internal storage format for the route
         */
        private function build_url_array( $path, $controller, $method ) {
            $temp = array_reverse($path['components']);
            $array = array('controller' => $controller,
                           'method' => $method,
                           'vars' => $path['vars'],
                           'path' => $path['path'],
                           );
            foreach($temp as $item) {
                $new = array();
                $new[$item] = $array;
                $array = $new;
            }
            return $array;
        }
        
        /*
         * tool to extract the components of a url
         */
        private function get_url_components($url, $with_vars = true) {
            $vars = array();
            $components = array();
            
            @list($url_path, $query) = explode('?',$url);
            $path_elements = explode('/',$url_path);
            foreach($path_elements as $element) {
                if(substr($element,0,1) == ':') $vars[] = trim(substr($element,1));
                else $components[] = trim(strtolower($element));
            }
            $path = implode('/',$components);

            if(!empty($query)) {
                $query_list = explode('&',$query);
                foreach($query_list as $query_var) {
                    @list($var,$contents) = explode('=',$query_var);
                    $vars[$var] = $contents;
                }
            }
            // if($with_vars) return array('components' => $components, 'vars' => $vars, 'path' => $path);
            // else return array('components' => $components);
            return array('components' => $components, 'vars' => $vars, 'path' => $path);

        }
        
        /**
         * Makes the thing run!
         */
        public function submit(){
            $base_url = dirname($_SERVER['SCRIPT_NAME']);
            $request_url = trim(str_replace($base_url, '', $_SERVER['REQUEST_URI']), '/');
            $request = $this->get_url_components($request_url, false);
            
            $path = $this->_routes;
            
            $fail = 0;
            foreach($request['components'] as $element) {
                if(isset($path[$element])) {
                    $path = $path[$element];
                }
            }

            if(isset($path['controller']) && isset($path['method']) && isset($path['path'])) {
                // didn't get path
                $controller = $path['controller'];
                $method = $path['method'];
                
                $vars_path = trim(str_replace($path['path'], '', $request_url),'/');
                
                $vars = array();
                $vars_raw = array();
                if(strlen(trim($vars_path))>0) {
                    // seperate out query from URL Path
                    @list($vars_path,$query) = explode('?',$vars_path);
                    $vars_raw = array_filter(explode('/',$vars_path));
                    if(isset($path['vars'])) {
                        foreach($path['vars'] as $index => $key) {
                            if(isset($vars_raw[$index]) && trim($vars_raw[$index]) != '') {
                                $vars[$key] = $vars_raw[$index];
                            }
                        }
                    }
                }
                
                $info = array('controller' => $controller,
                              'method' => $method,
                              'vars' => $vars,
                              'vars_query' => $request['vars'],
                              'vars_post' => $_POST,
                              'vars_raw' => $vars_raw,
                              );
                
                // include the controller file
                if( @require_once(CONTROLLERS.$controller.'.php') ) {
                    // start up the controller object
                    $obj_controller = new $controller($info);
                    // call the method
                    if( method_exists( $obj_controller, $method ) ) {
                        call_user_func(array($obj_controller, $method), $info);
                    } else {
                        echo '<h1> method does not exist </h1><pre>';
                        print_r($info);
                        echo '</pre>';
                    }
                } else {
                    echo '<h1> controller does not exist </h1><pre>';
                    print_r($info);
                    echo '</pre>';
                }
            } else {
                echo '<h1> path not found</h1><pre>';
                print_r($request);
                echo '</pre>';
            }
        }
    }
    
    
    
