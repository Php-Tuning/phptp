<?php
class db extends phptp {
	private $db_type;

	private function db($db_type = '') {
		if ($db_type != ''){
			$this->set_db_type($db_type);
		}
	}

	function detect_connection($possible_connection = '', $run = 0){
		if (is_array($possible_connection) && count($possible_connection)){
    		$array_count = count($possible_connection);
    		$count = 0;
			foreach($possible_connection as $tmp){
				$count++;
				if ($count == $array_count){
					return $tmp;
				}
    		}
    	}elseif ($possible_connection != ''){
			return $possible_connection;
    	}else{
    		$run++;
    		$this->define_connection($this->get_connections(), $run);
    		if ($run == 0){
    			$this->throw_exception('DB -> Critical Error', 'No Database Connection found');
    		}
    	}
    }

    public function set_db_type($db_type = 'db_mysql') {
		$this->set_requirement('db_mysql', phptp::true_false_define(false));
		$this->modules_file('db_mysql', 'db/db_mysql.class.php');
		$this->db_type = $db_type;
		$this->load_module($db_type);
	}

	public function check_type(){
		if (!isset($this->db_type) || $this->db_type == '' || !isset($this->modules[$this->db_type])){
			$this->throw_exception('DB -> Critical Error', 'Database Type not specified');
		}
	}
}
?>