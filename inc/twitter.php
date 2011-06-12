<?php
	function xmt_twt_raw_imp($acc, $cfg, $req, $typ = 'twt') {
		if($typ == 'dmg'){
			$req = str_replace('direct-messages', 'statuses', $req);
			$req = str_replace('direct_message', 'status', $req);
			$req = str_replace('sender', 'user', $req);
		}

		if($req == '')
			return $arr;

		$xml = @simplexml_load_string($req);	
			
		if($xml->error)
			xmt_log($xml->error);	
		
		foreach($xml->status as $res){
			$twt_typ = $typ;
			if($res->retweeted_status)
				$twt_typ = 'rtw';
			$rpl = (string)$res->in_reply_to_screen_name;
			if($rpl != ''){
				if($rpl == $cfg['twt_usr_nme'])
					$twt_typ = 'rty';
				else
					$twt_typ = 'rfy';
			}
			
			xmt_twt_ins($acc, array(
				'id' => (string)$res->id,
				'twt' => (string)$res->text,
				'ath' => (string)$res->user->screen_name,
				'src' => (string)$res->source,
				'dtp' => date('Y-m-d H:i:s', xmt_get_time((string)$res->created_at)),
				'typ' => $twt_typ,
			));

			xmt_ath_ins(array(
				'uid' => (string)$res->user->screen_name,
				'nme' => (string)$res->user->name,
				'img_url' => (string)$res->user->profile_image_url,
			));
		}
		unset($xml);
	}

	function xmt_twt_imp($acc, $cfg){
		global $wpdb;
		global $xmt_tmd;

		$las_imp = intval(xmt_acc_ifo_get($acc, 'las_twt_imp_dtp'));
		$imp_itv = intval($cfg['imp_itv']) * 60;

		if(time() - $las_imp < $imp_itv)
			return;

		$sql = '
			update '.$wpdb->prefix.'xmt_acc
			set las_twt_imp_dtp = '.xmt_sql_int(time()).'
			where acc_nme = '.xmt_sql_str($acc).'
		';
		$wpdb->query($sql);

		$lmt = $cfg['cnt'];			
		if($lmt <= 0)
			$lmt = 5;
				
		$method = 'public';
		if($cfg['oah_use'])
			$method = 'oauth';
			
		@include xmt_base_dir.'/method/'.$method.'.php';

		xmt_tmd('Import Tweets - Finished');
	}

	function xmt_twt_ins($acc, $prm){
		global $wpdb;
		$sql = '
			select count(*)
			from '.$wpdb->prefix.'xmt_twt
			where 
				acc_nme = '.xmt_sql_str($acc).' and
				twt_id = '.xmt_sql_str($prm['id']).'
		';
		$exs = intval($wpdb->get_var($sql));
		if($exs == 0){
			$sql = '
				insert into '.$wpdb->prefix.'xmt_twt(
					acc_nme, 
					twt_id, 
					twt_ath, 
					twt, 
					twt_dtp, 
					twt_typ, 
					twt_src
				)values(
					'.xmt_sql_str($acc).',
					'.xmt_sql_str($prm['id']).',
					'.xmt_sql_str($prm['ath']).',
					'.xmt_sql_str($prm['twt']).',
					'.xmt_sql_str($prm['dtp']).',
					'.xmt_sql_str($prm['typ']).',
					'.xmt_sql_str($prm['src']).'
				)
			';
			$wpdb->query($sql);
		}
	}

	function xmt_ath_ins($prm){
		global $wpdb;
		$sql = '
			select count(*)
			from '.$wpdb->prefix.'xmt_ath
			where uid = '.xmt_sql_str($prm['uid']).' 
		';
		$exs = intval($wpdb->get_var($sql));
		if($exs == 0){
			$sql = '
				insert into '.$wpdb->prefix.'xmt_ath(
					uid, 
					nme, 
					img_url, 
					dte_upd
				)values(
					'.xmt_sql_str($prm['uid']).',
					'.xmt_sql_str($prm['nme']).',
					'.xmt_sql_str($prm['img_url']).',
					now()
				)
			';
		}else{
			$sql = '
				update '.$wpdb->prefix.'xmt_ath
				set
					nme = '.xmt_sql_str($prm['nme']).', 
					img_url = '.xmt_sql_str($prm['img_url']).', 
					dte_upd = now()
				where uid = '.xmt_sql_str($prm['uid']).' 
			';
		}
		$wpdb->query($sql);
	}

	function xmt_twt_get($acc, $cfg){
		global $wpdb;
		xmt_tmd('Get Tweets - Start');

		$cch_tmp = xmt_twt_cch_get($acc);
						
		$cch_exp = intval($cfg['cch_exp']) * 60;	
		$cch_tmd = $cch_tmp['tmd'];

		$cch_use = false;
		if($cfg['cch_enb'] && $cch_tmd > 0){
			$cch_age = time() - $cch_tmd;
			if($cch_age <= $cch_exp)
				$cch_use = true;
		}
		if(!$cch_use){
			$lmt = $cfg['cnt'];			
			if($lmt <= 0)
				$lmt = 5;
			
			$arr = array();
			
			$crt = array();
			$crt[] = 'acc_nme = '.xmt_sql_str($acc);
			
			$typ_exc = array();
			if(!$cfg['inc_rpl_fru'])
				$typ_exc[] = '\'rfy\'';
			if(!$cfg['inc_rpl_tou'])
				$typ_exc[] = '\'rty\'';
			if(!$cfg['inc_rtw'])
				$typ_exc[] = '\'rtw\'';
			if(!$cfg['inc_drc_msg'])
				$typ_exc[] = '\'dmg\'';
			if(count($typ_exc) > 0)
				$crt[] = 'twt_typ not in ('.implode(',', $typ_exc).')';
	
			$sql = '
				select 
					twt.twt_id,
					twt.twt_typ,
					twt.twt_dtp,
					twt.twt,
					twt.twt_src,
					twt.twt_ath,
					ath.nme as ath_nme,
					ath.img_url
				from '.$wpdb->prefix.'xmt_twt twt
				left join '.$wpdb->prefix.'xmt_ath ath
					on twt.twt_ath = ath.uid
				where '.implode(' and ', $crt).'
				order by twt_id '.($cfg['ord']=='lto'?'desc':'asc').'
				limit '.$lmt.'
			';
			$rst = $wpdb->get_results($sql, ARRAY_A);
			foreach($rst as $row){
				$gmt_add = intval(get_option('gmt_offset')) * 60 * 60;
				$twt_dtp = strtotime($row['twt_dtp']) + $gmt_add + ($cfg['gmt_add'] * 60);				
				if($cfg['dtm_fmt'] != ''){
					if($cfg['dtm_fmt'] == 'span')
						$twt_dtp = xmt_time_span($twt_dtp);
					else
						$twt_dtp = date($cfg['dtm_fmt'], $twt_dtp);
				}

				$twt = $row['twt'];
				$twt = html_entity_decode($twt, ENT_COMPAT, 'UTF-8');
				$twt = htmlentities($twt, ENT_COMPAT, 'UTF-8');

				if($cfg['trc_len'] > 0){
					if(strlen($twt) > $cfg['trc_len'])
						$twt = substr($twt, 0, $cfg['trc_len']).' '.$cfg['trc_chr'];
				}
					
				if($cfg['clc_url'])
					$twt = xmt_make_clickable($twt, $acc, $cfg);							

				if($cfg['clc_hsh_tag']){
					$pattern = '/(\s\#([_a-z0-9\-]+))/i';
					$replace = '<a href="http://search.twitter.com/search?q=%23$2" '.($cfg['lnk_new_tab']?'target="_blank"':'').'>$1</a>';
					$twt = preg_replace($pattern,$replace,$twt);
				}

				if($cfg['clc_usr_tag']){
					$pattern = '/(@([_a-z0-9\-]+))/i';
					$replace = '<a href="http://twitter.com/$2" title="Follow $2" '.($cfg['lnk_new_tab']?'target="_blank"':'').'>$1</a>';
					$twt = preg_replace($pattern,$replace,$twt);
				}

				if($cfg['cvr_sml'])
					$twt = convert_smilies($twt);

				$arr[$row['twt_id']] = array(
					'type' => $row['twt_typ'],
					'timestamp' => $twt_dtp,
					'tweet' => $twt,
					'author' => $row['twt_ath'],
					'author_name' => $row['ath_nme'],
					'author_url' => 'http://twitter.com/'.$row['twt_ath'],
					'author_img' => $row['img_url'],
					'source' => $row['twt_src'],					
				);
			}

			if(count($arr))
				xmt_twt_cch_set($acc, $arr);				
			else
				$cch_use = true;			
		}

		if($cch_use)
			$arr = $cch_tmp['dat'];	
		
		xmt_tmd('Get Tweets - Finished');
		return $arr;
	}

	function xmt_prf_get($acc, $cfg){
		$cch_tmp = xmt_prf_cch_get($acc);
						
		$cch_exp = intval($cfg['cch_exp']) * 60;	
		$cch_tmd = $cch_tmp['tmd'];

		$cch_use = false;
		if($cfg['cch_enb'] && $cch_tmd > 0){
			$cch_age = time() - $cch_tmd;
			if($cch_age <= $cch_exp)
				$cch_use = true;			
		}

		if(!$cch_use){
			$api_url_reply = 'http://twitter.com/users/'.urlencode($cfg['twt_usr_nme']).'.xml';
			$req = xmt_get_file($api_url_reply);
			$xml = @simplexml_load_string($req);

			$arr = array(
				'avatar' => (string)$xml->profile_image_url,
				'followers_count' => intval($xml->followers_count),
				'friends_count' => intval($xml->friends_count),
				'favourites_count' => intval($xml->favourites_count),
				'statuses_count' => intval($xml->statuses_count),
				'name' => (string)$xml->name,
				'screen_name' => (string)$xml->screen_name,
			);
			if($req)
				xmt_prf_cch_set($acc, $arr);
			else
				$cch_use = true;
		}

		if($cch_use)
			$arr = $cch_tmp['dat'];	
		return $arr;
	}
?>