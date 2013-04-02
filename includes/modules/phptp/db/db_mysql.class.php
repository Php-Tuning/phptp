<?php

class db_mysql extends db {
	private $query_result;
	private $connection = array();
	private $connection_type = array();

    function db_mysql() {

    }

    function __construct(){
    	$this->set_requirement('db', 'mysql');
    }

    function set_connections($connections, $connection_types = ''){
		$this->connection = $connections;
		if (is_array($connections)){
			foreach ($connections as $tmpkey => $tmpval){
				if (!isset($connection_types[$tmpkey]) || $connection_types[$tmpkey] == ''){
					$this->connection_type[$tmpkey] = 'normal';
				}else{
					$this->connection_type[$tmpkey] = $connection_types[$tmpkey];
				}
			}
		}
    }

    function get_connections(){
		return $this->connection;
    }

    function get_connection_types(){
		return $this->connection_type;
    }

    public function db_connect($db_host, $db_user, $db_password, $connecttype='normal'){
		$this->connection[] = mysql_connect($db_host, $db_user, $db_password);
		$this->connection_type[] = $connecttype;
	}

	public function db_query($query, $con = ''){
		return $this->query_result = mysql_query($query, $this->detect_connection($con));
	}

	public function db_num_rows($result = '', $con = ''){
		if ($result == ''){
			$result = $this->query_result;
		}
		return mysql_num_rows($result, $this->detect_connection($con));
	}

	public function db_fetch_array($result = '', $con = ''){
		if ($result == ''){
			$result = $this->query_result;
		}
		return mysql_fetch_array($result, $this->detect_connection($con));
	}

	public function db_real_escape_string($variable, $con = ''){
		return mysql_real_escape_string($variable, $this->detect_connection($con));
	}
}
?>