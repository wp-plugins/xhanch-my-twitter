<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 1.3.3
	*/

	function xhanch_my_twitter_install () {
		require_once(dirname(__FILE__).'/installer.php');
	}
	register_activation_hook(__FILE__,'xhanch_my_twitter_install');

	require_once(dirname(__FILE__).'/xhanch_my_twitter.function.php');	
	require_once(dirname(__FILE__).'/xhanch_my_twitter_header_style.php');	

	function xhanch_my_twitter($param, $args = array()){		
		update_option("xhanch_my_twitter_title", htmlspecialchars($param['title']));
		update_option("xhanch_my_twitter_header_style", htmlspecialchars($_POST['header_style']));
		update_option("xhanch_my_twitter_name", htmlspecialchars($param['name']));
		update_option("xhanch_my_twitter_id", htmlspecialchars($param['username']));
		update_option("xhanch_my_twitter_count", intval($param['count']));
		update_option("xhanch_my_twitter_show_post_by", htmlspecialchars($param['show_post_by']));
		update_option("xhanch_my_twitter_date_format", htmlspecialchars($param['date_format']));
		update_option("xhanch_my_twitter_show_hr", intval($param['show_hr']));
		
		update_option("xhanch_my_twitter_pw", htmlspecialchars($param['password']));
		update_option("xhanch_my_twitter_rep_msg_enable", intval($param['rep_msg_enable']));
		update_option("xhanch_my_twitter_dir_msg_enable", intval($param['dir_msg_enable']));

		update_option("xhanch_my_twitter_scroll_enable", htmlspecialchars($param['scroll_enable']));
		update_option("xhanch_my_twitter_scroll_area_height", htmlspecialchars($param['scroll_area_height']));

		update_option("xhanch_my_twitter_text_header", htmlspecialchars($param['text_header']));
		update_option("xhanch_my_twitter_text_footer", htmlspecialchars($param['text_footer']));

		update_option("xhanch_my_twitter_credit", intval($param['credit']));
		widget_xhanch_my_twitter($args);
	}

	//Widget
	
	function widget_xhanch_my_twitter($args) {		
		extract($args);

		$res = xhanch_my_twitter_get_tweets();
		$show_post_by = get_option('xhanch_my_twitter_show_post_by');
		$scroll_mode = intval(get_option('xhanch_my_twitter_scroll_enable'));
		$scroll_h = intval(get_option('xhanch_my_twitter_scroll_area_height'));
        $show_hr = intval(get_option('xhanch_my_twitter_show_hr'));
		$scroll_animate = intval(get_option('xhanch_my_twitter_scroll_animate'));
		$scroll_animate_amount = intval(get_option('xhanch_my_twitter_scroll_amount'));
		$scroll_animate_delay = intval(get_option('xhanch_my_twitter_scroll_delay'));

		if(count($res) == 0) 
			return;		
		echo $before_widget;
?>
		<?php 
			if (get_option("xhanch_my_twitter_title")!='')
				echo $before_title.get_option("xhanch_my_twitter_title").$after_title;				
		?>
		<div id="xhanch_my_twitter">
			<?php xhanch_my_twitter_header_style(); ?>

			<?php echo convert_smilies(html_entity_decode(get_option("xhanch_my_twitter_text_header"))); ?>

			<?php if($scroll_mode){ ?>
				<?php if($scroll_animate){ ?>
					<marquee direction="up" onmouseover="this.stop()" onmouseout="this.start()" scrolldelay="<?php echo $scroll_animate_delay; ?>" scrollamount="<?php echo $scroll_animate_amount; ?>" height="<?php echo $scroll_h; ?>px" style="height:<?php echo $scroll_h; ?>px;overflow:hidden">
				<?php }else{ ?>
					<div style="max-height:<?php echo $scroll_h; ?>px;overflow:auto">
				<?php } ?>
			<?php } ?>
			<ul id="xhanch_my_twitter_list">
			<?php foreach($res as $row){ ?>
				<li class="tweet_list">
					<?php if($show_hr) echo '<hr />'; ?>
					<div>
					<?php if($show_post_by != '' && $show_post_by != 'hidden_personal'){ ?>
						<a href="<?php echo $row['author_url']; ?>">
							<?php if($show_post_by == 'avatar'){ ?>
								<img class="avatar" src="<?php echo $row['author_img']; ?>" alt="<?php echo $row['author_name']; ?>"/></a>						
							<?php } else if($show_post_by == 'avatar_name'){ ?>
								<img class="avatar" src="<?php echo $row['author_img']; ?>" alt="<?php echo $row['author_name']; ?>"/> <?php echo $row['author_name'].': '; ?></a>			
							<?php }else{ echo $row['author_name'].'</a>: '; } ?>
					<?php } ?>
					<?php echo $row['tweet']; ?> <?php echo $row['timestamp']; ?>
					<div class="clear"></div>
					</div>
				</li>
			<?php } ?>
			</ul>
			<?php if($scroll_mode){ ?>
				<?php if($scroll_animate){ ?>
					</marquee>
				<?php }else{ ?>
					</div>
				<?php } ?>
			<?php } ?>
						
			<?php echo convert_smilies(html_entity_decode(get_option("xhanch_my_twitter_text_footer"))); ?>

			<?php if (get_option("xhanch_my_twitter_credit")){ ?>
				<div class="credit"><a href="http://xhanch.com/wp-plugin-my-twitter/" rel="section" title="Xhanch My Twitter - A free WordPress plugin to display your latest tweets from Twitter">My Twitter</a>, <a href="http://xhanch.com/" rel="section" title="Developed by Xhanch Studio">by Xhanch</a></div>
			<?php }?>
		</div>
<?php		
		echo $after_widget;
	}

	function xhanch_my_twitter_control(){		
		$arr_header_style = array(
			'' => 'No Header',
			'default' => 'Twitter bird 1 + display name',
			'bird_with_text-2' => 'Twitter bird 2 + display name',
			'bird_with_text-3' => 'Twitter bird 3 + display name',
			'bird_with_text-4' => 'Twitter bird 4 + display name',
			'bird_with_text-5' => 'Twitter bird 5 + display name',
			'bird_with_text-6' => 'Twitter bird 6 + display name',
			'bird_with_text-7' => 'Twitter bird 7 + display name',
			'bird_with_text-8' => 'Twitter bird 8 + display name',
			'logo_with_text-1' => 'Twitter logo 1 + display name',
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
			'span' => '? period ago',
		);		

		$arr_post_by = array(
			'' => 'Hidden',
			'hidden_personal' => 'Hidden (Show my tweets only)',
			'avatar' => 'Avatar',
			'avatar_name' => 'Avatar + Name',
			'name' => 'Name',
		);

		$title = get_option('xhanch_my_twitter_title');
		$header_style = get_option('xhanch_my_twitter_header_style');
		$name = htmlspecialchars(get_option('xhanch_my_twitter_name'));
		$uid = get_option('xhanch_my_twitter_id');	
		$limit = intval(get_option('xhanch_my_twitter_count'));
		$show_post_by = get_option('xhanch_my_twitter_show_post_by');
		$date_format = get_option('xhanch_my_twitter_date_format');
		$show_hr = intval(get_option('xhanch_my_twitter_show_hr'));
		$credit = intval(get_option('xhanch_my_twitter_credit'));
		
		$pwd = get_option('xhanch_my_twitter_pw');
		$rep_msg_enable = intval(get_option('xhanch_my_twitter_rep_msg_enable'));	
		$dir_msg_enable = intval(get_option('xhanch_my_twitter_dir_msg_enable'));
		
		$scroll_enable = intval(get_option('xhanch_my_twitter_scroll_enable'));	
		$scroll_animate = intval(get_option('xhanch_my_twitter_scroll_animate'));
		$scroll_animate_amount = intval(get_option('xhanch_my_twitter_scroll_amount'));
		$scroll_animate_delay = intval(get_option('xhanch_my_twitter_scroll_delay'));
		$scroll_area_height = intval(get_option('xhanch_my_twitter_scroll_area_height'));	

		$text_header = htmlspecialchars(get_option('xhanch_my_twitter_text_header'));
		$text_footer = htmlspecialchars(get_option('xhanch_my_twitter_text_footer'));

		if ($_POST['xhanch_my_twitter_submit']){
			update_option("xhanch_my_twitter_title", htmlspecialchars($_POST['xhanch_my_twitter_title']));
			update_option("xhanch_my_twitter_header_style", htmlspecialchars($_POST['xhanch_my_twitter_header_style']));
			update_option("xhanch_my_twitter_name", xhanch_my_twitter_form_post('xhanch_my_twitter_name'));
			update_option("xhanch_my_twitter_id", htmlspecialchars($_POST['xhanch_my_twitter_id']));
			update_option("xhanch_my_twitter_count", intval($_POST['xhanch_my_twitter_count']));
			update_option("xhanch_my_twitter_show_post_by", htmlspecialchars($_POST['xhanch_my_twitter_show_post_by']));
			update_option("xhanch_my_twitter_date_format", htmlspecialchars($_POST['xhanch_my_twitter_date_format']));
			update_option("xhanch_my_twitter_show_hr", intval($_POST['xhanch_my_twitter_show_hr']));
			update_option("xhanch_my_twitter_credit", intval($_POST['xhanch_my_twitter_credit']));

			update_option("xhanch_my_twitter_pw", htmlspecialchars($_POST['xhanch_my_twitter_pw']));
			update_option("xhanch_my_twitter_rep_msg_enable", intval($_POST['xhanch_my_twitter_rep_msg_enable']));
			update_option("xhanch_my_twitter_dir_msg_enable", intval($_POST['xhanch_my_twitter_dir_msg_enable']));
			
			update_option("xhanch_my_twitter_scroll_enable", intval($_POST['xhanch_my_twitter_scroll_enable']));
			update_option("xhanch_my_twitter_scroll_animate", intval($_POST['xhanch_my_twitter_scroll_animate']));
			update_option("xhanch_my_twitter_scroll_amount", intval($_POST['xhanch_my_twitter_scroll_amount']));
			update_option("xhanch_my_twitter_scroll_delay", intval($_POST['xhanch_my_twitter_scroll_delay']));
			update_option("xhanch_my_twitter_scroll_area_height", intval($_POST['xhanch_my_twitter_scroll_area_height']));

			update_option("xhanch_my_twitter_text_header", xhanch_my_twitter_form_post('xhanch_my_twitter_text_header'));
			update_option("xhanch_my_twitter_text_footer", xhanch_my_twitter_form_post('xhanch_my_twitter_text_footer'));
		}
?>
		<b>General Setting</b>
		<table>
			<tr>
				<td width="150"><label for="xhanch_my_twitter_title">Title</label></td>
				<td><input type="text" id="xhanch_my_twitter_title" name="xhanch_my_twitter_title" value="<?php echo $title; ?>" /></td>
			</tr>
			<tr>
				<td width="150"><label for="xhanch_my_twitter_title">Name</label></td>
				<td><input type="text" id="xhanch_my_twitter_name" name="xhanch_my_twitter_name" value="<?php echo $name; ?>" /></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_id">Username</label></td>
				<td><input type="text" id="xhanch_my_twitter_id" name="xhanch_my_twitter_id" value="<?php echo $uid; ?>" /></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_count"># Latest Tweets</label></td>
				<td><input type="text" id="xhanch_my_twitter_count" name="xhanch_my_twitter_count" value="<?php echo $limit; ?>" size="5"  maxlength="2"/></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_header_style">Header Style</label></td>
				<td>
					<select id="xhanch_my_twitter_header_style" name="xhanch_my_twitter_header_style" style="width:100%">
						<?php foreach($arr_header_style as $key=>$row){ ?>
							<option value="<?php echo $key; ?>" <?php echo ($key==$header_style)?'selected="selected"':''; ?>><?php echo $row; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_show_post_by">Show Post By</label></td>
				<td>
					<select id="xhanch_my_twitter_show_post_by" name="xhanch_my_twitter_show_post_by" style="width:100%">
						<?php foreach($arr_post_by as $key=>$row){ ?>
							<option value="<?php echo $key; ?>" <?php echo ($key==$show_post_by)?'selected="selected"':''; ?>><?php echo $row; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_date_format">Date Format</label></td>
				<td>
					<select id="xhanch_my_twitter_date_format" name="xhanch_my_twitter_date_format" style="width:100%">
						<?php foreach($arr_date_format as $fmt_val=>$fmt_ex){ ?>
							<option value="<?php echo $fmt_val; ?>" <?php echo ($fmt_val==$date_format)?'selected="selected"':''; ?>><?php echo $fmt_ex; ?></option>
						<?php } ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_show_hr">Show Divider Line</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_show_hr" name="xhanch_my_twitter_show_hr" value="1" <?php echo ($show_hr?'checked="checked"':''); ?>/></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_credit">Display Credit</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_credit" name="xhanch_my_twitter_credit" value="1" <?php echo ($credit?'checked="checked"':''); ?>/></td>
			</tr>
		</table>
		<br/>
		
        <b>Advanced Options</b>
		<table>
            <tr>
				<td width="150"><label for="xhanch_my_twitter_pw">Password</label></td>
				<td><input type="password" id="xhanch_my_twitter_pw" name="xhanch_my_twitter_pw" value="<?php echo $pwd; ?>" /></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_rep_msg_enable">Tweet Replies</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_rep_msg_enable" name="xhanch_my_twitter_rep_msg_enable" value="1" <?php echo ($rep_msg_enable?'checked="checked"':''); ?>/></td>
		    </tr>
            <tr>
				<td><label for="xhanch_my_twitter_dir_msg_enable">Direct Message</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_dir_msg_enable" name="xhanch_my_twitter_dir_msg_enable" value="1" <?php echo ($dir_msg_enable?'checked="checked"':''); ?>/></td>
		    </tr>
		</table>
		<br/>

		<b>Scrolling Mode</b>
		<table>
			<tr>
				<td width="150"><label for="xhanch_my_twitter_scroll_enable">Enable Scrolling?</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_scroll_enable" name="xhanch_my_twitter_scroll_enable" value="1" <?php echo ($scroll_enable?'checked="checked"':''); ?>/></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_scroll_animate">Animate Scrolling?</label></td>
				<td><input type="checkbox" id="xhanch_my_twitter_scroll_animate" name="xhanch_my_twitter_scroll_animate" value="1" <?php echo ($scroll_animate?'checked="checked"':''); ?>/></td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_scroll_amount">Scroll Amount</label></td>
				<td><input type="text" id="xhanch_my_twitter_scroll_amount" name="xhanch_my_twitter_scroll_amount" value="<?php echo $scroll_animate_amount; ?>" size="5"  maxlength="5"/> px</td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_scroll_delay">Scroll Delay</label></td>
				<td><input type="text" id="xhanch_my_twitter_scroll_delay" name="xhanch_my_twitter_scroll_delay" value="<?php echo $scroll_animate_delay; ?>" size="5"  maxlength="5"/> ms</td>
			</tr>
			<tr>
				<td><label for="xhanch_my_twitter_scroll_area_height">Area Height</label></td>
				<td><input type="text" id="xhanch_my_twitter_scroll_area_height" name="xhanch_my_twitter_scroll_area_height" value="<?php echo $scroll_area_height; ?>" size="5"  maxlength="5"/> px</td>
			</tr>
		</table>
		<br/>
		
		<b>Header Text</b>
		<textarea id="xhanch_my_twitter_text_header" name="xhanch_my_twitter_text_header" style="width:100%" rows="2"><?php echo $text_header; ?></textarea>
		<br/>

		<b>Footer Text</b>
		<textarea id="xhanch_my_twitter_text_footer" name="xhanch_my_twitter_text_footer" style="width:100%" rows="2"><?php echo $text_footer; ?></textarea>
		<input type="hidden" id="xhanch_my_twitter_submit" name="xhanch_my_twitter_submit" value="1" />
<?php
	}

	function widget_xhanch_my_twitter_init(){
		register_sidebar_widget('Xhanch - My Twitter', 'widget_xhanch_my_twitter');
		register_widget_control('Xhanch - My Twitter', 'xhanch_my_twitter_control', 300, 200 );     
	}
	add_action("plugins_loaded", "widget_xhanch_my_twitter_init");

	function xhanch_my_twitter_css() {
		echo '<link rel="stylesheet" href="'.xhanch_my_twitter_get_dir('url').'/css.css" type="text/css" media="screen" />';
	}
	add_action('wp_print_styles', 'xhanch_my_twitter_css');
?>