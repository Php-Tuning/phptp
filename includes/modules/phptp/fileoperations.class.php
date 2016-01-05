<?php

class fileoperations extends phptp{
	private $file_exists_check;
	private $dir_exists_check;

    function read_file($filename){
		return file_get_contents($filename);
    }

    function write_file_wmode($file, $content, $mode){
    	if ($mode == 'write'){
    		$mode = 'w';
    	}elseif($mode == 'append'){
    		$mode = 'a';
    	}
    	$f = fopen($file, $mode);
		$ret = fwrite($f, $content);
		fclose($f);
		return $ret;
    }

    function write_file($file, $content){
    	return fileoperations::write_file_wmode($file, $content, 'write');
    }

    function append_file($file, $content){
    	return fileoperations::write_file_wmode($file, $content, 'append');
    }

    function dir_create($dirname){
    	$ret = fileoperations::dir_exists($dirname);
    	if (!$ret){
    		$ret = mkdir($dirname);
    	}
    	return $ret;
    }

    function filetime($filename, $check = 1){
    	if ($check ==  1 && !isset($file_isset_check[$filename])){
    		if (fileoperations::file_exists($filename)){
    			return fileoperations::filetime($filename);
		    } else {
			    return false;
		    }
    	}else{
    		return filemtime($filename);
    	}
    }

    function filesize($filename, $check = 1){
    	if ($check ==  1 && !isset($file_isset_check[$filename])){
    		if (fileoperations::file_exists($filename)){
    			return fileoperations::filesize($filename);
		    } else {
			    return false;
		    }
    	}else{
    		return filesize($filename);
    	}
    }

    function file_exists($filename){
    	if (isset($this->file_exists_check[$filename])){
    		return $this->file_exists_check[$filename];
    	}else{
    		if (!$ret = is_file($filename)){
	    		$this->file_exists_check[$filename] = false;
	    		return false;
	    	}else{
	    		$this->file_exists_check[$filename] = true;
	    		return true;
	    	}
    	}
    }

    function dir_exists($dirname){
    	if ($ret = is_dir($dirname)){
    		$this->dir_exists_check[$dirname] = 1;
    		return $ret;
    	}else{
    		$this->dir_exists_check[$dirname] = $ret;
    		return $ret;
    	}
    }

}