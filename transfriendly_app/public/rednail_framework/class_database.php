<?php
    # Really Simple DataBase Object
    
    class database {
        private	$_DBuser = '';
        private	$_DBpass = '';
        private	$_DBwriteHost = '';
        private	$_DBreadHost = '';
        private	$_DBdb = '';
        
        private $_DBwriteLink = '';
        private	$_DBreadLink = '';
        
        private $_errors = array();
        
        public function __construct( $username, $password, $dbwritehost, $dbreadhost = null ) {
            if( ( $username === null ) || ( trim( $username ) == '' ) ) $this->doerror( 'empty username' );
            if( ( $password === null ) || ( trim( $password ) == '' ) ) $this->doerror( 'empty password' );
            if( ( $dbwritehost === null ) || ( trim( $dbwritehost ) == '' ) ) $this->doerror( 'empty database host' );
            if( ( $dbreadhost === null ) || ( trim( $dbreadhost ) == '' ) ) $dbreadhost = $dbwritehost;
            
            $this->_DBuser = $username;
            $this->_DBpass = $password;
            $this->_DBwriteHost = $dbwritehost;
            $this->_DBreadHost = $dbreadhost;
        }
        
        public function __destruct( ) {
            $this->disconnect( );
        }
        
        public function connect( $database = '' ) {
            $this->_DBwriteLink =   mysqli_connect( $this->_DBwriteHost, $this->_DBuser, $this->_DBpass ) 
                                    or $this->add_error( $this->_DBwriteLink->connect_errno, $this->_DBwriteLink->connect_error );

            if( $this->_DBreadHost != $this->_DBwriteHost ) {
                $this->_DBreadLink =    mysqli_connect( $this->_DBreadHost, $this->_DBuser, $this->_DBpass )
                                        or $this->add_error( $this->_DBreadLink->connect_errno, $this->_DBreadLink->connect_error );
            } else {
                $this->_DBreadLink = $this->_DBwriteLink;
            }
            if( ( $database !== null ) && ( trim( $database ) != '') ) $this->usedb( $database );
        }
        
        public function disconnect( ) {
            $this->_DBwriteLink = mysqli_close( $this->_DBwriteLink );
            if( $this->_DBreadHost != $this->_DBwriteHost ) {
                $this->_DBreadLink = mysqli_close( $this->_DBreadLink );
            }
        }
        
        public function usedb( $database = '' ) {
            if( ( $database !== null ) && ( trim( $database ) != '') ) {
                $this->_DBdb = $database;
                mysqli_select_db( $this->_DBwriteLink, $this->_DBdb ) 
                or $this->add_error( $this->_DBwriteLink->errno, $this->_DBwriteLink->error );
                if( $this->_DBreadHost != $this->_DBwriteHost ) {
                    mysqli_select_db( $this->_DBreadLink, $this->_DBdb ) 
                    or $this->add_error( $this->_DBreadLink->errno, $this->_DBreadLink->error );
                }
            }
        }
        
        /*
        * insert
        *
        * returns
        *   id of row inserted if success (if table does not have id then returns true)
        *   false on error
        */
        public function escape( $string ) {
            $string = $this->_DBwriteLink->real_escape_string($string);
            return $string;
        }
        
        /*
         * query
         *
         * returns
         *   true on sucess
         *   false on error
         */
        public function query( $SQL ) {
            error_log($SQL);
            if ($result = $this->_DBwriteLink->query( $SQL ) ) {
                $result = true;
            } else {
                $this->add_error( $this->_DBwriteLink->errno, $this->_DBwriteLink->error, $SQL);
                $result=false;
            }
            return $result;
        }
        

        /*
        * insert
        *
        * returns
        *   id of row inserted if success (if table does not have id then returns true)
        *   false on error
        */
        public function insert( $SQL ) {
            error_log($SQL);

            if ($result = $this->_DBwriteLink->query( $SQL ) ) {
                $id = $this->_DBwriteLink->insert_id;
                if($id==0) $id = true;
            } else {
                $this->add_error( $this->_DBwriteLink->errno, $this->_DBwriteLink->error, $SQL);
                $id=false;
            }
            return $id;
        }
        
        /*
        * select 
        *
        * returns 
        *   error returns FALSE
        *   multiple rows returns dimensioned array of associative arrays containing results
        *   no results returns 0
        *
        */
        public function select( $SQL ) {
            error_log($SQL);

            if ($result = $this->_DBreadLink->query( $SQL, MYSQLI_USE_RESULT)) {
                while ($row = $result->fetch_assoc()) {
                    $results[] = $row;
                }
                $result->close();
                if(isset($results) && !empty($results)) {
                    // if(count($results)==1) return $results[0];
                    // else return $results;
                    return $results;
                } else return 0;
            } else {
                $this->add_error( $this->_DBwriteLink->errno, $this->_DBwriteLink->error, $SQL);
                return false;
            }
        }

        /*
        * select first row
        *
        * returns 
        *   error returns FALSE
        *   multiple rows returns associative array of first row
        *   no results returns 0
        *
        */
        public function select_first_row( $SQL ) {
            error_log($SQL);

            if ($result = $this->_DBreadLink->query( $SQL, MYSQLI_USE_RESULT)) {
                $results = $result->fetch_assoc();
                $result->close();
                if(isset($results) && !empty($results)) {
                    // if(count($results)==1) return $results[0];
                    // else return $results;
                    return $results;
                } else return 0;
            } else {
                $this->add_error( $this->_DBwriteLink->errno, $this->_DBwriteLink->error, $SQL);
                return false;
            }
        }
        
        private function add_error( $errornum, $errorstring, $data = null ) {
            $this->_errors[] = array( 'timestamp' => time(), 
                                        'id' => $errornum, 
                                        'error' => $errorstring, 
                                        'data' => $data);
        }
        
        public function get_errors( ) {
            if ( isset($this->_errors) && is_array($this->_errors) && count($this->_errors) > 0 ) {
                $temp = $this->_errors;
                unset($this->_errors);
                return $temp;
            } else return FALSE;
        }
        
        public function mysql_escape_mimic($inp) { 
            if(is_array($inp)) 
                return array_map(__METHOD__, $inp); 
            
            if(!empty($inp) && is_string($inp)) { 
                return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $inp); 
            } 
            
            return $inp; 
        }

    }
    

