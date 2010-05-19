<?php
	if(!defined('xhanch_my_twitter'))
		exit;
	
	function xhanch_my_twitter_setting(){
		$arr_header_style = array(
			'' => 'No Header',
			'avatar' => 'Your avatar + display name',
			'default' => 'Elegant Twitter bird + display name',
			'bird_with_text-2' => 'Twitter bird - side view + display name',
			'bird_with_text-3' => 'Twitter bird plays a notebook + display name',
			'bird_with_text-4' => 'Twitter bird with sharp nose + display name',
			'bird_with_text-5' => 'Twitter bird holds a \'Twitter\' board + display name',
			'bird_with_text-6' => 'Twitter bird holds a \'Follow Me\' banner + display name',
			'bird_with_text-7' => 'Twitter bird listens to music + display name',
			'bird_with_text-8' => 'Twitter bird - front view + display name',
			'bird_with_text-9' => 'Twitter bird - side view + display name',
			'bird_with_text-10' => 'Twitter bird with one wing down + display name',
			'bird_with_text-11' => 'Twitter bird stands next to \'Twitter\' board + display name',
			'logo_with_text-1' => 'Twitter logo 1 + display name',
			'logo_with_text-2' => 'Twitter logo 2 + display name',
		);

		$arr_date_format = array(
			'' => 'Hidden',
			'd/m/Y H:i:s' => 'dd/mm/yyyy hh:mm:ss',
			'd/m/Y H:i' => 'dd/mm/yyyy hh:mm',
			'd/m/Y h:i a' => 'dd/mm/yyyy hh:mm am/pm',
			'd/m/Y' => 'dd/mm/yyyy',
			'm/d/Y H:i:s' => 'mm/dd/yyyy hh:mm:ss',
			'm/d/Y H:i' => 'mm/dd/yyyy hh:mm',
			'm/d/Y h:i a' => 'mm/dd/yyyy hh:mm am/pm',
			'm/d/Y' => 'mm/dd/yyyy',
			'M d, Y H:i:s' => 'mmm dd, yyyy hh:mm:ss',
			'M d, Y H:i' => 'mmm dd, yyyy hh:mm',
			'M d, Y h:i a' => 'mmm dd, yyyy hh:mm am/pm',
			'M d, Y' => 'mmm dd, yyyy',
			'd-F-Y H:i:s' => 'dd-month-yyyy hh:mm:ss',
			'd-F-Y H:i' => 'dd-month-yyyy hh:mm',
			'd-F-Y h:i a' => 'dd-month-yyyy hh:mm am/pm',
			'd-F-Y' => 'dd-month-yyyy',
			'l, F d, Y H:i:s' => 'dayname, month dd, yyyy hh:mm:ss',
			'l, F d, Y H:i' => 'dayname, month dd, yyyy hh:mm',
			'l, F d, Y h:i a' => 'dayname, month dd, yyyy hh:mm am/pm',
			'l, F d, Y' => 'dayname, month dd, yyyy',
			'span' => '? period ago',
		);		

		$arr_post_by = array(
			'' => 'Hidden',
			'hidden_personal' => 'Hidden (Show my tweets only)',
			'avatar' => 'Avatar',
			'avatar_name' => 'Avatar + Name',
			'name' => 'Name',
		);

		$arr_tweet_order = array(
			'lto' => 'Latest to oldest',
			'otl' => 'Oldest to latest',
		);

		if ($_POST['xhanch_my_twitter_submit']){
			update_option("xhanch_my_twitter_title", htmlspecialchars($_POST['xhanch_my_twitter_title']));
			update_option("xhanch_my_twitter_header_style", htmlspecialchars($_POST['xhanch_my_twitter_header_style']));
			update_option("xhanch_my_twitter_name", xhanch_my_twitter_form_post('xhanch_my_twitter_name'));
			update_option("xhanch_my_twitter_id", htmlspecialchars($_POST['xhanch_my_twitter_id']));
			update_option("xhanch_my_twitter_count", intval($_POST['xhanch_my_twitter_count']));
			update_option("xhanch_my_twitter_tweet_order", htmlspecialchars($_POST['xhanch_my_twitter_tweet_order']));
			
			update_option("xhanch_my_twitter_show_post_by", htmlspecialchars($_POST['xhanch_my_twitter_show_post_by']));
			update_option("xhanch_my_twitter_avatar_width", intval(htmlspecialchars($_POST['xhanch_my_twitter_avatar_width'])));
			update_option("xhanch_my_twitter_avatar_height", intval(htmlspecialchars($_POST['xhanch_my_twitter_avatar_height'])));

			update_option("xhanch_my_twitter_date_format", htmlspecialchars($_POST['xhanch_my_twitter_date_format']));
			update_option("xhanch_my_twitter_date_string", htmlspecialchars($_POST['xhanch_my_twitter_date_string']));
			update_option("xhanch_my_twitter_show_hr", intval($_POST['xhanch_my_twitter_show_hr']));
			update_option("xhanch_my_twitter_credit", intval($_POST['xhanch_my_twitter_credit']));

			update_option("xhanch_my_twitter_pw", htmlspecialchars($_POST['xhanch_my_twitter_pw']));
			update_option("xhanch_my_twitter_rep_msg_enable", intval($_POST['xhanch_my_twitter_rep_msg_enable']));
			update_option("xhanch_my_twitter_dir_msg_enable", intval($_POST['xhanch_my_twitter_dir_msg_enable']));

			update_option("xhanch_my_twitter_link_on_title", intval($_POST['xhanch_my_twitter_link_on_title']));
			update_option("xhanch_my_twitter_clickable_user_tag", intval($_POST['xhanch_my_twitter_clickable_user_tag']));		
			update_option("xhanch_my_twitter_clickable_hash_tag", intval($_POST['xhanch_my_twitter_clickable_hash_tag']));		
			update_option("xhanch_my_twitter_clickable_url", intval($_POST['xhanch_my_twitter_clickable_url']));
			update_option("xhanch_my_twitter_open_link_in_new_window", intval($_POST['xhanch_my_twitter_open_link_in_new_window']));

			update_option("xhanch_my_twitter_cache_enable", intval($_POST['xhanch_my_twitter_cache_enable']));
			update_option("xhanch_my_twitter_cache_expiry", intval($_POST['xhanch_my_twitter_cache_expiry']));
			
			update_option("xhanch_my_twitter_scroll_enable", intval($_POST['xhanch_my_twitter_scroll_enable']));
			update_option("xhanch_my_twitter_scroll_animate", intval($_POST['xhanch_my_twitter_scroll_animate']));
			update_option("xhanch_my_twitter_scroll_amount", intval($_POST['xhanch_my_twitter_scroll_amount']));
			update_option("xhanch_my_twitter_scroll_delay", intval($_POST['xhanch_my_twitter_scroll_delay']));
			update_option("xhanch_my_twitter_scroll_area_height", intval($_POST['xhanch_my_twitter_scroll_area_height']));

			update_option("xhanch_my_twitter_text_header", xhanch_my_twitter_form_post('xhanch_my_twitter_text_header'));
			update_option("xhanch_my_twitter_text_footer", xhanch_my_twitter_form_post('xhanch_my_twitter_text_footer'));

			update_option('xhanch_my_twitter_cache_date', 0);
			update_option('xhanch_my_twitter_cache_data', serialize(array()));
			update_option('xhanch_my_twitter_profile_cache_date', 0);
			update_option('xhanch_my_twitter_profile_cache_data', serialize(array()));

			echo '<div id="message" class="updated fade"><p>Configuration Updated</p></div>';
		}
		
		$title = get_option('xhanch_my_twitter_title');
		$header_style = get_option('xhanch_my_twitter_header_style');
		$name = htmlspecialchars(get_option('xhanch_my_twitter_name'));
		$uid = get_option('xhanch_my_twitter_id');	
		$limit = intval(get_option('xhanch_my_twitter_count'));
		$tweet_order = get_option('xhanch_my_twitter_tweet_order');

		$show_post_by = get_option('xhanch_my_twitter_show_post_by');
		$avatar_width = get_option('xhanch_my_twitter_avatar_width');
		$avatar_height = get_option('xhanch_my_twitter_avatar_height');		

		$date_format = get_option('xhanch_my_twitter_date_format');
		$date_string = get_option('xhanch_my_twitter_date_string');
		$show_hr = intval(get_option('xhanch_my_twitter_show_hr'));
		$credit = intval(get_option('xhanch_my_twitter_credit'));
		
		$pwd = get_option('xhanch_my_twitter_pw');
		$rep_msg_enable = intval(get_option('xhanch_my_twitter_rep_msg_enable'));	
		$dir_msg_enable = intval(get_option('xhanch_my_twitter_dir_msg_enable'));
		
		$link_on_title = intval(get_option('xhanch_my_twitter_link_on_title'));	
		$clickable_user_tag = intval(get_option('xhanch_my_twitter_clickable_user_tag'));	
		$clickable_hash_tag = intval(get_option('xhanch_my_twitter_clickable_hash_tag'));		
		$clickable_url = intval(get_option('xhanch_my_twitter_clickable_url'));	

		$open_link_in_new_window = intval(get_option('xhanch_my_twitter_open_link_in_new_window'));

		$cache_enable = intval(get_option('xhanch_my_twitter_cache_enable'));	
		$cache_expiry = intval(get_option('xhanch_my_twitter_cache_expiry'));	

		$scroll_enable = intval(get_option('xhanch_my_twitter_scroll_enable'));	
		$scroll_animate = intval(get_option('xhanch_my_twitter_scroll_animate'));
		$scroll_animate_amount = intval(get_option('xhanch_my_twitter_scroll_amount'));
		$scroll_animate_delay = intval(get_option('xhanch_my_twitter_scroll_delay'));
		$scroll_area_height = intval(get_option('xhanch_my_twitter_scroll_area_height'));	

		$text_header = htmlspecialchars(get_option('xhanch_my_twitter_text_header'));
		$text_footer = htmlspecialchars(get_option('xhanch_my_twitter_text_footer'));
?>
		<div class="wrap">
			<h2>Xhanch - My Twitter - Configuration</h2>
			<form action="" method="post">
				Note: <a href="http://xhanch.com/wp-plugin-my-twitter/" target="_blank">Click here for a complete explanation about configurations' fields</a><br/>
				<br/>
				<b>General Setting</b>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">Title</th>
						<td><input type="text" id="xhanch_my_twitter_title" name="xhanch_my_twitter_title" value="<?php echo $title; ?>" /></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Name</th>
						<td><input type="text" id="xhanch_my_twitter_name" name="xhanch_my_twitter_name" value="<?php echo $name; ?>" /></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Username</th>
						<td><input type="text" id="xhanch_my_twitter_id" name="xhanch_my_twitter_id" value="<?php echo $uid; ?>" /></td>
					</tr>
					<tr>
						<th scope="row" valign="top"># Latest Tweets</th>
						<td><input type="text" id="xhanch_my_twitter_count" name="xhanch_my_twitter_count" value="<?php echo $limit; ?>" size="5"  maxlength="2"/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Tweet Order</th>
						<td>
							<select id="xhanch_my_twitter_tweet_order" name="xhanch_my_twitter_tweet_order" style="width:100%">
								<?php foreach($arr_tweet_order as $key=>$row){ ?>
									<option value="<?php echo $key; ?>" <?php echo ($key==$tweet_order)?'selected="selected"':''; ?>><?php echo $row; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Header Style</td>
						<td>
							<select id="xhanch_my_twitter_header_style" name="xhanch_my_twitter_header_style" style="width:100%">
								<?php foreach($arr_header_style as $key=>$row){ ?>
									<option value="<?php echo $key; ?>" <?php echo ($key==$header_style)?'selected="selected"':''; ?>><?php echo $row; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Avatar Size</td>
						<td>
							Width: <input type="text" id="xhanch_my_twitter_avatar_width" name="xhanch_my_twitter_avatar_width" value="<?php echo $avatar_width; ?>" size="5"  maxlength="3"/> px; 
							Height:	<input type="text" id="xhanch_my_twitter_avatar_height" name="xhanch_my_twitter_avatar_height" value="<?php echo $avatar_height; ?>" size="5"  maxlength="3"/> px
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Show Post By</th>
						<td>
							<select id="xhanch_my_twitter_show_post_by" name="xhanch_my_twitter_show_post_by" style="width:100%">
								<?php foreach($arr_post_by as $key=>$row){ ?>
									<option value="<?php echo $key; ?>" <?php echo ($key==$show_post_by)?'selected="selected"':''; ?>><?php echo $row; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Date Format</th>
						<td>
							<select id="xhanch_my_twitter_date_format" name="xhanch_my_twitter_date_format" style="width:100%">
								<?php foreach($arr_date_format as $fmt_val=>$fmt_ex){ ?>
									<option value="<?php echo $fmt_val; ?>" <?php echo ($fmt_val==$date_format)?'selected="selected"':''; ?>><?php echo $fmt_ex; ?></option>
								<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Date Layout</th>
						<td><input type="text" id="xhanch_my_twitter_date_string" name="xhanch_my_twitter_date_string" size="50" value="<?php echo $date_string; ?>" /></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Show Divider Line</th>
						<td><input type="checkbox" id="xhanch_my_twitter_show_hr" name="xhanch_my_twitter_show_hr" value="1" <?php echo ($show_hr?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Widget Title Link</th>
						<td><input type="checkbox" id="xhanch_my_twitter_link_on_title" name="xhanch_my_twitter_link_on_title" value="1" <?php echo ($link_on_title?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Clickable User Tag (@user)</th>
						<td><input type="checkbox" id="xhanch_my_twitter_clickable_user_tag" name="xhanch_my_twitter_clickable_user_tag" value="1" <?php echo ($clickable_user_tag?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Clickable Hash Tag (#tag)</th>
						<td><input type="checkbox" id="xhanch_my_twitter_clickable_hash_tag" name="xhanch_my_twitter_clickable_hash_tag" value="1" <?php echo ($clickable_hash_tag?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Clickable URL</th>
						<td><input type="checkbox" id="xhanch_my_twitter_clickable_url" name="xhanch_my_twitter_clickable_url" value="1" <?php echo ($clickable_url?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Open Link In New Window</th>
						<td><input type="checkbox" id="xhanch_my_twitter_open_link_in_new_window" name="xhanch_my_twitter_open_link_in_new_window" value="1" <?php echo ($open_link_in_new_window?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Display Credit</th>
						<td><input type="checkbox" id="xhanch_my_twitter_credit" name="xhanch_my_twitter_credit" value="1" <?php echo ($credit?'checked="checked"':''); ?>/></td>
					</tr>
				</table>
				<br/>
				
				<b>Advanced Options</b>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">Password</th>
						<td><input type="password" id="xhanch_my_twitter_pw" name="xhanch_my_twitter_pw" value="<?php echo $pwd; ?>" /></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Tweet Replies</th>
						<td><input type="checkbox" id="xhanch_my_twitter_rep_msg_enable" name="xhanch_my_twitter_rep_msg_enable" value="1" <?php echo ($rep_msg_enable?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Direct Message</th>
						<td><input type="checkbox" id="xhanch_my_twitter_dir_msg_enable" name="xhanch_my_twitter_dir_msg_enable" value="1" <?php echo ($dir_msg_enable?'checked="checked"':''); ?>/></td>
					</tr>
				</table>
				<br/>
				
				<b>Cache Options</b>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">Enable Cache</th>
						<td><input type="checkbox" id="xhanch_my_twitter_cache_enable" name="xhanch_my_twitter_cache_enable" value="1" <?php echo ($cache_enable?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Cache Expiry (in minutes)</th>
						<td><input type="text" id="xhanch_my_twitter_cache_expiry" name="xhanch_my_twitter_cache_expiry" value="<?php echo $cache_expiry; ?>" size="5"  maxlength="3"/></td>
					</tr>
				</table>
				<br/>

				<b>Scrolling Mode</b>
				<table class="form-table">
					<tr>
						<th scope="row" valign="top">Enable Scrolling?</th>
						<td><input type="checkbox" id="xhanch_my_twitter_scroll_enable" name="xhanch_my_twitter_scroll_enable" value="1" <?php echo ($scroll_enable?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Animate Scrolling?</th>
						<td><input type="checkbox" id="xhanch_my_twitter_scroll_animate" name="xhanch_my_twitter_scroll_animate" value="1" <?php echo ($scroll_animate?'checked="checked"':''); ?>/></td>
					</tr>
					<tr>
						<th scope="row" valign="top">Scroll Amount</th>
						<td><input type="text" id="xhanch_my_twitter_scroll_amount" name="xhanch_my_twitter_scroll_amount" value="<?php echo $scroll_animate_amount; ?>" size="5"  maxlength="5"/> px</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Scroll Delay</th>
						<td><input type="text" id="xhanch_my_twitter_scroll_delay" name="xhanch_my_twitter_scroll_delay" value="<?php echo $scroll_animate_delay; ?>" size="5"  maxlength="5"/> ms</td>
					</tr>
					<tr>
						<th scope="row" valign="top">Area Height</th>
						<td><input type="text" id="xhanch_my_twitter_scroll_area_height" name="xhanch_my_twitter_scroll_area_height" value="<?php echo $scroll_area_height; ?>" size="5"  maxlength="5"/> px</td>
					</tr>
				</table>
				<br/>
				
				<b>Header Text</b>
				<textarea id="xhanch_my_twitter_text_header" name="xhanch_my_twitter_text_header" style="width:100%" rows="2"><?php echo $text_header; ?></textarea>
				<br/>

				<b>Footer Text</b>
				<textarea id="xhanch_my_twitter_text_footer" name="xhanch_my_twitter_text_footer" style="width:100%" rows="2"><?php echo $text_footer; ?></textarea>
				<br/>

				<b><i>Available variables for footer and header text</i></b>
				<ul>
					<li><b>@avatar</b>: display URL of your Twitter avatar</li>
					<li><b>@name</b>: display your full name on Twitter</li>
					<li><b>@screen_name</b>: display your screen name on Twitter</li>
					<li><b>@followers_count</b>: display a number of your followers</li>
					<li><b>@statuses_count</b>: display a number of your total statuses/tweets</li>
					<li><b>@favourites_count</b>: display a number of your favourites</li>
					<li><b>@friends_count</b>: display a number of your friends</li>
				</ul>

				<input type="hidden" id="xhanch_my_twitter_submit" name="xhanch_my_twitter_submit" value="1" />
				<p class="submit">
					<input type="submit" name="cmd_submit" value="Save"/>
				</p>
			</form>
		</div>
<?php
	}
?>