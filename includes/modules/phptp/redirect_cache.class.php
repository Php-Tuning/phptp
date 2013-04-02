<?php

/**
 * Redirect Cache Klasse
 * Autor: Andreas Höhne
 * 		  Webdesign Höhne
 *
 * Feel free to use this Code on your website, reselling is not allowed
 *
 * Aufruf:
 include ('includes/modules/redirect_cache.class.php');
 $redirect_cache = new redirect_cache;
 $redirect_cache->set_cache_folder('writeperm/redirect_cache/');
 $redirect_cache->set_cache_time(60*60*24);
 #$redirect_cache->set_debug(1);
 $redirect_cache->do_redirect();

 * Cache schreiben:
 $redirect_cache->write_cache($target_url);

 */

class redirect_cache extends phptp{
	private $defect = 0;
	private $cache_folder;
	private $request_domain;
	private $request_path;
	private $request_hash_subfolder;
	private $request_hash_filename;
	private $cachefile;
	private $cache_time;
	private $redirect_type = 'permanent';
	private $hash_requested = FALSE;

    function redirect_cache() {
    	$this->request_domain = $_SERVER['SERVER_NAME'];
    	$this->request_path = $_SERVER['REQUEST_URI'];
    	$this->set_debug();
    	$this->set_cache_folder();
    	$this->set_cache_time();
    	$this->set_redirect_type();
    }

    function set_cache_folder($folder = 'writeperm/redirect_cache/'){
    	#if (is_dir($folder)){
    		// 2do right check
    		$this->cache_folder = $folder;
    	#}else{
    	#	$this->defect = 1;
    	#}
    }

    function set_cache_time($cachetime = 2678400){
    	$this->cache_time = $cachetime;
    }

    function set_redirect_type($redirect_type = 'permanent'){
    	$this->redirect_type = $redirect_type;
    }

    function critical_error($type='redir'){
    	if ($this->debug == 1){
    		if ($type == 'redir'){
    			die('Kritischer Umleitungsfehler, bitte besuchen Sie unsere Hauptseite: ' .
    			'<a href="http://'.$_SERVER['SERVER_NAME'].'/">'.$_SERVER['SERVER_NAME'].'</a>');
    		}elseif ($type == 'defect'){
    			die('Variablen defekt gesetzt');
    		}
    	}
    }

    function write_cache($target){
    	if ($this->defect != 1){
    		$this->request_hash();
	    	if ($target != 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']){
	    		$fo = $this->modules['fileoperations'];
	    		if (!$fo->dir_exists($this->cache_folder.$this->request_hash_subfolder)){
		    		$fo->dir_create($this->cache_folder.$this->request_hash_subfolder);
		    	}
		    	$fo->write_file($this->cachefile, $target);
	    	}else{
	    		$this->critical_error();
	    	}
    	}elseif ($this->defect == 1){
    		$this->critical_error('defect');
    	}
    }

    function check_file(){
		$fo = $this->modules['fileoperations'];
		if ($fo->dir_exists($this->cache_folder.$this->request_hash_subfolder)){
			if ($fo->file_exists($this->cachefile)){
				if ($fo->filetime($this->cachefile) >= ( time() - $this->cache_time )
				&& $fo->filesize($this->cachefile) >= 1){
					#if ($this->debug==1)die('Dateigrösse und Zeit stimmt');
					return TRUE;
				}else{
					#if ($this->debug==1)die('Dateigrösse und Zeit stimmt nicht');
				}
			}else{
				#if ($this->debug==1)die('Datei nicht vorhanden');
			}
		}else{
			#if ($this->debug==1)die('Verzeichnis nicht vorhanden');
		}
		return FALSE;
    }

    function request_hash(){
    	if ($this->hash_requested == FALSE){
    		$this->request_hash_subfolder = md5($this->request_domain);
	    	$this->request_hash_filename = md5($this->request_path).'.url';
	    	$this->cachefile = $this->cache_folder.
	    		$this->request_hash_subfolder.
				'/'.$this->request_hash_filename;
			$this->hash_requested = TRUE;
    	}
    }

    function do_redirect(){
    	if ($this->defect != 1){
    		$this->request_hash();
	    	if ($this->check_file() == TRUE){
	    		$target_url = $this->read_file($this->cachefile);
	    		if ($target_url != ''){
	    			if ($this->redirect_type == 'permanent' || $this->redirect_type == 301){
			    		header("HTTP/1.1 301 Moved Permanently");
			    	}
				    header("Location: ".$target_url);
				    die();
	    		}else{
	    			$this->critical_error();
	    		}
	    	}else{
	    		#if ($this->debug==1)die('Check_File stimmt nicht');
	    	}
    	}
    }

}
?>