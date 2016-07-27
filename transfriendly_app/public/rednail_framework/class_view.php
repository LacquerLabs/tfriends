<?php
	class View {
        public $_authenticated;
        
		protected $variables = array();
        protected $_templates = array();
        
        protected $_header = null;
        protected $_footer = null;
        
		protected $_base_url = null;
        
        public $_menu;
        public $_breadcrumbs;

		function __construct( $template = null ) {
            if(isset($_SESSION['user'])) {
                $this->_authenticated = true;
            } else $this->_authenticated = false;
            $this->_base_url = $this->get_base_url();
			$this->addTemplate( $template );
			// $this->setHeader( 'layout/header.php' );
			// $this->setFooter( 'layout/footer.php' );
		}
        
        function addTemplate($template) {
        	if($template != null && trim($template)!='') {
        		$this->_templates[] = $template;
        	}
        }
        
        function setHeader($template) {
        	$this->_header = $template;
        }
        
        function setFooter($template) {
        	$this->_footer = $template;
        }
        
		/** Set Global Variables **/
		function setGlobals($array) {
			if(is_array($array)) {
				$this->variables = array_merge($this->variables,$array);
			}
		}
        
		/** Set Variables **/
		function setVariables($array) {
			if(is_array($array)) {
				$this->variables = array_merge($this->variables,$array);
			}
		}
        
		/** Display View **/
	    function render( $include_header_footer = true ) {
 			// Load view according to current controller and action
            if( !empty($this->_templates) ) {
				extract($this->variables);
                
 				if($include_header_footer && $this->_header != '') include (VIEWS . $this->_header);
                
 				foreach($this->_templates as $template) {
 					include (VIEWS.$template);
 				}
				
				if($include_header_footer && $this->_footer != '') include (VIEWS . $this->_footer);
            }
	    }
        
        function fetch($template = null, $array = null) {
            $renderResult = false;
            if( trim($template) !='' ) {
                if(is_array($array)) extract($array);
                ob_start();
                ob_implicit_flush(false);
                $result = include(FRAMEWORK . 'views/'.$template);
                $renderResult = ob_get_clean();
            }
            return $renderResult;
        }
        
        function insert($template = null, $array = null) {
            $renderResult = false;
            if( trim($template) !='' ) {
                if(is_array($array)) extract($array);
                ob_start();
                ob_implicit_flush(false);
                $result = include(FRAMEWORK . 'views/'.$template);
                $renderResult = ob_get_clean();
            }
            echo $renderResult;
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

        function determine_active($match, $content) {
            if($match == $content) return "active";
            else return "";
        }

        function breadcrumb_add($what, $url, $active = null) {
            $this->_breadcrumbs[] = array('what'=> $what, 'url' => $url, 'active' => ($active != null?'active':''));
        }
	}
