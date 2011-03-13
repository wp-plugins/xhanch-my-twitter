<?php
	function xmt_split_xml($acc, $cfg, $arr, $req, $type = 'tweet') {
		if($type == 'direct_message') {
			$req = str_replace('direct-messages', 'statuses', $req);
			$req = str_replace('direct_message', 'status', $req);
			$req = str_replace('sender', 'user', $req);
		}

		if($req == '')
			return $arr;

		$xml = @simplexml_load_string($req);	
			
		if($xml->error)
			xmt_log($xml->error);	
		
		$items_count = count($xml->entry);
		$lmt = $items_count;
		foreach($xml->status as $res){
			if($res->retweeted_status){
				if($cfg['shw_org_rtw'])
					$res = $res->retweeted_status;
			}

			$sts_id = (string)$res->id;
			$rpl = (string)$res->in_reply_to_status_id;
			$date_time = (string)$res->created_at;
			
			$timestamp = xmt_parse_time($date_time, $cfg['dtm_fmt'], $cfg['gmt_add']);
						
			$output = (string)$res->text;
			$output = html_entity_decode($output, ENT_COMPAT, 'UTF-8');
			$output = htmlentities($output, ENT_COMPAT, 'UTF-8');

			if($cfg['trc_len'] > 0){
				if(strlen($output) > $cfg['trc_len'])
					$output = substr($output, 0, $cfg['trc_len']).' '.$cfg['trc_chr'];
			}
				
			if($cfg['clc_url'])
				$output = xmt_make_clickable($output, $acc, $cfg);							

			if($cfg['clc_hsh_tag']){
				$pattern = '/(\s\#([_a-z0-9\-]+))/i';
				$replace = '<a href="http://search.twitter.com/search?q=%23$2" '.($cfg['lnk_new_tab']?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			if($cfg['clc_usr_tag']){
				$pattern = '/(@([_a-z0-9\-]+))/i';
				$replace = '<a href="http://twitter.com/$2" title="Follow $2" '.($cfg['lnk_new_tab']?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			if($cfg['cvr_sml'])
				$output = convert_smilies($output);

			$author_uid = (string)$res->user->screen_name;
			$arr[$sts_id] = array(
				'type' => $type,
				'timestamp' => $timestamp,
				'tweet' => $output,
				'author' => $author_uid,
				'author_name' => (string)$res->user->name,
				'author_url' => 'http://twitter.com/'.$author_uid,
				'author_img' => (string)$res->user->profile_image_url,
				'source' => (string)$res->source,
			);
		}
		unset($xml);
		return $arr;
	}

	function xmt_twt_get($acc, $cfg){
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
			
			$method = 'public';
			if($cfg['oah_use'])
				$method = 'oauth';
				
			@include xmt_base_dir.'/method/'.$method.'/build-list.php';
				
			if(!$cfg['inc_rpl_fru']){
				foreach($arr as $sts_id=>$val){
					if(substr(strip_tags($val['tweet']),0,1) == '@' && $val['author'] != $cfg['tweet']['username'])
						unset($arr[$sts_id]);					
				}
				
				$lmt = $cfg['cnt'];			
				if($lmt <= 0)
					$lmt = 5;
					
				if(count($arr) < $lmt){
					$tmp_lmt = $lmt;
					$lmt = $lmt * 2;
					
					include xmt_base_dir.'/method/'.$method.'/build-list.php';
										
					foreach($arr as $sts_id=>$val){
						if(substr(strip_tags($val['tweet']),0,1) == '@' && $val['author'] != $cfg['tweet']['username'])
							unset($arr[$sts_id]);
					}
					
					$lmt = $tmp_lmt;
				}
			}
			
			krsort($arr);
	
			$lmt = $cfg['cnt'];
			if($lmt <= 0)
				$lmt = 5;

			if(count($arr) > $lmt){
				do{
					array_pop($arr);
				}while(count($arr) > $lmt);
			}

			if(count($arr))
				xmt_twt_cch_set($acc, $arr);				
			else
				$cch_use = true;			
		}

		if($cch_use)
			$arr = $cch_tmp['dat'];	
		
		if($cfg['ord'] == 'otl')
			$arr = array_reverse($arr);
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