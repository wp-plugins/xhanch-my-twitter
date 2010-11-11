<?php
	$expires = 2592000;
	
	header("Content-Type: text/css");
	header("Pragma: public");
	header("Cache-Control: maxage=".$expires);
	header('Expires: '.gmdate('D, d M Y H:i:s', time()+$expires).' GMT');
	
	include_once('../../../../wp-config.php');
	include_once('../../../../wp-load.php');
	include_once('../../../../wp-includes/wp-db.php');

	function css_minify($v){
		$v = trim($v);
		$v = str_replace("\r\n", "\n", $v);
        $search = array("/\/\*[\d\D]*?\*\/|\t+/", "/\s+/", "/\}\s+/");
        $replace = array(null, " ", "}\n");
		$v = preg_replace($search, $replace, $v);
		$search = array("/\\;\s/", "/\s+\{\\s+/", "/\\:\s+\\#/", "/,\s+/i", "/\\:\s+\\\'/i", "/\\:\s+([0-9]+|[A-F]+)/i");
        $replace = array(";", "{", ":#", ",", ":\'", ":$1");
        $v = preg_replace($search, $replace, $v);
        $v = str_replace("\n", null, $v);
    	return $v;	
  	}
	
	$css = css_minify(file_get_contents("css.css"));
	
	$xmt_accounts = get_option('xmt_accounts');
	if($xmt_accounts === false)
		$xmt_accounts = array();	
	if(!is_array($xmt_accounts))
		$xmt_accounts = array();
	
	$profiles = array_keys($xmt_accounts);
	foreach($profiles as $profile)
		echo str_replace('{xmt_id}', '#xmt_'.$profile.'_wid', $css);
?>