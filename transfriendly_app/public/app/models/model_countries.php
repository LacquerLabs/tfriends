<?php
    
	class model_countries extends model {
        
        public function get_country_by_name($country_name) {
            $country_name = $this->escape($country_name);
            
            $sql = "SELECT * FROM country WHERE short_name = '{$country_name}'";
            $data = $this->sql_select_row( $sql );
			if($data!==false && $data!=0) return $data;
            else return false;
        }
        
        public function get_country_by_id($country_id) {
            $country_id = intval($country_id);
            
            $sql = "SELECT * FROM country WHERE country_id = '{$country_id}'";
            $data = $this->sql_select_row( $sql );
			if($data!==false && $data!=0) return $data;
            else return false;
        }
        
        public function get_all_countries( ) {
            $sql = "select * from country";
            $data = $this->sql_select( $sql );
			if($data!==false && $data!=0) return $data;
            else return false;
        }
    }
    
