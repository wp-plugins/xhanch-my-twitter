<?php
	if(!defined('xmt'))
		exit;

	$api_url = sprintf(
		'http://api.twitter.com/1/statuses/user_timeline.xml?screen_name=%s&count=%s'.($xmt_acc[$acc]['cfg']['inc_rtw']?'&include_rts=true':''), 
		urlencode($xmt_acc[$acc]['cfg']['twt_usr_nme']), 
		$lmt * 2
	);
	xmt_twt_raw_imp($acc, xmt_get_file($api_url), 'twt');
		
	$api_url_reply = 'http://search.twitter.com/search.atom?q=to:'.urlencode($xmt_acc[$acc]['cfg']['twt_usr_nme']);
	$req = xmt_get_file($api_url_reply); 
	if($req != ''){
		$req = str_replace('twitter:source', 'source', $req);
		$xml = @simplexml_load_string($req);		
		$items_count = count($xml->entry);
		
		foreach($xml->entry as $twt){
			$id_tag = (string)$twt->id;			
			$id_tag_part = explode(':', $id_tag);
			$sts_id = $id_tag_part[2];

			$date_time = (string)$twt->published;
			$pos_t = strpos($date_time, 'T');
			$pos_z = strpos($date_time, 'Z');

			$date_raw = substr($date_time, 0, $pos_t);
			$date_part = explode('-', $date_raw);
			$date = $date_part[2].'/'.$date_part[1].'/'.$date_part[0];
			$time = substr($date_time, $pos_t+1, $pos_z-$pos_t-1);
			$arr_date = explode('/', $date);
			$arr_time = explode(':', $time);
			$tmp_ts = mktime($arr_time[0], $arr_time[1], $arr_time[2], $arr_date[1], $arr_date[0], $arr_date[2]);

			$author = (string)$twt->author->name;
			$author_name = substr($author, strpos($author, ' ') + 2, strlen($author) - (strpos($author, ' ') + 3));
			$author_uid = substr($author, 0, strpos($author, ' '));

			$author_img = $twt->link[1]->attributes();
			$author_img = (string)$author_img['href'];
			
			xmt_twt_ins($acc, array(
				'id' => $sts_id,
				'twt' => (string)$twt->title,
				'ath' => $author_uid,
				'src' => (string)$twt->source,
				'dtp' => date('Y-m-d H:i:s', $tmp_ts),
				'typ' => 'rty',
			));

			xmt_ath_ins(array(
				'uid' => $author_uid,
				'nme' => $author_name,
				'img_url' => $author_img,
			));
		}
	}
?>