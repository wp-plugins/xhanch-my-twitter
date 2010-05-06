<?php
	function xhanch_my_twitter_header_style(){		
		xhanch_my_twitter_timed('Build header - Start');

		$header_style = get_option('xhanch_my_twitter_header_style');
		$username = get_option('xhanch_my_twitter_id');
		$open_link_in_new_window = intval(get_option('xhanch_my_twitter_open_link_in_new_window'));

		$twitter_url = 'http://twitter.com/'.$username;
		$img_url = xhanch_my_twitter_get_dir('url').'/img/';

		$part = explode('-', $header_style); 
		$sty_type = $part[0];
		$sty_var = $part[1];


		switch($sty_type){
			case '':
				break;
			case 'bird_with_text':
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-'.$sty_var.'.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a></div>';
				break;
			case 'logo_with_text':
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-logo-'.$sty_var.'.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a></div>';
				break;
			case 'avatar':
				$det = xhanch_my_twitter_get_detail(); 
				if(!$det['avatar']){
					echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-1.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a></div>';
				}else{
					echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$det['avatar'].'" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a></div>';
				}
				break;
			default:
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-1.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a></div>';
				break;
		}
		xhanch_my_twitter_timed('Build header - Finished');
	}
?>