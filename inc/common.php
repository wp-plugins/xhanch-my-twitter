<?php
	function xmt_is_ie6() {
		  $userAgent = strtolower($_SERVER['HTTP_USER_AGENT']);
		  if (ereg("msie 6.0", $userAgent))
				return true;
		  else
			return false;		  
	}

	function xmt_check(){
		$issues = array();
		if(!function_exists('curl_init'))
			$issues[] = 'Ups, your web server does not provide/support/enable the CURL Extension. But, this plugin may work if you just leave the password field empty/blank or you can ask your hosting provider to enable it for you';
		if(!function_exists('simplexml_load_string'))
			$issues[] = 'SimpleXML cannot be found. You can ask your hosting provider to enable it or you can\'t use this plugin.';
		if(count($issues))
			echo '<div id="message" class="updated fade"><p><b>Plugin requirements issue(s)</b>:<br/><br/>'.implode('<br/><br/>', $issues).'</p></div>';
	}

	function xmt_replace_vars($str, $profile){		
		if(trim($str) == '')
			return $str;

		$str = convert_smilies(html_entity_decode($str));
		if(strpos($str, '@') === false)
			return $str;
		
		$det = xmt_get_detail($profile); 	
		$str = str_replace('@followers_count', intval($det['followers_count']), $str);
		$str = str_replace('@friends_count', intval($det['friends_count']), $str);
		$str = str_replace('@favourites_count', intval($det['favourites_count']), $str);
		$str = str_replace('@statuses_count', intval($det['statuses_count']), $str);
		$str = str_replace('@avatar', $det['avatar'], $str);
		$str = str_replace('@name', $det['name'], $str);
		$str = str_replace('@screen_name', $det['screen_name'], $str);
		return $str; 
	}

	function xmt_timed($str = ''){
		global $xmt_timed;	
		$span = time() - $xmt_timed;
		xmt_log(($str?$str.' - ':'').'Exec time - '.$span.' s');
	}

	function xmt_log($str){
		if(isset($_GET['xmt_debug']))
			echo '<!-- XMT: '.str_replace('--', '-', $str).' -->';
		elseif(isset($_GET['xmt_debug_show']))
			echo '<i>- XMT: '.str_replace('--', '-', $str).' -</i><br/>';
	}

	function xmt_make_url_clickable_cb($matches, $new_tab_link = true){
		$url = $matches[2];
		$url = esc_url($url);
		if ( empty($url) )
			return $matches[0];
		return $matches[1].'<a href="'.$url.'" rel="nofollow" '.($new_tab_link?'target="_blank"':'').'>'.$url.'</a>';
	}

	function xmt_make_web_ftp_clickable_cb($matches, $new_tab_link = true) {
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
		return $matches[1].'<a href="'.$dest.'" rel="nofollow" '.($new_tab_link?'target="_blank"':'').'>'.$dest.'</a>'.$ret;
	}

	function xmt_make_email_clickable_cb($matches) {
		$email = $matches[2] . '@'.$matches[3];
		return $matches[1] . "<a href=\"mailto:$email\" target=\"_blank\">$email</a>";
	}

	function xmt_make_clickable($ret, $new_tab_link = true) {
		$ret = ' ' . $ret;
		$ret = preg_replace_callback('#(?<=[\s>])(\()?([\w]+?://(?:[\w\\x80-\\xff\#$%&~/\-=?@\[\](+]|[.,;:](?![\s<])|(?(1)\)(?![\s<])|\)))+)#is', 'xmt_make_url_clickable_cb', $ret);
		$ret = preg_replace_callback('#([\s>])((www|ftp)\.[\w\\x80-\\xff\#$%&~/.\-;:=,?@\[\]+]+)#is', 'xmt_make_web_ftp_clickable_cb', $ret);
		//$ret = preg_replace_callback('#([\s>])([.0-9a-z_+-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,})#i', 'xmt_make_email_clickable_cb', $ret);
		// this one is not in an array because we need it to run last, for cleanup of accidental links within links
		$ret = preg_replace("#(<a( [^>]+?>|>))<a [^>]+?>([^>]+?)</a></a>#i", "$1$3</a>", $ret);
		$ret = trim($ret);
		return $ret;
	}

	function xmt_time_in_zone() {
		if ($tz = get_option ('timezone_string') ) {
			$tz_obj = timezone_open ($tz);
			$offset = timezone_offset_get($tz_obj, new datetime('now',$tz_obj));
		}else if (($gmt_offset = get_option ('gmt_offset')) && (!(is_null($gmt_offset))) && (is_numeric($gmt_offset)))
			$offset = $gmt_offset;
		else 
			return(time());
		return (time() + $offset);
	}

	function xmt_time_span($unix_date){	 
		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");
	 
		//$now = xmt_time_in_zone();
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

	function xmt_form_get($str){
		if(!isset($_GET[$str]))
			return false;
		return xmt_read_var(urldecode($_GET[$str]));
	}

	function xmt_read_var($str){
		$res = $str;
		$res = str_replace('\\\'','\'',$res);
		$res = str_replace('\\\\','\\',$res);
		$res = str_replace('\\"','"',$res);
		return $res;
	}

	function xmt_form_post($str, $parse = true){
		if(!isset($_POST[$str]))
			return false;
		if($parse)
			return xmt_read_var($_POST[$str]);
		return $_POST[$str];
	}

	function xmt_get_dir($type) {
		if ( !defined('WP_CONTENT_URL') )
			define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
		if ( !defined('WP_CONTENT_DIR') )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ($type=='path') { return WP_CONTENT_DIR.'/plugins/'.plugin_basename(xmt_base_dir); }
		else { return WP_CONTENT_URL.'/plugins/'.plugin_basename(xmt_base_dir); }
	}
	
	function xmt_req($act, $profile,$add=array(),$decode=true){		
		global $xmt_accounts;
		$set = $xmt_accounts[$profile];
		$url = 'http://xhanch.com/api/xmt.php?gz&a='.$act.'&ot='.$set['tweet']['oauth_token'].'&os='.$set['tweet']['oauth_secret'];
		foreach($add as $aK=>$aV){
			$url .= '&'.$aK.'='.urlencode($aV);
		}
		$res = gzinflate(xmt_get_file($url));		
		if($decode)
			return unserialize($res);
		else
			return $res;
	}

	function xmt_get_file($name){
		$res = '';
		$res = @file_get_contents($name);
		if($res === false || $res == ''){
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $name);
			curl_setopt($ch, CURLOPT_AUTOREFERER, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

			$res = curl_exec($ch);
			if($res === false){
				xmt_log('Failed to read feeds from twitter because of ' . curl_error($ch));	
			}
			curl_close($ch);
		}		
		return $res;
	}	

	function xmt_get_time($dt, $gmt_cst_add = 0){		
		$gmt_cst_add = $gmt_cst_add * 60;
		
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

	function xmt_parse_time($dt, $date_format, $gmt_cst_add = 0){		
		$timestamp = '';
		if($date_format != ''){
			if($date_format == 'span')
				$timestamp .= xmt_time_span(xmt_get_time($dt, $gmt_cst_add));
			else
				$timestamp .= date($date_format, xmt_get_time($dt, $gmt_cst_add));
		}
		return $timestamp;
	}	
?>