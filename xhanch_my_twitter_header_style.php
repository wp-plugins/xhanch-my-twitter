<?php
	function xhanch_my_twitter_header_style(){		
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
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-'.$sty_var.'.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a><div class="clear"></div></div>';
				break;
			case 'logo_with_text':
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-logo-'.$sty_var.'.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a><div class="clear"></div></div>';
				break;
			case 'avatar':
				$api_url_reply = 'http://twitter.com/users/'.urlencode($username).'.xml';
				$req = xhanch_my_twitter_get_file($api_url_reply); 
				if($req == ''){
					echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-1.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a><div class="clear"></div></div>';
				}else{
					$xml = @new SimpleXMLElement($req);
					$img_url = $xml->profile_image_url;

					echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a><div class="clear"></div></div>';
				}
				break;
			default:
				echo '<div class="header_48"><a href="'.$twitter_url.'" '.($open_link_in_new_window?'target="_blank"':'').'><img src="'.$img_url.'twitter-bird-1.png" class="img_left" alt="'.$username.'"/></a><a '.($open_link_in_new_window?'target="_blank"':'').' class="header_48 text_18" href="'.$twitter_url.'">'.get_option("xhanch_my_twitter_name").'</a><div class="clear"></div></div>';
				break;
		}
	}
?>