<?php

	class model {

		protected $_database;

		function __construct(&$db) {
			$this->_database = $db;
		}

        public function escape($string) {
            $new_string = $string;
            // $new_string = $this->_database->escape($string);
            return $new_string;
        }

        public function sql_error() {
            if(app_debug) {
                $errors = $this->_database->get_errors();

                // echo '<script type="text/javascript" src="/javascript/javascript.prettyprint/prettify.js"></script>';
                // echo '<script type="text/javascript" src="/javascript/javascript.prettyprint/lang-sql.js"></script>';
                // echo '<link rel="stylesheet" type="text/css" href="/javascript/javascript.prettyprint/sunburst.css"/>';

                echo '<div style="background-color: Azure;border: 1px solid Lavender; ';
                echo 'padding: 8px;overflow: hidden;font-size: 12px;';
                echo 'font-family: sans-serif;margin: 4px 0px;-moz-border-radius: 8px;-webkit-border-radius: 8px;';
                echo '-o-border-radius: 8px;-ms-border-radius: 8px;-khtml-border-radius: 8px; border-radius: 8px;">';
                echo '<span style="font-size: 16px;font-weight: bold;color: maroon;">';
                echo 'MYSQL ERROR:';
                echo '</span>';
                echo '<table>';
                foreach($errors as $error) {
                    echo '<tr><td>';
                    echo '<span style="border: 1px solid lavender;';
                    echo 'background-color: lavenderblush;padding: 8px;margin: 8px 0px;';
                    echo 'display: block;';
                    echo '-moz-border-radius: 8px; -webkit-border-radius: 8px; ';
                    echo '-o-border-radius: 8px; -ms-border-radius: 8px; ';
                    echo '-khtml-border-radius: 8px; border-radius: 8px; ">';
                    echo $error['error'];
                    echo '</span>';
                    echo '<pre class="prettyprint lang-sql" style="background-color:#333;';
                    echo 'padding: 8px; display: block;word-wrap: break-word;font-size: 10px; color: white;';
                    echo '-moz-border-radius: 8px; -webkit-border-radius: 8px; ';
                    echo '-o-border-radius: 8px; -ms-border-radius: 8px; ';
                    echo '-khtml-border-radius: 8px; border-radius: 8px; ';
                    echo 'line-height: 15px;margin: 0px;">';
                    echo $error['data'];
                    echo '</pre>';
                    echo '<div style="clear:both;"></div>';
                    echo '</td></tr>';
                }
                echo '</table>';
                echo '<span style="padding: 0px;margin: 0px; margin-top: 8px;display: block;font-family: monospace;font-size: 10px;">';
                echo 'class('.get_parent_class($this).'->'.get_class($this).')';
                echo ' memory('.$this->memory_usage_format().')</span>';
                echo '</div>';
                
                /*
                echo '<script type="text/javascript" >';
                echo 'if(typeof add_pretty != "defined") {';
                echo 'console.log(1);';
                echo 'addEventListener("load", function (event) { prettyPrint() }, false);';
                echo 'var add_pretty = 1;';
                echo '}';
                echo '</script>';
                */

                echo '</div>';
            }
        }

        public function sql_insert( $sql ) {
            $result = $this->_database->insert( $sql );
            if($result === false) $this->sql_error();
            
            return $result;
        }
        
        public function sql_query( $sql ) {
            $result = $this->_database->query( $sql );
            if($result === false) $this->sql_error();
            
            return $result;
        }
        
        public function sql_select( $sql ) {
            $result = $this->_database->select( $sql );
            if($result === false) $this->sql_error();
            return $result;
        }

        public function sql_select_all( $sql ) {
            // this is an alias of sql_select
            return $this->sql_select( $sql );
        }

        public function sql_select_row( $sql ) {
            $result = $this->_database->select_first_row( $sql );
            if($result === false) $this->sql_error();
            return $result;
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

		function __destruct() {
			
		}
	}

