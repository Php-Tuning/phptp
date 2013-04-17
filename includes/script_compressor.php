<?php
/*
 * Created on 08.06.2009 by Schubert Media, Nico Schubert
 * Modificated 2009-2013 by PHP-Tuning.de, Andreas Hoehne
 */
$file_tmp = $_GET['img']; //evt. stripslashes, falls magic_quotes
$file_tmp = preg_replace('/images\/ .jpg/', 'images/+.jpg', $file_tmp);

include_once('script_compressor.config.php');

$file = "";
$chk = preg_replace($allowed_paths, '', $file_tmp);
if (!preg_match('/(^[\/])/', $file_tmp, $ret)) {
	$ret = preg_replace('/\?(.*)/', '', $ret);
	preg_match('/(.*)\.(jpg|jpeg|png|gif|ico|css|js)/', $file_tmp, $ret);
	$extension = '';
	if (isset($ret[2])){
		$extension = strtolower($ret[2]);
	}
  if ($extension == 'js' || $extension == 'css') {
    $offset = ((60 * 60 * 12));
  } else {
    $offset = ((60 * 60 * 24) * 365);
  }
  $mime = '';
  if (!isset($mimetypes[$extension])){
  	$mime = $mimetypes[$extension];
  }
  $file = "./" . $file_tmp; //pfad anpassen, wenn ntig.
}

if ( $file == '' || !@file_exists($file) ) {
  #header("Location: http://www.hosterplus.de/fehler404.php");
  header("HTTP/1.1 404 file not found");
  echo "404 - File not found";
} elseif ( $file != '' ) {
  $filedate = filectime($file); //letzte Änderung an der Datei
  $etag = strtolower(md5_file($file)); //md5-Hash der Datei als eindeutiger Etag

  $modified = true;

  if (isset ($_SERVER['HTTP_IF_NONE_MATCH'])) {
    $oldtag = trim(strtolower($_SERVER['HTTP_IF_NONE_MATCH']), '"');
    if ($oldtag == $etag) {
      $modified = false;
    } else {
      $modified = true;
    }
  }

  if (isset ($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
    #$olddate = date_parse(trim(strtolower($_SERVER['HTTP_IF_MODIFIED_SINCE'])));
    #$olddate = gmmktime($olddate['hour'],
    # $olddate['minute'],
    # $olddate['second'],
    # $olddate['month'],
    # $olddate['day'],
    # $olddate['year']);
    $olddate = strtotime(trim(strtolower($_SERVER['HTTP_IF_MODIFIED_SINCE'])));
    if ($olddate >= $filedate) {
      $modified = false;
    } else {
      $modified = true;
    }
  }

  if (!$modified) {
    if ($mime != ''){
      header("Content-Type: " . $mime);
    }
    header("Last-Modified: " . date('D, d M Y H:i:s', $filedate) . " GMT"); //Beispiel: Mon, 15 Sep 2008 17:46:02 GMT
    header('Etag: "' . $etag . '"');
    $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
    header($ExpStr);
    header("Cache-Control: max-age=" . $offset);
    header("HTTP/1.1 304 Not Modified");
  } else {
    #if ($mime != ''){
      header("Content-Type: " . $mime);
    #}
    header("Last-Modified: " . date('D, d M Y H:i:s', $filedate) . " GMT"); //Beispiel: Mon, 15 Sep 2008 17:46:02 GMT
    header('Etag: "' . $etag . '"');
    $ExpStr = "Expires: " . gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
    header($ExpStr);
    header("Cache-Control: max-age=" . $offset);
    ob_start("ob_gzhandler");
    readfile($file);
  }
}
?>