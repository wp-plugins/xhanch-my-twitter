<?php
	function my_twitter_header_style(){		
		$header_style = get_option('my_twitter_header_style');
		$username = get_option('my_twitter_id');
		$twitter_url = 'http://twitter.com/'.$username;
		$img_url = my_twitter_get_dir('url').'/img/';

		$part = explode('-', $header_style); 
		$sty_type = $part[0];
		$sty_var = $part[1];

		switch($sty_type){
			case '':
				break;
			case 'bird_with_text':
				?>
					<div class="header_48"><a href="<?php echo $twitter_url; ?>" target="_blank"><img src="<?php echo $img_url; ?>twitter-bird-<?php echo $sty_var; ?>.png" class="img_left" alt="<?php echo $username; ?>"/></a><a target="_blank" class="header_48 text_18" href="<?php echo $twitter_url; ?>"><?php echo get_option("my_twitter_name"); ?></a><div class="clear"></div></div>
				<?php
				break;
			case 'logo_with_text':
				?>
					<div class="header_48"><a href="<?php echo $twitter_url; ?>" target="_blank"><img src="<?php echo $img_url; ?>twitter-logo-<?php echo $sty_var; ?>.png" class="img_left" alt="<?php echo $username; ?>"/></a><a target="_blank" class="header_48 text_18" href="<?php echo $twitter_url; ?>"><?php echo get_option("my_twitter_name"); ?></a><div class="clear"></div></div>
				<?php
				break;
			default:
				?>
					<div class="header_48"><a href="<?php echo $twitter_url; ?>" target="_blank"><img src="<?php echo $img_url; ?>twitter-bird-1.png" class="img_left" alt="<?php echo $username; ?>"/></a><a target="_blank" class="header_48 text_18" href="<?php echo $twitter_url; ?>"><?php echo get_option("my_twitter_name"); ?></a><div class="clear"></div></div>
				<?php
				break;
		}
	}
?>