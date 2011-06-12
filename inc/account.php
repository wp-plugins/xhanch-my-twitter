<?php
	if(!defined('xmt'))
		exit;

	function xmt_acc_lst(){
		global $wpdb;
		$res = array();

		$sql = '
			select nme
			from '.$wpdb->prefix.'xmt_acc
			order by nme
		';
		$rs = $wpdb->get_results($sql, ARRAY_A);
		if($rs){
			foreach ($rs as $row)
				$res[] = $row['nme'];
		}

		return $res;
	}

	function xmt_acc_add($acc, $cfg){
		global $wpdb;
		$sql = '
			insert into '.$wpdb->prefix.'xmt_acc(
				nme,
				cfg,
				twt_cch,
				twt_cch_dtp,
				prf_cch,
				prf_cch_dtp,
				las_twt_imp_dtp						
			)values(
				'.xmt_sql_str($acc).',
				'.xmt_sql_str(serialize($cfg)).',
				'.xmt_sql_str('').',
				0,
				'.xmt_sql_str('').',
				0,
				0
			)
		';
		 $wpdb->query($sql);
	}

	function xmt_acc_del($acc){
		global $wpdb;

		$sql = '
			delete from '.$wpdb->prefix.'xmt_twt
			where acc_nme = '.xmt_sql_str($acc).'
		';
		 $wpdb->query($sql);

		$sql = '
			delete from '.$wpdb->prefix.'xmt_acc
			where nme = '.xmt_sql_str($acc).'
		';
		 $wpdb->query($sql);
	}

	function xmt_acc_ifo_get($acc, $ifo){
		global $wpdb;

		$sql = '
			select '.$ifo.'
			from '.$wpdb->prefix.'xmt_acc
			where nme = '.xmt_sql_str($acc).'
		';
		return $wpdb->get_var($sql);
	}

	function xmt_acc_cfg_get($acc){
		global $wpdb;
		$res = array();

		$sql = '
			select cfg
			from '.$wpdb->prefix.'xmt_acc
			where nme = '.xmt_sql_str($acc).'
		';
		$rs = $wpdb->get_results($sql, ARRAY_A);
		if($rs){
			foreach ($rs as $row)
				$res = unserialize($row['cfg']);			
		}

		return $res;
	}

	function xmt_acc_cfg_upd($acc, $cfg){
		global $wpdb;
		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set cfg = '.xmt_sql_str(serialize($cfg)).'
			where nme = '.xmt_sql_str($acc).'
		';
		$wpdb->query($sql);
	}

	function xmt_twt_cch_rst($acc){
		global $wpdb;
		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set
				twt_cch = '.xmt_sql_str('').',
				twt_cch_dtp = 0,
				las_twt_imp_dtp = 0
			where nme = '.xmt_sql_str($acc).'
		';
		 $wpdb->query($sql);
	}

	function xmt_twt_cch_get($acc){
		global $wpdb;
		$res = array(
			'dat' => array(),
			'tmd' => 0
		);

		$sql = '
			select 
				twt_cch,
				twt_cch_dtp
			from '.$wpdb->prefix.'xmt_acc
			where nme = '.xmt_sql_str($acc).'
		';
		$rs = $wpdb->get_results($sql, ARRAY_A);
		if($rs){
			foreach($rs as $row){
				$res['dat'] = unserialize($row['twt_cch']);	
				$res['tmd'] = intval($row['twt_cch_dtp']);	
			}
		}

		return $res;
	}

	function xmt_twt_cch_set($acc, $dat){
		global $wpdb;

		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set 
				twt_cch = '.xmt_sql_str(serialize($dat)).',
				twt_cch_dtp = '.xmt_sql_int(time()).'
			where nme = '.xmt_sql_str($acc).'
		';
		$wpdb->query($sql);
	}

	function xmt_prf_cch_rst($acc){
		global $wpdb;
		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set
				prf_cch = '.xmt_sql_str('').',
				prf_cch_dtp = 0
			where nme = '.xmt_sql_str($acc).'
		';
		 $wpdb->query($sql);
	}

	function xmt_prf_cch_get($acc){
		global $wpdb;
		$res = array(
			'dat' => array(),
			'tmd' => 0
		);

		$sql = '
			select 
				prf_cch,
				prf_cch_dtp
			from '.$wpdb->prefix.'xmt_acc
			where nme = '.xmt_sql_str($acc).'
		';
		$rs = $wpdb->get_results($sql, ARRAY_A);
		if($rs){
			foreach($rs as $row){
				$res['dat'] = unserialize($row['prf_cch']);	
				$res['tmd'] = intval($row['prf_cch_dtp']);	
			}
		}

		return $res;
	}

	function xmt_prf_cch_set($acc, $dat){
		global $wpdb;

		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set 
				prf_cch = '.xmt_sql_str(serialize($dat)).',
				prf_cch_dtp = '.xmt_sql_int(time()).'
			where nme = '.xmt_sql_str($acc).'
		';
		$wpdb->query($sql);
	}
?>