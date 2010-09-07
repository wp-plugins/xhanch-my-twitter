<?php
	// Get Reply
	if(intval($cfg['tweet']['include']['replies'])) {
		$rep_req = xmt_req('get-reply', $profile, array('limit' => $limit), false);
		$arr = xmt_split_xml($profile, $arr, $rep_req, 'reply');
	}
	
	// Get Direct Message
	if(intval($cfg['tweet']['include']['direct_message'])) {
		$rep_req = xmt_req('get-direct-message', $profile, array('limit' => $limit), false);
		$arr = xmt_split_xml($profile, $arr, $rep_req, 'direct_message');
	}
	
	// Get Tweet
	$req = xmt_req('get-tweet', $profile, array('limit' => $limit), false);
	$arr = xmt_split_xml($profile, $arr, $req, 'tweet');	
?>