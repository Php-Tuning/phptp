<?php
class phptp {
	private $debug = 0;
	private $modules_required;
	private $modules_file;
	public $module;

	function __construct() {
		$this->set_module_requirement('phptp', false);
		$this->set_module_requirement('db', false);
		$this->set_module_file('db', 'db.class.php');
		$this->set_module_requirement('fileoperations', false);
		$this->set_module_file('fileoperations', 'fileoperations.class.php');
		$this->set_module_requirement('caching', array('fileoperations'));
		$this->set_module_file('caching', 'caching.class.php');
		$this->set_module_requirement('redirect_cache', array('caching', 'fileoperations'));
		$this->set_module_file('redirect_cache', 'redirect_cache.class.php');
		$this->set_debug();
		$this->check_requirements();
	}

	public function set_debug($debug = 0){
		if ($debug === true){
			$this->debug = 1;
		}elseif ($debug === false){
			$this->debug = 0;
		}elseif ($debug != '' && ($debug == 0 || $debug == 1)){
			$this->debug = $debug;
		}

    }

    public function get_debug(){
    	return $this->debug;
    }

    public function load_module($module){
		if (!isset($this->module[$module])){
			if (isset($this->modules_required[$module]) && $this->modules_required[$module] != '' && $this->modules_required[$module] != array()){
				foreach ($this->modules_required[$module] as $tmpmodule){
					$this->load_module($tmpmodule);
				}
			}
			require_once($this->modules_file[$module]);
			$this->module[$module] = new $module;
			return 1;
		}else{
			return 0;
		}
	}

	function set_module_requirement($module, $requirements){
		$this->modules_required[$module] = $requirements;
		$this->check_requirements();
	}

	private function set_module_file($module, $file){
		$this->modules_file[$module] = 'phptp/'.$file;
	}

	function check_requirements(){
		// Todo Check: Alle benötigten Module geladen?
	}

	public function throw_exception($error_subject, $error_message=''){
		if ($error_message == ''){
			die('<h2>Uncommon Error:</h2>'.$error_subject);
		}else{
			die('<h2>'.$error_subject.':</h2>'.$error_message);
		}
	}

}