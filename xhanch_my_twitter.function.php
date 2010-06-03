<?php
	function xhanch_my_twitter_is_ie6() {
		  $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		  if (ereg("msie 6.0", $userAgent))
				return true;
		  else
			return false;		  
	}

	function xhanch_my_twitter_replace_vars($str){		
		if(trim($str) == '')
			return $str;

		$str = convert_smilies(html_entity_decode($str));
		if(strpos($str, '@') === false)
			return $str;
		
		$det = xhanch_my_twitter_get_detail(); 	
		$str = str_replace('@followers_count', intval($det['followers_count']), $str);
		$str = str_replace('@friends_count', intval($det['friends_count']), $str);
		$str = str_replace('@favourites_count', intval($det['favourites_count']), $str);
		$str = str_replace('@statuses_count', intval($det['statuses_count']), $str);
		$str = str_replace('@avatar', $det['avatar'], $str);
		$str = str_replace('@name', $det['name'], $str);
		$str = str_replace('@screen_name', $det['screen_name'], $str);
		return $str; 
	}

	function xhanch_my_twitter_timed($str = ''){
		global $xhanch_my_twitter_timed;	
		$span = time() - $xhanch_my_twitter_timed;
		xhanch_my_twitter_log(($str?$str.' - ':'').'Exec time - '.$span.' s');
	}

	function xhanch_my_twitter_log($str){
		if(isset($_GET['xmt_debug']))
			echo '<!-- XMT: '.str_replace('--', '-', $str).' -->';
	}

	function xhanch_my_twitter_make_url_clickable_cb($matches) {
		$open_link_in_new_window = intval(get_option('xhanch_my_twitter_open_link_in_new_window'));
		$url = $matches[2];
		$url = esc_url($url);
		if ( empty($url) )
			return $matches[0];
		return $matches[1].'<a href="'.$url.'" rel="nofollow" '.($open_link_in_new_window?'target="_blank"':'').'>'.$url.'</a>';
	}

	function xhanch_my_twitter_make_web_ftp_clickable_cb($matches) {
		$open_link_in_new_window = intval(get_option('xhanch_my_twitter_open_link_in_new_window'));
		$ret = '';
		$dest = $matches[2];
		$dest = 'http://' . $dest;
		$dest = esc_url($dest);
		if ( empty($dest) )
			return $matches[0];
		if ( in_array(substr($dest, -1), array('.', ',', ';', ':')) === true ) {
			$ret = substr($dest, -1);
			$dest = substr($dest, 0, strlen($dest)-1);
		}
		return $matches[1].'<a href="'.$dest.'" rel="nofollow" '.($open_link_in_new_window?'target="_blank"':'').'>'.$dest.'</a>'.$ret;
	}

	function xhanch_my_twitter_make_email_clickable_cb($matches) {
		$email = $matches[2] . '@'.$matches[3];
		return $matches[1] . "<a href=\"mailto:$email\" target=\"_blank\">$email</a>";
	}

	function xhanch_my_twitter_make_clickable($ret) {
		$ret = ' ' . $ret;
		$ret = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/\-=?@\[\](+]|[.,;:](?![\s<])|(?(1)\)(?![\s<])|\)))+)#is', 'xhanch_my_twitter_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', 'xhanch_my_twitter_make_web_ftp_clickable_cb', $ret);
		//$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'xhanch_my_twitter_make_email_clickable_cb', $ret);
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}

	function xhanch_my_twitter_time_in_zone() {
		if ($tz = get_option ('timezone_string') ) {
			$tz_obj = timezone_open ($tz);
			$offset = timezone_offset_get($tz_obj, new datetime('now',$tz_obj));
		}else if (($gmt_offset = get_option ('gmt_offset')) && (!(is_null($gmt_offset))) && (is_numeric($gmt_offset)))
			$offset = $gmt_offset;
		else 
			return(time());
		return (time() + $offset);
	}

	function xhanch_my_twitter_time_span($unix_date){	 
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
	 
		//$now = xhanch_my_twitter_time_in_zone();
	 	$now = time();
		
		if(empty($unix_date))  
			return "Bad date";
			 
		if($now > $unix_date){
			$difference = $now - $unix_date;
			$tense = "ago";	 
		}else{
			$difference = $unix_date - $now;
			$tense = "from now";
		}
	 
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++)
			$difference /= $lengths[$j];
			 
		$difference = round($difference);
	 
		if($difference != 1)
			$periods[$j].= "s";
			 
		return "$difference $periods[$j] {$tense}";
	}

	function xhanch_my_twitter_form_get($str){
		if(!isset($_GET[$str]))
			return false;
		return urldecode($_GET[$str]);
	}

	function xhanch_my_twitter_read_var($str){
		$res = $str;
		$res = str_replace('\\\'','\'',$res);
		$res = str_replace('\\\\','\\',$res);
		$res = str_replace('\\"','"',$res);
		return $res;
	}

	function xhanch_my_twitter_form_post($str, $parse = true){
		if(!isset($_POST[$str]))
			return false;
		if($parse)
			return xhanch_my_twitter_read_var($_POST[$str]);
		return $_POST[$str];
	}

	function xhanch_my_twitter_get_dir($type) {
		if ( !defined('WP_CONTENT_URL') )
			define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		if ( !defined('WP_CONTENT_DIR') )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(dirname(__FILE__)); }
		else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(dirname(__FILE__)); }
	}

	function xhanch_my_twitter_get_file($name, $credentials=false){
		$res = '';
		if($credentials === false)
			$res = @file_get_contents($name);
		if($res === false || $res == ''){
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $name);
			if($credentials !== false)
				curl_setopt($ch, CURLOPT_USERPWD, $credentials);

			curl_setopt($ch, CURLOPT_AUTOREFERER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			if($res === false){
				xhanch_my_twitter_log('Failed to read feeds from twitter because of ' . curl_error($ch));	
			}
			curl_close($ch);
		}		
		return $res;
	}	

	function xhanch_my_twitter_get_time($dt){
		$gmt_cst_add = intval(get_option('xhanch_my_twitter_gmt_add')) * 60;
		
		$tmp = explode(' ', $dt);
		$time = explode(':', $tmp[3]);
		switch($tmp[1]){
			case 'Jan':$tmp[1]=1;break;
			case 'Feb':$tmp[1]=2;break;
			case 'Mar':$tmp[1]=3;break;
			case 'Apr':$tmp[1]=4;break;
			case 'May':$tmp[1]=5;break;
			case 'Jun':$tmp[1]=6;break;
			case 'Jul':$tmp[1]=7;break;
			case 'Aug':$tmp[1]=8;break;
			case 'Sep':$tmp[1]=9;break;
			case 'Oct':$tmp[1]=10;break;
			case 'Nov':$tmp[1]=11;break;
			case 'Dec':$tmp[1]=12;break;
		}
		$gmt_add = get_option('gmt_offset') * 60 * 60;
		return @mktime($time[0], $time[1], $time[2], $tmp[1], $tmp[2], $tmp[5]) + $gmt_add + $gmt_cst_add;
	}

	function xhanch_my_twitter_parse_time($dt){
		$timestamp = '';
		$date_format = get_option('xhanch_my_twitter_date_format');
		if($date_format != ''){
			if($date_format == 'span')
				$timestamp .= xhanch_my_twitter_time_span(xhanch_my_twitter_get_time($dt));
			else
				$timestamp .= date($date_format, xhanch_my_twitter_get_time($dt));
		}
		return $timestamp;
	}

	function xhanch_my_twitter_split_xml($arr, $req, $kind = '') {
		$clickable_user_tag = intval(get_option('xhanch_my_twitter_clickable_user_tag'));	
		$clickable_hash_tag = intval(get_option('xhanch_my_twitter_clickable_hash_tag'));	
		$clickable_url = intval(get_option('xhanch_my_twitter_clickable_url'));	
		$open_link_in_new_window = intval(get_option('xhanch_my_twitter_open_link_in_new_window'));

		if($kind == 'direct') {
			$req = str_replace('direct-messages', 'statuses', $req);
			$req = str_replace('direct_message', 'status', $req);
			$req = str_replace('sender', 'user', $req);
		}

		if($req == '')
			return $arr;

		$xml = @simplexml_load_string($req);	
			
		if($xml->error)
			xhanch_my_twitter_log($xml->error);	
		
		$items_count= count($xml->entry);
		$limit = $items_count;
		foreach($xml->status as $res){
			$sts_id = (string)$res->id;
			$rpl = (string)$res->in_reply_to_status_id;
			$date_time = (string)$res->created_at;
			
			$timestamp = xhanch_my_twitter_parse_time($date_time);
			
			if($clickable_url)
				$output = xhanch_my_twitter_make_clickable($res->text);
			else
				$output = (string)$res->text;

			if($clickable_hash_tag){
				$pattern = '/(\#([_a-z0-9\-]+))/i';
				$replace = '<a href="http://search.twitter.com/search?q=%23$2" '.($open_link_in_new_window?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			if($clickable_user_tag){
				$pattern = '/(@([_a-z0-9\-]+))/i';
				$replace = '<a href="http://twitter.com/$2" title="Follow $2" '.($open_link_in_new_window?'target="_blank"':'').'>$1</a>';
				$output = preg_replace($pattern,$replace,$output);
			}

			$output = convert_smilies($output);
			$author_name = (string)$res->user->name;
			$author_uid = (string)$res->user->screen_name;
			$author_img = (string)$res->user->profile_image_url;
			$arr[date('YmdHis', xhanch_my_twitter_get_time($date_time))] = array(
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

	function xhanch_my_twitter_merge_messages($std_req, $rep_req, $dir_req, $extra_options){
        $res = array();
		if($extra_options['rep_msg'])
			$res = xhanch_my_twitter_split_xml($res, $rep_req, 'reply');
		if($extra_options['dir_msg'])
			$res = xhanch_my_twitter_split_xml($res, $dir_req, 'direct');
		$res = xhanch_my_twitter_split_xml($res, $std_req, 'standard');
		krsort($res);
		return $res;
	}

	function xhanch_my_twitter_get_tweets(){		
		xhanch_my_twitter_timed('Get Tweets - Start');
		$cache_enable = intval(get_option('xhanch_my_twitter_cache_enable'));	
		$cache_expiry = intval(get_option('xhanch_my_twitter_cache_expiry')) * 60;	
		$cache_date = intval(get_option('xhanch_my_twitter_cache_date'));
		$tweet_order = get_option('xhanch_my_twitter_tweet_order');

		$use_cache = false;
		if($cache_enable && $cache_date > 0){
			$cache_age = time() - $cache_date;
			if($cache_age <= $cache_expiry)
				$use_cache = true;			
		}
		if(!$use_cache){
			$uid = get_option('xhanch_my_twitter_id');
			$pwd = get_option('xhanch_my_twitter_pw');
			$limit = intval(get_option('xhanch_my_twitter_count'));
			$show_post_by = intval(get_option('xhanch_my_twitter_show_post_by'));
			
			if($limit <= 0)
				$limit = 5;
			
			$arr = array();
			if($pwd == ''){			
				$api_url = sprintf('http://twitter.com/statuses/user_timeline/%s.xml?count=%s',urlencode($uid),$limit);
				$arr = xhanch_my_twitter_split_xml($arr, xhanch_my_twitter_get_file($api_url));
				if(count($arr) == 0)
					$use_cache = true;
			}else{			
				$extra_options = Array();
				$extra_options['rep_msg'] = intval(get_option("xhanch_my_twitter_rep_msg_enable"));
				$extra_options['dir_msg'] = intval(get_option("xhanch_my_twitter_dir_msg_enable"));
				$extra_options['credentials'] = sprintf("%s:%s", $uid, $pwd);

				if($extra_options['rep_msg']) {
					$api_url = sprintf('http://twitter.com/statuses/replies/%s.xml?count=%s',urlencode($uid),$limit);
					$rep_req = xhanch_my_twitter_get_file($api_url, $extra_options['credentials']);
				}
				if($extra_options['dir_msg']) {
					$api_url = sprintf('http://twitter.com/direct_messages.xml?count=%s',$limit);
					$dir_req = xhanch_my_twitter_get_file($api_url, $extra_options['credentials']);
				}
				$api_url = sprintf('http://twitter.com/statuses/user_timeline/%s.xml?count=%s',urlencode($uid),$limit);
				$std_req = xhanch_my_twitter_get_file($api_url, $extra_options['credentials']);

				$arr = xhanch_my_twitter_merge_messages($std_req, $rep_req, $dir_req, $extra_options);			
			}

			if(true){
				$api_url_reply = 'http://search.twitter.com/search.atom?q=to:'.urlencode($uid);
				$req = xhanch_my_twitter_get_file($api_url_reply); 
				if($req == '')
					return array();
				$req = str_replace('twitter:source', 'source', $req);
				$xml = @simplexml_load_string($req);		
				$items_count = count($xml->entry);
				
				$limit = intval(get_option('xhanch_my_twitter_count'));
				if($items_count < $limit)
					$limit = $items_count;

				$i = 0;			
				while($i < $limit){
					$id_tag = (string)$xml->entry[$i]->id;			
					$id_tag_part = explode(':', $id_tag);
					$sts_id = $id_tag_part[2];

					$date_time = (string)$xml->entry[$i]->published;
					$pos_t = strpos($date_time, 'T');
					$pos_z = strpos($date_time, 'Z');

					$date_raw = substr($date_time, 0, $pos_t);
					$date_part = explode('-', $date_raw);
					$date = $date_part[2].'/'.$date_part[1].'/'.$date_part[0];
					$time = substr($date_time, $pos_t+1, $pos_z-$pos_t-1);

					$author = (string)$xml->entry[$i]->author->name;
					$author_name = substr($author, strpos($author, ' ') + 2, strlen($author) - (strpos($author, ' ') + 3));
					$author_uid = substr($author, 0, strpos($author, ' '));

					$author_img = $xml->entry[$i]->link[1]->attributes();	
					$author_img = (string)$author_img['href'];

					$arr[$sts_id] = array(
						'date' => $date,
						'time' => $time,
						'tweet' => (string)$xml->entry[$i]->content,
						'author' => $author_uid,
						'author_name' => $author_name,
						'author_url' => (string)$xml->entry[$i]->author->uri,
						'author_img' => $author_img,
						'source' => (string)$xml->entry[$i]->source,
					);
					$i++;
				}
				krsort($arr);

				$limit = intval(get_option('xhanch_my_twitter_count'));
				if(count($arr) > $limit){
					do{
						array_pop($arr);
					}while(count($arr) > $limit);
				}
			}

			if(count($arr)){
				update_option('xhanch_my_twitter_cache_date', time());
				update_option('xhanch_my_twitter_cache_data', $arr);
			}else
				$use_cache = true;			
		}

		if($use_cache)
			$arr = get_option('xhanch_my_twitter_cache_data');		
		
		if($tweet_order == 'otl')
			$arr = array_reverse($arr);
		xhanch_my_twitter_timed('Get Tweets - Finished');
		return $arr;
	}

	function xhanch_my_twitter_get_detail(){		
		$username = get_option('xhanch_my_twitter_id');
		$cache_enable = intval(get_option('xhanch_my_twitter_cache_enable'));	
		$cache_expiry = intval(get_option('xhanch_my_twitter_cache_expiry')) * 60;	
		$cache_date = intval(get_option('xhanch_my_twitter_profile_cache_date'));

		$use_cache = false;
		if($cache_enable && $cache_date > 0){
			$cache_age = time() - $cache_date;
			if($cache_age <= $cache_expiry)
				$use_cache = true;			
		}

		if(!$use_cache){
			$api_url_reply = 'http://twitter.com/users/'.urlencode($username).'.xml';
			$req = xhanch_my_twitter_get_file($api_url_reply);
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
				update_option('xhanch_my_twitter_profile_cache_date', time());
				update_option('xhanch_my_twitter_profile_cache_data', $arr);
			}else
				$use_cache = true;
		}

		if($use_cache)
			$arr = get_option('xhanch_my_twitter_profile_cache_data');
		return $arr;
	}
?>