<?php
	// Get Reply
	if($cfg['inc_rpl_tou']) {
		$req = xmt_req('get-reply', $acc, $cfg, array('limit' => $lmt), false);
		$arr = xmt_split_xml($acc, $cfg, $arr, $req, 'reply');
	}
	
	// Get Direct Message
	if($cfg['inc_drc_msg']) {
		$req = xmt_req('get-direct-message', $acc, $cfg, array('limit' => $lmt), false);
		$arr = xmt_split_xml($acc, $cfg, $arr, $req, 'direct_message');
	}
	
	// Get Tweet
	$req = xmt_req('get-tweet', $acc, $cfg, array('limit' => $lmt, 'inc_rts' => $cfg['inc_rtw']), false);
	$arr = xmt_split_xml($acc, $cfg, $arr, $req, 'tweet');	
?>