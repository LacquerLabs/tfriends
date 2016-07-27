<?php
    /**
     * A PHP session handler to keep session data within a MySQL database
     */
    
    class my_session_handler {
        
        /**
         * a database MySQLi connection resource
         * @var resource
         */
        protected $dbConnection;
        
        /**
         * the name of the DB table which handles the sessions
         * @var string
         */
        protected $dbTable;
        
        /**
         * Set db data if no connection is being injected
         * @param   string  $dbHost
         * @param   string  $dbUser
         * @param   string  $dbPassword
         * @param   string  $dbDatabase
         */
        public function set_connection($dbHost, $dbUser, $dbPassword, $dbDatabase){
            // error_log('session handler connect');
            //create db connection
            $this->dbConnection = new mysqli($dbHost, $dbUser, $dbPassword, $dbDatabase);
            //check connection
            if (mysqli_connect_error()) {
                error_log('session handler CONNECT ERROR:'.$this->dbConnection->error);
                return false;
            }
        }        
        
        
        /**
         * Inject DB connection from outside
         * @param   object  $dbConnection   expects MySQLi object
         */
        public function set_mysqli($dbConnection){
            $this->dbConnection = $dbConnection;
        }
        
        
        /**
         * Inject DB connection from outside
         * @param   object  $dbConnection   expects MySQLi object
         */
        public function set_session_table($dbTable){
            $this->dbTable = $dbTable;
        }
        
        
        /**
         * Open the session
         * @return bool
         */
        public function open() {
            // Open up the session handler
            // Only uncomment this if it is not being done to your satisfaction by
            // PHPs built in garbage collection
            // $limit = time() - (3600 * 24);
            // $sql = sprintf("DELETE FROM %s WHERE timestamp < %s", $this->dbTable, $limit);
            // error_log('session handler OPEN:'.$sql);
            // if ($result = $this->dbConnection->query($sql)) {
            //     return $result;
            // } else {
            //     error_log('session handler OPEN ERROR:'.$this->dbConnection->error);
            //     return false;
            // }
            return true;
        }
        
        /**
         * Close the session
         * @return bool
         */
        public function close() {
            // error_log('session handler CLOSE');
            return $this->dbConnection->close();
        }
        
        /**
         * Read the session
         * @param int session id
         * @return string string of the sessoin
         */
        public function read($id) {
            $sql = sprintf("SELECT data FROM %s WHERE id = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
            // error_log('session handler READ:'.$sql);
            if ($result = $this->dbConnection->query($sql)) {
                if ($result->num_rows && $result->num_rows > 0) {
                    $record = $result->fetch_assoc();
                    return $record['data'];
                } else {
                    // error_log('session handler READ ERROR:'.$this->dbConnection->error);
                    return false;
                }
            } else {
                error_log('session handler READ ERROR:'.$this->dbConnection->error);
                return false;
            }
            return true;
        }
        
        
        /**
         * Write the session
         * @param int session id
         * @param string data of the session
         */
        public function write($id, $data) {
            $sql = sprintf("REPLACE INTO %s VALUES('%s', '%s', '%s')",
                           $this->dbTable,
                           $this->dbConnection->escape_string($id),
                           $this->dbConnection->escape_string($data),
                           time());
            // error_log('session handler WRITE:'.$sql);
            if ($result = $this->dbConnection->query($sql)) {
                return $result;
            } else {
                error_log('session handler WRITE ERROR:'.$this->dbConnection->error);
                return false;
            }
        }
        
        /**
         * Destoroy the session
         * @param int session id
         * @return bool
         */
        public function destroy($id) {
            $sql = sprintf("DELETE FROM %s WHERE `id` = '%s'", $this->dbTable, $this->dbConnection->escape_string($id));
            // error_log('session handler DESTROY:'.$sql);
            if ($result = $this->dbConnection->query($sql)) {
                return $result;
            } else {
                error_log('session handler DESTROY ERROR:'.$this->dbConnection->error);
                return false;
            }
        }
        
        
        
        /**
         * Garbage Collector
         * @param int life time (sec.)
         * @return bool
         * @see session.gc_divisor      100
         * @see session.gc_maxlifetime 1440
         * @see session.gc_probability    1
         * @usage execution rate 1/100
         *        (session.gc_probability/session.gc_divisor)
         */
        public function gc($max) {
            $sql = sprintf("DELETE FROM %s WHERE `timestamp` < '%s'", $this->dbTable, time() - intval($max));
            // error_log('session handler GARBAGE COLLECTION:'.$sql);
            if ($result = $this->dbConnection->query($sql)) {
                return $result;
            } else {
                error_log('session handler GARBAGE COLLECTION ERROR:'.$this->dbConnection->error);
                return false;
            }
        }
    }
