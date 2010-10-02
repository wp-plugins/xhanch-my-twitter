<?php
	function xmt_split_xml($profile, $arr, $req, $type = 'tweet') {
		global $xmt_accounts;		
		$cfg = $xmt_accounts[$profile];
		
		$clickable_user_tag = intval($cfg['tweet']['make_clickable']['user_tag']);	
		$clickable_hash_tag = intval($cfg['tweet']['make_clickable']['hash_tag']);	
		$clickable_url = intval($cfg['tweet']['make_clickable']['url']);	
		$new_tab_link = intval($cfg['other']['open_link_on_new_window']);
		$convert_similies = intval($cfg['other']['convert_similies']);	

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
		
		$items_count= count($xml->entry);
		$limit = $items_count;
		foreach($xml->status as $res){
			//if($res->retweeted_status){
			//	$res = $res->retweeted_status;
			//}

			$sts_id = (string)$res->id;
			$rpl = (string)$res->in_reply_to_status_id;
			$date_time = (string)$res->created_at;
			
			$timestamp = xmt_parse_time($date_time, $cfg['tweet']['date_format'], $cfg['tweet']['time_add']);
			
			if($clickable_url)
				$output = xmt_make_clickable($res->text);
			else
				$output = (string)$res->text;

			if($clickable_hash_tag){
				$pattern = '/(\#([_a-z0-9\-]+))/i';
				$replace = '<a href="http://search.twitter.com/search?q=%23$2" '.($new_tab_link?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			if($clickable_user_tag){
				$pattern = '/(@([_a-z0-9\-]+))/i';
				$replace = '<a href="http://twitter.com/$2" title="Follow $2" '.($new_tab_link?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			if($convert_similies)
				$output = convert_smilies($output);
			$author_name = (string)$res->user->name;
			$author_uid = (string)$res->user->screen_name;
			$author_img = (string)$res->user->profile_image_url;
			$arr[$sts_id] = array(
				'type' => $type,
				'timestamp' => $timestamp,
				'tweet' => $output,
				'author' => $author_uid,
				'author_name' => $author_name,
				'author_url' => 'http://twitter.com/'.$author_uid,
				'author_img' => $author_img,
				'source' => (string)$res->source,
			);
		}
		unset($xml);
		return $arr;
	}

	function xmt_get_tweets($profile){
		global $xmt_accounts;		
		$cfg = $xmt_accounts[$profile];
		
		xmt_timed('Get Tweets - Start');
		$cache_enable = intval($cfg['tweet']['cache']['enable']);	
		$cache_expiry = intval($cfg['tweet']['cache']['expiry']) * 60;	
		$cache_date = intval($cfg['tweet']['cache']['tweet_cache']['date']);
		$tweet_order = $cfg['tweet']['order'];

		$use_cache = false;
		if($cache_enable && $cache_date > 0){
			$cache_age = time() - $cache_date;
			if($cache_age <= $cache_expiry)
				$use_cache = true;
		}
		if(!$use_cache){
			$uid = $cfg['tweet']['username'];
			$limit = intval($cfg['tweet']['count']);			
			if($limit <= 0)
				$limit = 5;
			
			$arr = array();
			
			$method = 'public';
			if($cfg['tweet']['oauth_use'])
				$method = 'oauth';
				
			include xmt_base_dir.'/method/'.$method.'/build-list.php';
				
			if(!intval($cfg['tweet']['include']['replies_from_you'])){
				foreach($arr as $sts_id=>$val){
					if(substr(strip_tags($val['tweet']),0,1) == '@' && $val['author'] == $uid)
						unset($arr[$sts_id]);					
				}
				
				$limit = intval($cfg['tweet']['count']);			
				if($limit <= 0)
					$limit = 5;
					
				if(count($arr) < $limit){
					$tmp_limit = $limit;
					$limit = $limit * 2;
					
					include xmt_base_dir.'/method/'.$method.'/build-list.php';
										
					foreach($arr as $sts_id=>$val){
						if(substr(strip_tags($val['tweet']),0,1) == '@' && $val['author'] == $uid)
							unset($arr[$sts_id]);					
					}
					
					$limit = $tmp_limit;
				}
			}
			
			krsort($arr);
	
			$limit = intval($cfg['tweet']['count']);			
			if($limit <= 0)
				$limit = 5;
				
			if(count($arr) > $limit){
				do{
					array_pop($arr);
				}while(count($arr) > $limit);
			}
			
			if(count($arr)){
				$cfg['tweet']['cache']['tweet_cache']['date'] = time();
				$cfg['tweet']['cache']['tweet_cache']['data'] = $arr;
				
				$xmt_accounts[$profile] = $cfg;
				update_option('xmt_accounts', $xmt_accounts);					
			}else
				$use_cache = true;			
		}

		if($use_cache)
			$arr = $cfg['tweet']['cache']['tweet_cache']['data'];		
		
		if($tweet_order == 'otl')
			$arr = array_reverse($arr);
		xmt_timed('Get Tweets - Finished');
		return $arr;
	}

	function xmt_get_detail($profile){
		global $xmt_accounts;
		
		$cfg = $xmt_accounts[$profile];
				
		$cache_enable = intval($cfg['tweet']['cache']['enable']);	
		$cache_expiry = intval($cfg['tweet']['cache']['expiry']) * 60;	
		$cache_date = intval($cfg['tweet']['cache']['profile_cache']['date']);

		$use_cache = false;
		if($cache_enable && $cache_date > 0){
			$cache_age = time() - $cache_date;
			if($cache_age <= $cache_expiry)
				$use_cache = true;			
		}

		if(!$use_cache){
			$api_url_reply = 'http://twitter.com/users/'.urlencode($cfg['tweet']['username']).'.xml';
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
			if($req){			
				$cfg['tweet']['cache']['profile_cache']['date'] = time();
				$cfg['tweet']['cache']['profile_cache']['data'] = $arr;
				
				$xmt_accounts[$profile] = $cfg;
				update_option('xmt_accounts', $xmt_accounts);	
			}else
				$use_cache = true;
		}

		if($use_cache)
			$arr = $cfg['tweet']['cache']['profile_cache']['data'];
		return $arr;
	}
?>