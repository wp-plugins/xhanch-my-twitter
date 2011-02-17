<?php
	if(!defined('xmt'))
		exit;

	global $wpdb;
	global $xmt_cfg_def;
				
	$ver = get_option('xmt_vsn');
	if(!$ver){
		$sql = '
			create table if not exists '.$wpdb->prefix.'xmt(
				id int(11) not null auto_increment,
				nme varchar(100) not null,
				cfg longblob not null,
				twt_cch longblob not null,
				twt_cch_dtp bigint(20) not null default \'0\',
				prf_cch longblob not null,
				prf_cch_dtp bigint(20) not null default \'0\',
				primary key (id),
				unique key nme_unique (nme)
			)
		';
		$wpdb->query($sql);

		$ver = '1.0.0';
		update_option('xmt_vsn', $ver);
	}

	$acc_lst = xmt_acc_lst();	
	foreach($acc_lst as $acc){
		$xmt_cfg = xmt_acc_cfg_get($acc);
		$xmt_cfg = array_merge($xmt_cfg_def, $xmt_cfg);
		xmt_acc_cfg_upd($acc, $xmt_cfg);
	}

	if(count($acc_lst) == 0)
		xmt_acc_add('Primary', $xmt_cfg_def);	
?>