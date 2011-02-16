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
				twt_cch longblob not null default \'\',
				twt_cch_dtp bigint(20) not null default \'0\',
				prf_cch longblob not null default \'\',
				prf_cch_dtp bigint(20) not null default \'0\',
				primary key (id),
				unique key nme_unique (nme)
			)
		';
		$wpdb->query($sql);

		//This part is to import old settings/profiles but seem like it cause problems to some people
/*
		$xmt_acc_old = get_option('xmt_accounts');
		if($xmt_acc_old !== false){
			foreach($xmt_acc_old as $acc_nme=>$acc_cfg){
				$tmp_cfg = $xmt_cfg_def;
				
				$tmp_cfg['ttl'] = $acc_cfg['widget']['title'];
				$tmp_cfg['nme'] = $acc_cfg['widget']['name'];
				$tmp_cfg['lnk_ttl'] = $acc_cfg['widget']['link_title'];
				$tmp_cfg['hdr_sty'] = $acc_cfg['widget']['header_style'];
				$tmp_cfg['cst_hdr_txt'] = $acc_cfg['widget']['custom_text']['header'];
				$tmp_cfg['cst_ftr_txt'] = $acc_cfg['widget']['custom_text']['footer'];
				$tmp_cfg['twt_usr_nme'] = $acc_cfg['tweet']['username'];
				$tmp_cfg['oah_use'] = $acc_cfg['tweet']['oauth_use'];
				$tmp_cfg['oah_tkn'] = $acc_cfg['tweet']['oauth_token'];
				$tmp_cfg['oah_sct'] = $acc_cfg['tweet']['oauth_secret'];
				$tmp_cfg['ord'] = $acc_cfg['tweet']['order'];	
				$tmp_cfg['cnt'] = $acc_cfg['tweet']['count'];
				$tmp_cfg['gmt_add'] = $acc_cfg['tweet']['time_add'];
				$tmp_cfg['dtm_fmt'] = $acc_cfg['tweet']['date_format'];
				$tmp_cfg['twt_lyt'] = $acc_cfg['tweet']['layout'];
				$tmp_cfg['shw_hrl'] = $acc_cfg['tweet']['show_hr'];
				$tmp_cfg['shw_pst_frm'] = $acc_cfg['tweet']['show_post_form'];
				$tmp_cfg['shw_org_rtw'] = $acc_cfg['tweet']['show_origin_retweet'];
				$tmp_cfg['twt_new_pst'] = $acc_cfg['tweet']['tweet_new_post'];
				$tmp_cfg['twt_new_pst_lyt'] = $acc_cfg['tweet']['tweet_new_post_layout'];
				$tmp_cfg['clc_usr_tag'] = $acc_cfg['tweet']['make_clickable']['user_tag'];
				$tmp_cfg['clc_hsh_tag'] = $acc_cfg['tweet']['make_clickable']['hash_tag'];
				$tmp_cfg['clc_url'] = $acc_cfg['tweet']['make_clickable']['url'];
				$tmp_cfg['url_lyt'] = $acc_cfg['tweet']['url_layout'];
				$tmp_cfg['avt_shw'] = $acc_cfg['tweet']['avatar']['show'];
				$tmp_cfg['avt_szw'] = $acc_cfg['tweet']['avatar']['size']['w'];
				$tmp_cfg['avt_szh'] = $acc_cfg['tweet']['avatar']['size']['h'];
				$tmp_cfg['inc_rpl_fru'] = $acc_cfg['tweet']['include']['replies_from_you'];
				$tmp_cfg['inc_rpl_tou'] = $acc_cfg['tweet']['include']['replies'];
				$tmp_cfg['inc_rtw'] = $acc_cfg['tweet']['include']['retweet'];
				$tmp_cfg['inc_drc_msg'] = $acc_cfg['tweet']['include']['direct_message'];
				$tmp_cfg['cch_enb'] = $acc_cfg['tweet']['cache']['enable'];
				$tmp_cfg['cch_exp'] = $acc_cfg['tweet']['cache']['expiry'];	
				$tmp_cfg['cst_css'] = $acc_cfg['css']['custom_css'];
				$tmp_cfg['cvr_sml'] = $acc_cfg['other']['convert_similies'];
				$tmp_cfg['lnk_new_tab'] = $acc_cfg['other']['open_link_on_new_window'];
				$tmp_cfg['tmp_oah_tkn'] = '';
				$tmp_cfg['tmp_oah_sct'] = '';

				xmt_acc_add($acc_nme, $tmp_cfg);
			}
		}
*/	
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