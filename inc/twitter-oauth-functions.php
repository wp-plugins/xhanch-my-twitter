<?php
	function xmt_twt_oah_prf_get($cfg){
		$cls = new TwitterOAuth($cfg['csm_key'], $cfg['csm_sct'], $cfg['oah_tkn'], $cfg['oah_sct']);
		$usr = json_decode($cls->get('account/verify_credentials'));
		if($usr->screen_name != ''){
			return array(
				'img_url' => $usr->profile_image_url,
				'nme' => $usr->name,
				'scr_nme' => $usr->screen_name,
				'dtp_crt' => $usr->created_at,
				'tot_frd' => $usr->friends_count,
				'tot_flw' => $usr->followers_count,
				'tot_sts' => $usr->statuses_count
			);
		}else
			return false;
	}

	function xmt_twt_oah_twt_get($cfg){
		$cls = new TwitterOAuth($cfg['csm_key'], $cfg['csm_sct'], $cfg['oah_tkn'], $cfg['oah_sct']);
		$cls->format = 'xml';
		return $cls->get('statuses/user_timeline', array('count' => intval($cfg['cnt']), 'include_rts' => intval($cfg['inc_rtw'])));
	}

	function xmt_twt_oah_twt_pst($cfg, $twt){
		$cls = new TwitterOAuth($cfg['csm_key'], $cfg['csm_sct'], $cfg['oah_tkn'], $cfg['oah_sct']);
		return $cls->post('statuses/update', array('status' => $twt));
	}

	function xmt_twt_oah_rpl_get($cfg){
		$cls = new TwitterOAuth($cfg['csm_key'], $cfg['csm_sct'], $cfg['oah_tkn'], $cfg['oah_sct']);
		$cls->format = 'xml';
		return $cls->get('statuses/replies', array('count' => intval($cfg['cnt'])));
	}

	function xmt_twt_oah_drc_msg_get($cfg){
		$cls = new TwitterOAuth($cfg['csm_key'], $cfg['csm_sct'], $cfg['oah_tkn'], $cfg['oah_sct']);
		$cls->format = 'xml';
		return $cls->get('direct_messages', array('count' => intval($cfg['cnt'])));
	}
?>