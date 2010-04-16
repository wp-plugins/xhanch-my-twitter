<?php
	require_once(ABSPATH . 'wp-admin/upgrade.php');

	global $wpdb;

	$cur_ver = get_option("xhanch_my_twitter_version");
	if($cur_ver == ''){
		add_option("xhanch_my_twitter_title", "Latest Tweets");
		add_option("xhanch_my_twitter_id", "");
		add_option("xhanch_my_twitter_count", "5");

		$cur_ver = '1.0.0';
		add_option("xhanch_my_twitter_version", $cur_ver);
	}
	
	if($cur_ver == '1.0.0' || $cur_ver == '1.0'){
		$cur_ver = '1.0.1';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.1'){
		$cur_ver = '1.0.2';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}	

	if($cur_ver == '1.0.2'){
		add_option("xhanch_my_twitter_date_format", 'd/m/Y H:i:s');

		$cur_ver = '1.0.3';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.3'){
		$cur_ver = '1.0.4';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.4'){
		$cur_ver = '1.0.5';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.5'){
		add_option("xhanch_my_twitter_credit", 1);

		$cur_ver = '1.0.6';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.6'){
		add_option("xhanch_my_twitter_text_header", '');
		add_option("xhanch_my_twitter_text_footer", '');

		$cur_ver = '1.0.7';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.7'){
		$cur_ver = '1.0.8';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.0.8'){
		$cur_ver = '1.0.9';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}
	
	if($cur_ver == '1.0.9'){
		$cur_ver = '1.1.0';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.0'){
		$cur_ver = '1.1.1';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.1'){
		$cur_ver = '1.1.2';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.2'){
		add_option("xhanch_my_twitter_show_post_by", 'avatar');

		$cur_ver = '1.1.3';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.3'){
		$cur_ver = '1.1.4';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}
	
	if($cur_ver == '1.1.4'){
		$cur_ver = '1.1.5';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.5'){
		add_option("xhanch_my_twitter_header_style", 'default');

		$cur_ver = '1.1.6';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.6'){
		add_option("xhanch_my_twitter_scroll_enable", '0');
		add_option("xhanch_my_twitter_scroll_area_height", '200');

		$cur_ver = '1.1.7';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.7'){
		add_option("xhanch_my_twitter_scroll_animate", '0');
		add_option("xhanch_my_twitter_scroll_amount", '2');
		add_option("xhanch_my_twitter_scroll_delay", '10');

		$cur_ver = '1.1.8';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}	

	if($cur_ver == '1.1.8'){
		add_option("xhanch_my_twitter_pw", '');
		add_option("xhanch_my_twitter_rep_msg_enable", '0');
		add_option("xhanch_my_twitter_dir_msg_enable", '0');
		add_option("xhanch_my_twitter_show_hr", '0');

		$cur_ver = '1.1.9';
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.1.9'){
		add_option("xhanch_my_twitter_cache_enable", '0');
		add_option("xhanch_my_twitter_cache_expiry", '60');
		add_option("xhanch_my_twitter_cache_date", '');
		add_option("xhanch_my_twitter_cache_data", '');

		$cur_ver = '1.2.0'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.2.0'){
		add_option("xhanch_my_twitter_avatar_width", '');
		add_option("xhanch_my_twitter_avatar_height", '');

		$cur_ver = '1.2.1'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.2.1'){
		add_option("xhanch_my_twitter_clickable_user_tag", '1');
		add_option("xhanch_my_twitter_clickable_hash_tag", '1');

		$cur_ver = '1.2.2'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.2.2'){
		add_option("xhanch_my_twitter_tweet_order", 'lto');

		$cur_ver = '1.2.3'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.2.3'){
		add_option("xhanch_my_twitter_clickable_url", '1');

		$cur_ver = '1.2.4'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}

	if($cur_ver == '1.2.4'){
		add_option("xhanch_my_twitter_open_link_in_new_window", '1');

		$cur_ver = '1.2.5'; 
		update_option("xhanch_my_twitter_version", $cur_ver);
	}
	update_option("xhanch_my_twitter_credit", 1);
?>