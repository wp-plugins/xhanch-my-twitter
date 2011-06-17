<?php
	// Get Reply
	$req = xmt_twt_oah_rpl_get($cfg);
	xmt_twt_raw_imp($acc, $cfg, $req, 'rty');
	
	// Get Direct Message
	$req =xmt_twt_oah_drc_msg_get($cfg);
	xmt_twt_raw_imp($acc, $cfg, $req, 'dmg');
	
	// Get Tweet
	$req = xmt_twt_oah_twt_get($cfg);
	xmt_twt_raw_imp($acc, $cfg, $req, 'twt');	
?>