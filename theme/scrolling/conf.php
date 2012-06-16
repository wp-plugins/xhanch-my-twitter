<?php
	if(!defined('xmt'))
		exit;
		
	wp_enqueue_script('jquery');
	wp_enqueue_script('xmt_marquee', xmt_get_dir('url').'/js/marquee.js', array('jquery'));

	$tpl_cfg = array(
		'thm_scr_szh' => 200,
		'thm_scr_anm' => 0,
		'thm_scr_anm_dir' => 'up',
		'thm_scr_anm_amt' => 1,
		'thm_scr_anm_dly' => 50
	);
?>