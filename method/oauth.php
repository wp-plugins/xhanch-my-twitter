<?php
	// Get Reply
	$req = xmt_req('get-reply', $acc, $cfg, array('limit' => $lmt), false);
	xmt_twt_raw_imp($acc, $cfg, $req, 'rty');
	
	// Get Direct Message
	$req = xmt_req('get-direct-message', $acc, $cfg, array('limit' => $lmt), false);
	xmt_twt_raw_imp($acc, $cfg, $req, 'dmg');
	
	// Get Tweet
	$req = xmt_req('get-tweet', $acc, $cfg, array('limit' => $lmt, 'inc_rts' => $cfg['inc_rtw']), false);
	xmt_twt_raw_imp($acc, $cfg, $req, 'twt');	
?>