<?php
	if(!defined('xmt'))
		exit;
		
	wp_enqueue_script('jquery');
	wp_enqueue_script('xmt_innerfade', xmt_get_dir('url').'/js/innerfade.js', array('jquery'));

	$tpl_cfg = array(
		'thm_sld_int' => 2000
	);
?>