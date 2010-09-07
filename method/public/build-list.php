<?php
	$api_url = sprintf('http://api.twitter.com/1/statuses/user_timeline.xml?screen_name=%s&count=%s&include_rts=true',urlencode($uid),$limit);			
	$arr = xmt_split_xml($profile, $arr, xmt_get_file($api_url), 'tweet');
	if(count($arr) == 0)
		$use_cache = true;
		
	if(intval($cfg['tweet']['include']['replies'])){
		$api_url_reply = 'http://search.twitter.com/search.atom?q=to:'.urlencode($uid);
		$req = xmt_get_file($api_url_reply); 
		if($req == '')
			return array();
		$req = str_replace('twitter:source', 'source', $req);
		$xml = @simplexml_load_string($req);		
		$items_count = count($xml->entry);
		
		$limit = intval($cfg['tweet']['count']);	
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
			$arr_date = explode('/', $date);
			$arr_time = explode(':', $time);
			$tmp_ts = mktime($arr_time[0], $arr_time[1], $arr_time[2], $arr_date[1], $arr_date[0], $arr_date[2]);
			$date_time = date('D M d H:i:s O Y', $tmp_ts);
			
			$timestamp = xmt_parse_time($date_time, $cfg['tweet']['date_format'], $cfg['tweet']['time_add']);

			$author = (string)$xml->entry[$i]->author->name;
			$author_name = substr($author, strpos($author, ' ') + 2, strlen($author) - (strpos($author, ' ') + 3));
			$author_uid = substr($author, 0, strpos($author, ' '));

			$author_img = $xml->entry[$i]->link[1]->attributes();
			$author_img = (string)$author_img['href'];

			$arr[$sts_id] = array(
				'type' => 'public_reply',
				'timestamp' => $timestamp,
				'tweet' => (string)$xml->entry[$i]->content,
				'author' => $author_uid,
				'author_name' => $author_name,
				'author_url' => (string)$xml->entry[$i]->author->uri,
				'author_img' => $author_img,
				'source' => (string)$xml->entry[$i]->source,
			);
			$i++;
		}
	}
?>