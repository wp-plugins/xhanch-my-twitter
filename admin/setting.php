<?php
	if(!defined('xmt'))
		exit;
	
	function xmt_setting(){
		global $wpdb;
		global $xmt_accounts;
		global $xmt_default;
				
		$sel_account = urldecode(xmt_form_get('profile'));
			
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
			'bird_with_text-12' => 'Cute Twitter bird + display name',
			'bird_with_text-13' => 'Silenced Twitter bird + display name',
			'bird_with_text-14' => 'Twitter bird on a tree branch + display name',
			'bird_with_text-15' => 'Winking Twitter bird  + display name',
			'logo_with_text-1' => 'Twitter logo 1 + display name',
			'logo_with_text-2' => 'Twitter logo 2 + display name',
		);
		
		$arr_dt_format = array(
			'd/m/Y' => 'dd/mm/yyyy',	
			'd.m.y' => 'dd.mm.yy',	
			'm/d/Y' => 'mm/dd/yyyy',
			'Y-m-d' => 'yyyy-mm-dd',
			'M d, Y' => 'mmm dd, yyyy',
			'd-F-Y' => 'dd-'.__('month', 'xmt').'-yyyy',
			'l, F d, Y' => ''.__('dayname', 'xmt').', '.__('month', 'xmt').' dd, yyyy',
		);
		
		$arr_tm_format = array(		
			'H:i' => 'hh:mm',
			'H:i:s' => 'hh:mm:ss',
			'h:i a' => 'hh:mm am/pm',
		);

		$arr_date_format = array();
		foreach($arr_dt_format as $dt_f=>$dt_v){
			$arr_date_format[$dt_f] = $dt_v;
			foreach($arr_tm_format as $tm_f=>$tm_v)
				$arr_date_format[$dt_f.' '.$tm_f] = $dt_v.' '.$tm_v;							
		}
		$arr_date_format['span'] = '? period ago';		

		$arr_tweet_order = array(
			'lto' => 'Latest to oldest',
			'otl' => 'Oldest to latest',
		);
				
		if(isset($_POST['cmd_xmt_create_profile'])){
			$acc_name = strtolower(xmt_form_post('txt_xmt_account_name'));
			$valid_chars = array(
				'a','b','c','d','e','f','g','h','i','j',
				'k','l','m','n','o','p','q','r','s','t',
				'u','v','w','x','y','z',
				'0','1','2','3','4','5','6','7','8','9'
			);
		
			if(empty($acc_name))
				echo '<div id="message" class="updated fade"><p>'.__('Profile name is empty', 'xmt').'</p></div>';			
			elseif(array_key_exists($acc_name, $xmt_accounts))
				echo '<div id="message" class="updated fade"><p>'.__('Profile already exists', 'xmt').'</p></div>';
			else{
				$chars = str_split($acc_name);
				$valid = true;
				foreach($chars as $key){
					if(!in_array($key, $valid_chars)){		
						$valid = false;		
						echo '<div id="message" class="updated fade"><p>'.__('Profile name must contain A to Z and 0 to 9', 'xmt').'</p></div>';
						break;
					}
				}
				if($valid){
					$xmt_accounts[$acc_name] = $xmt_default;
					update_option('xmt_accounts', $xmt_accounts);			
					echo '<div id="message" class="updated fade"><p>'.__('A new profile has been created', 'xmt').'</p></div>';			
				}
			}
		}elseif(isset($_POST['cmd_xmt_delete_profile'])){
			unset($xmt_accounts[$sel_account]);
			update_option('xmt_accounts', $xmt_accounts);	
			echo '<div id="message" class="updated fade"><p>Profile <b>'.htmlspecialchars($sel_account).'</b> has been deleted</p></div>';				
		}elseif(isset($_POST['cmd_xmt_disconnect'])){
			$set = $xmt_accounts[$sel_account];				
			$set['tweet']['oauth_use'] = 0;						
			$set['tweet']['oauth_token'] = $res['ot'];
			$set['tweet']['oauth_secret'] = $res['os'];						
			$set['temp']['oauth_req_token'] = '';
			$set['temp']['oauth_req_secret'] = '';						
			$xmt_accounts[$sel_account] = $set;
			update_option('xmt_accounts', $xmt_accounts);
			echo '<div id="message" class="updated fade"><p>'.__('This profile has been disconnected with Twitter', 'xmt').'</p></div>';				
		}elseif(isset($_POST['cmd_xmt_clear_cache'])){
			$set = $xmt_accounts[$sel_account];		
			$set['tweet']['cache']['tweet_cache']['date'] = 0;
			$set['tweet']['cache']['tweet_cache']['data'] = array();
			$set['tweet']['cache']['profile_cache']['date'] = 0;
			$set['tweet']['cache']['profile_cache']['data'] = array();				
			$xmt_accounts[$sel_account] = $set;
			update_option('xmt_accounts', $xmt_accounts);
			echo '<div id="message" class="updated fade"><p>'.__('Cache has been cleared', 'xmt').'</p></div>';				
		}elseif($_POST['cmd_xmt_update_profile']){
			$set = $xmt_accounts[$sel_account];
			$xmt_config = array(
				'widget' => array(
					'title' => xmt_form_post('txt_xmt_widget_title'),
					'name' => xmt_form_post('txt_xmt_widget_name'),
					'link_title' => intval(xmt_form_post('chk_xmt_widget_link_title')),
					'header_style' => xmt_form_post('cbo_xmt_widget_header_style'),
					'custom_text' => array(
						'header' => xmt_form_post('txa_xmt_widget_custom_text_header'),
						'footer' => xmt_form_post('txa_xmt_widget_custom_text_footer')
					)
				),
				'tweet' => array(
					'username' => xmt_form_post('txt_xmt_tweet_username'),
					'oauth_use' => $set['tweet']['oauth_use'],
					'oauth_token' => $set['tweet']['oauth_token'],
					'oauth_secret' => $set['tweet']['oauth_secret'],
					'order' => xmt_form_post('cbo_xmt_tweet_order'),	
					'count' => xmt_form_post('int_xmt_tweet_count'),
					'include' => array(
						'replies' => xmt_form_post('chk_xmt_tweet_include_replies'),
						'replies_from_you' => xmt_form_post('chk_xmt_tweet_include_replies_from_you'),
						'retweet' => xmt_form_post('chk_xmt_tweet_include_retweet'),
						'direct_message' => xmt_form_post('chk_xmt_tweet_include_direct_message')
					),
					'date_format' => xmt_form_post('txt_xmt_tweet_date_format'),
					'time_add' => xmt_form_post('int_xmt_tweet_time_add'),
					'layout' => xmt_form_post('txa_xmt_tweet_layout'),
					'show_hr' => xmt_form_post('chk_xmt_tweet_show_hr'),
					'show_post_form' => xmt_form_post('chk_xmt_tweet_show_post_form'),
					'make_clickable' => array(
						'user_tag' => xmt_form_post('chk_xmt_tweet_make_clickable_user_tag'),
						'hash_tag' => xmt_form_post('chk_xmt_tweet_make_clickable_hash_tag'),
						'url' => xmt_form_post('chk_xmt_tweet_make_clickable_url')
					),
					'avatar' => array(
						'show' => xmt_form_post('chk_xmt_tweet_avatar_show'),
						'size' => array(
							'w' => xmt_form_post('int_xmt_tweet_avatar_size_w'),
							'h' => xmt_form_post('int_xmt_tweet_avatar_size_h')
						)
					),
					'cache' => array(
						'enable' => xmt_form_post('chk_xmt_tweet_cache_enable'),
						'expiry' => xmt_form_post('int_xmt_tweet_cache_expire'),
						'tweet_cache' => array(
							'date' => 0,
							'data' => array()
						),
						'profile_cache' => array(
							'date' => 0,
							'data' => array()
						)								
					)
				),
				'display_mode' => array(
					'default' => array(
						'enable' => (xmt_form_post('cbo_xmt_display_mode') == 'default')?1:0
					),
					'scrolling' => array(
						'enable' => (xmt_form_post('cbo_xmt_display_mode') == 'scrolling')?1:0,
						'height' => xmt_form_post('int_xmt_display_mode_scrolling_height'),
						'animate' => array(
							'enable' => xmt_form_post('chk_xmt_display_mode_scrolling_animate_enable'),
							'amount' => xmt_form_post('int_xmt_display_mode_scrolling_animate_amount'),
							'delay' => xmt_form_post('int_xmt_display_mode_scrolling_animate_delay')
						),
					)
				),
				'css' => array(
					'custom_css' => xmt_form_post('txa_xmt_css_custom_css'),
				),
				'other' => array(
					'show_credit' => xmt_form_post('chk_xmt_other_show_credit'),
					'convert_similies' => xmt_form_post('chk_xmt_other_convert_similies'),
					'open_link_on_new_window' => xmt_form_post('chk_xmt_open_link_on_new_window')
				),
			);
						
			$xmt_accounts[$sel_account] = $xmt_config;
			update_option('xmt_accounts', $xmt_accounts);	
			echo '<div id="message" class="updated fade"><p>'.__('Configuration Updated', 'xmt').'</p></div>';
		}
				
		ksort($xmt_accounts);
			
?>
		<style type="text/css">
			table, td{font-family:Arial;font-size:12px}
			tr{height:22px}
			ul li{line-height:2px}	
			.clear{clear:both}		
		</style>
		<script type="text/javascript">
			function show_spoiler(obj){
				var inner = obj.parentNode.getElementsByTagName("div")[0];
				if (inner.style.display == "none")
					inner.style.display = "";
				else
					inner.style.display = "none";
			}
			function show_more(obj_nm){
				var obj = document.getElementById(obj_nm);
				if (obj.style.display == "none")
					obj.style.display = "";
				else
					obj.style.display = "none";
			}
			function show_mode_opt(){
				var obj = document.getElementById("cbo_xmt_display_mode");				
				var md_scrolling = document.getElementById("sct_md_scrolling");
				
				md_scrolling.style.display = "none";	
				
				if(obj.value == "scrolling")
					md_scrolling.style.display = "";	
			}
    	</script>
		<div class="wrap">
			<h2><?php echo __('Xhanch - My Twitter - Configuration', 'xmt'); ?></h2>		
            <div style="float:right;line-height:21px">
            	<b><?php echo __('Do you like this plugin? If yes, click this button -&gt;', 'xmt'); ?></b> <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fxhanch.com%2Fwp-plugin-my-twitter%2F&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:1px solid #999; overflow:hidden; width:100px; height:21px; margin:0 0 0 10px; float:right" allowTransparency="true"></iframe>           
            </div>
            <div class="clear"></div>	
            <div style="float:right;line-height:21px">
            	<b><?php echo __('Do you like our service and support? If yes, click this button -&gt;', 'xmt'); ?></b> <iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FXhanch-Studio%2F146245898739871&amp;layout=button_count&amp;show_faces=false&amp;width=450&amp;action=like&amp;font=arial&amp;colorscheme=light&amp;height=21" scrolling="no" frameborder="0" style="border:1px solid #999; overflow:hidden; width:100px; height:21px; margin:0 0 0 10px; float:right" allowTransparency="true"></iframe>           
            </div>
            <div class="clear"></div>
			<br/>
            <?php xmt_check(); ?>
			<form action="" method="post">
				<?php if(count($xmt_accounts) == 0){ ?>
					<?php echo __('You have not created any profile yet.', 'xmt'); ?><br/><br/>
				<?php } ?>
				
				<b><big><?php echo __('Add Profile', 'xmt'); ?></big></b><br/>
				<br/>
				<?php echo __('Fill the following form to create a new profile', 'xmt'); ?>
				<br/><br/>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<td width="150px"><?php echo __('Name', 'xmt'); ?></td>
						<td><input type="text" id="txt_xmt_account_name" name="txt_xmt_account_name" value="" style="width:200px"/></td>
					</tr>
				</table>
				<i><small><?php echo __('Note: Profile name must only contain alphanumeric characters (A to Z and 0 to 9)', 'xmt'); ?></small></i><br/>
				<i><small><?php echo __('Each profile will create a new widget to be placed to your sidebar/post/template code', 'xmt'); ?></small></i><br/>
				<p class="submit"><input type="submit" name="cmd_xmt_create_profile" value="<?php echo __('Create Profile', 'xmt'); ?>"/></p>
			</form>
			<br/>
			<?php if(count($xmt_accounts) > 0){ ?>	
				<b><big><?php echo __('Profile Configuration', 'xmt'); ?></big></b><br/>
				<br/>
				<table cellpadding="0" cellspacing="0" width="100%">
					<tr>
						<td width="150px"><?php echo __('Profile', 'xmt'); ?></td>
						<td>
							<select name="cbo_xmt_account_name" onchange="location.href='admin.php?page=xhanch-my-twitter&profile=' + this.value" style="width:200px">
								<option value=""><?php echo __('- Choose a profile -', 'xmt'); ?></option>
								<?php foreach($xmt_accounts as $acc=>$val){ ?>
									<option value="<?php echo urlencode($acc); ?>" <?php echo ($acc==$sel_account)?'selected="selected"':''; ?>><?php echo ucwords(htmlspecialchars($acc)); ?></option>									
								<?php } ?>
							</select>
						</td>
					</tr>
				</table>					
			<?php } ?>
					
			<?php 
				if(array_key_exists($sel_account, $xmt_accounts)){ 
					$conn = false;
					$set = $xmt_accounts[$sel_account];
					
					if($set['temp']['oauth_req_token'] != '' || $set['temp']['oauth_req_secret'] != ''){
						$set['tweet']['oauth_use'] = 0;
							
						$res = xmt_req('get-auth-token', $sel_account, array(
							'ort' => $set['temp']['oauth_req_token'],
							'ors' => $set['temp']['oauth_req_secret'],
							'ov' => $_GET['oauth_verifier'],							
						));
						
						$set['tweet']['oauth_token'] = $res['ot'];
						$set['tweet']['oauth_secret'] = $res['os'];
						
						$set['temp']['oauth_req_token'] = '';
						$set['temp']['oauth_req_secret'] = '';
						
						$xmt_accounts[$sel_account] = $set;
						update_option('xmt_accounts', $xmt_accounts);	
						
						unset($_SESSION['xmt']);
					}
					
					if($set['tweet']['oauth_token'] != '' && $set['tweet']['oauth_secret'] != ''){
						$res_prof = xmt_req('get-profile', $sel_account);
						if(!count($res_prof['err'])){
							$set['tweet']['username'] = $res_prof['scr_name'];
							$set['tweet']['oauth_use'] = 1;
							$xmt_accounts[$sel_account] = $set;
							update_option('xmt_accounts', $xmt_accounts);
							$conn = true;			
						}
					}
					
					$blog_url = get_option('siteurl');
					if(substr($blog_url,-1) != '/')
						$blog_url .= '/';
					$url_cb = $blog_url.'wp-admin/admin.php?page=xhanch-my-twitter&profile='.$sel_account;
					
					if(!$conn){
						$res = xmt_req('reg', $sel_account, array('cb' => $url_cb));	
						
						
						$set['temp']['oauth_req_token'] = $res['ort'];
						$set['temp']['oauth_req_secret'] = $res['ors'];
						
						$xmt_accounts[$sel_account] = $set;
						update_option('xmt_accounts', $xmt_accounts);	
					}					
					
			?>		
				<form action="" method="post">
					<i><small>Note: <a href="#guide"><?php echo __('Click here for a complete explaination about these configurations fields', 'xmt'); ?></a></small></i><br/>
					<br/>				
                   	
					<b><?php echo __('Widget Setting', 'xmt'); ?></b><br/>
					<br/>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="150px"><?php echo __('Title', 'xmt'); ?></td>
							<td width="200px"><input type="text" id="txt_xmt_widget_title" name="txt_xmt_widget_title" value="<?php echo htmlspecialchars($set['widget']['title']); ?>" style="width:100%"/></td>
							<td width="10px"></td>
							<td width="150px"><?php echo __('Name', 'xmt'); ?></td>
							<td width="200px"><input type="text" id="txt_xmt_widget_name" name="txt_xmt_widget_name" value="<?php echo htmlspecialchars($set['widget']['name']); ?>" style="width:100%"/></td>
						</tr>
						<tr>
							<td><?php echo __('Header style', 'xmt'); ?></td>
							<td>
								<select id="cbo_xmt_widget_header_style" name="cbo_xmt_widget_header_style" style="width:100%">
									<?php foreach($arr_header_style as $key=>$row){ ?>
										<option value="<?php echo $key; ?>" <?php echo ($key==htmlspecialchars($set['widget']['header_style']))?'selected="selected"':''; ?>><?php echo __($row, 'xmt'); ?></option>
									<?php } ?>
								</select>
							</td>
							<td></td>
							<td><?php echo __('Turn title to link?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_widget_link_title" name="chk_xmt_widget_link_title" value="1" <?php echo ($set['widget']['link_title']?'checked="checked"':''); ?>/></td>
						</tr>
						<tr>
							<td colspan="5">
								<?php echo __('Header text', 'xmt'); ?> (<a href="javascript:show_more('sct_text_var')"><?php echo __('show/hide available variables', 'xmt'); ?></a>)
								<textarea id="txa_xmt_widget_custom_text_header" name="txa_xmt_widget_custom_text_header" style="width:100%;height:40px"><?php echo htmlspecialchars($set['widget']['custom_text']['header']); ?></textarea>
								<br/>
				
								<?php echo __('', 'xmt'); ?>Footer text (<a href="javascript:show_more('sct_text_var')"><?php echo __('show/hide available variables', 'xmt'); ?></a>)
								<textarea id="txa_xmt_widget_custom_text_footer" name="txa_xmt_widget_custom_text_footer" style="width:100%;height:40px"><?php echo htmlspecialchars($set['widget']['custom_text']['footer']); ?></textarea>
								<br/>
                                
                                <div id="sct_text_var" style="display:none;">		
                                    <small><i><?php echo __('Available variables for footer and header text', 'xmt'); ?></i></small>
                                    <ul>
                                        <li><small><b>@avatar</b>: <?php echo __('display URL of your Twitter avatar', 'xmt'); ?></small></li>
                                        <li><small><b>@name</b>: <?php echo __('display your full name on Twitter', 'xmt'); ?></small></li>
                                        <li><small><b>@screen_name</b>: <?php echo __('display your screen name on Twitter', 'xmt'); ?></small></li>
                                        <li><small><b>@followers_count</b>: <?php echo __('display a number of your followers', 'xmt'); ?></small></li>
                                        <li><small><b>@statuses_count</b>: <?php echo __('display a number of your total statuses/tweets', 'xmt'); ?></small></li>
                                        <li><small><b>@favourites_count</b>: <?php echo __('display a number of your favourites', 'xmt'); ?></small></li>
                                        <li><small><b>@friends_count</b>: <?php echo __('display a number of your friends', 'xmt'); ?></small></li>
                                    </ul>
                                </div>
												
							</td>
						</tr>
					</table><br/>
					
					<b><?php echo __('Tweet Settings', 'xmt'); ?></b><br/>
					<br/>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="150px"><?php echo __('Username', 'xmt'); ?></td>
							<td width="200px"><input type="text" <?php echo ($conn?'value="'.$res_prof['scr_name'].'" disabled="disabled"':'value="'.htmlspecialchars($set['tweet']['username']).'"'); ?> id="txt_xmt_tweet_username" name="txt_xmt_tweet_username" style="width:100%"/></td>
							<td width="10px"></td>
							<td width="150px"></td>
							<td width="200px"></td>
						</tr>
						<tr>
							<td><?php echo __('Tweet order', 'xmt'); ?></td>
							<td>
								<select id="cbo_xmt_tweet_order" name="cbo_xmt_tweet_order" style="width:100%">
									<?php foreach($arr_tweet_order as $key=>$row){ ?>
										<option value="<?php echo $key; ?>" <?php echo ($key==htmlspecialchars($set['tweet']['order']))?'selected="selected"':''; ?>><?php echo __($row, 'xmt'); ?></option>
									<?php } ?>
								</select>
							</td>
							<td></td>
							<td><?php echo __('# Latest tweets', 'xmt'); ?></td>
							<td><input type="text" id="int_xmt_tweet_count" name="int_xmt_tweet_count" value="<?php echo htmlspecialchars($set['tweet']['count']); ?>" size="5"  maxlength="3"/></td>
						</tr>
						<tr>
							<td><?php echo __('Inc. replies to you?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_include_replies" name="chk_xmt_tweet_include_replies" value="1" <?php echo ($set['tweet']['include']['replies']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td><?php echo __('Inc. replies from you?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_include_replies_from_you" name="chk_xmt_tweet_include_replies_from_you" value="1" <?php echo ($set['tweet']['include']['replies_from_you']?'checked="checked"':''); ?>/></td>
						</tr>
						<tr>
							<td><?php echo __('Inc. retweet?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_include_retweet" name="chk_xmt_tweet_include_retweet" value="1" <?php echo ($set['tweet']['include']['retweet']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
						<!--<tr>
							<td>Inc. direct messages?</td>
							<td><input type="checkbox" id="chk_xmt_tweet_include_direct_message" name="chk_xmt_tweet_include_direct_message" value="1" <?php echo ($set['tweet']['include']['direct_message']?'checked="checked"':''); ?>/></td>
						</tr>-->
						<tr>
							<td><?php echo __('Date format', 'xmt'); ?> (<a href="javascript:show_more('sct_twt_dt_fmt')"><?php echo __('more', 'xmt'); ?></a>)</td>
							<td><input type="text" value="<?php echo htmlspecialchars($set['tweet']['date_format']); ?>" id="txt_xmt_tweet_date_format" name="txt_xmt_tweet_date_format" style="width:100%"/></td>
							<td></td>
							<td><?php echo __('GMT add (in minutes)', 'xmt'); ?></td>
							<td><input type="text" id="int_xmt_tweet_time_add" name="int_xmt_tweet_time_add" value="<?php echo intval($set['tweet']['time_add']); ?>" size="5"  maxlength="4"/></td>
						</tr>
						<tr id="sct_twt_dt_fmt" style="display:none;">
							<td colspan="5">  
                                <small><i><?php echo __('Commonly used date formats', 'xmt'); ?></i></small>
                                <ul>
                                    <?php foreach($arr_date_format as $fmt_val=>$fmt_ex){ ?>
                                        <li><small><b><?php echo $fmt_val; ?></b>: <?php echo __($fmt_ex, 'xmt'); ?></small></li>
                                    <?php } ?>
                                </ul>
                         	</td>
                      	</tr>
						<tr>
							<td colspan="5">
                            	<?php echo __('Tweet layout', 'xmt'); ?> (<a href="javascript:show_more('sct_twt_layout_var')"><?php echo __('show/hide available variables', 'xmt'); ?></a>)<br/>
								<textarea id="txa_xmt_tweet_layout" name="txa_xmt_tweet_layout" style="width:100%;height:40px"><?php echo htmlspecialchars($set['tweet']['layout']); ?></textarea><br/>
                                <div id="sct_twt_layout_var" style="display:none;">		
                                    <small><i><?php echo __('Available variables for tweet layout', 'xmt'); ?></i></small>
                                    <ul>
                                        <li><small><b>@screen_name</b>: <?php echo __('display the screen name who posts the tweet (Link Mode)', 'xmt'); ?></small></li>
                                        <li><small><b>@screen_name_plain</b>: <?php echo __('display the screen name who posts the tweet', 'xmt'); ?></small></li>
                                        <li><small><b>@name</b>: <?php echo __('display the full name who posts the tweet (Link Mode)', 'xmt'); ?></small></li>
                                        <li><small><b>@name_plain</b>: <?php echo __('display the full name who posts the tweet', 'xmt'); ?></small></li>
                                        <li><small><b>@tweet</b>: <?php echo __('content of the tweet', 'xmt'); ?></small></li>
                                        <li><small><b>@date</b>: <?php echo __('formatted publish date time of a tweet', 'xmt'); ?></small></li>
                                        <li><small><b>@source</b>: <?php echo __('display how/where the tweet is posted', 'xmt'); ?></small></li>
                                        <li><small><b>@reply_url</b>: <?php echo __('URL to reply a status', 'xmt'); ?></small></li>
                                        <li><small><b>@reply_link</b>: <?php echo __('Link to reply a status', 'xmt'); ?></small></li>
                                        <li><small><b>@retweet_url</b>: <?php echo __('URL to retweet a status', 'xmt'); ?></small></li>
                                        <li><small><b>@retweet_link</b>: <?php echo __('Link to retweet a status', 'xmt'); ?></small></li>
                                        <li><small><b>@status_url</b>: <?php echo __('URL to view the status on Twitter page', 'xmt'); ?></small></li>
                                    </ul>
                                </div>
                                
							</td>
						</tr>
						<tr>
							<td><?php echo __('Clickable URL?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_make_clickable_url" name="chk_xmt_tweet_make_clickable_url" value="1" <?php echo ($set['tweet']['make_clickable']['url']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td><?php echo __('Show divider line?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_show_hr" name="chk_xmt_tweet_show_hr" value="1" <?php echo ($set['tweet']['show_hr']?'checked="checked"':''); ?>/></td>
						</tr>
						<tr>
							<td><?php echo __('Clickable user tag?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_make_clickable_user_tag" name="chk_xmt_tweet_make_clickable_user_tag" value="1" <?php echo ($set['tweet']['make_clickable']['user_tag']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td><?php echo __('Clickable hash tag?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_make_clickable_hash_tag" name="chk_xmt_tweet_make_clickable_hash_tag" value="1" <?php echo ($set['tweet']['make_clickable']['hash_tag']?'checked="checked"':''); ?>/></td>
						</tr>
						<tr>
							<td><?php echo __('Show avatar?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_avatar_show" name="chk_xmt_tweet_avatar_show" value="1" <?php echo ($set['tweet']['avatar']['show']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td><?php echo __('Avatar size', 'xmt'); ?></td>
							<td>
								W: <input type="text" id="int_xmt_tweet_avatar_size_w" name="int_xmt_tweet_avatar_size_w" value="<?php echo $set['tweet']['avatar']['size']['w']; ?>" size="5"  maxlength="3"/> px; 
								H:	<input type="text" id="int_xmt_tweet_avatar_size_h" name="int_xmt_tweet_avatar_size_h" value="<?php echo $set['tweet']['avatar']['size']['h']; ?>" size="5"  maxlength="3"/> px
							</td>
						</tr>			
						<tr>
							<td><?php echo __('Enable Cache?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_tweet_cache_enable" name="chk_xmt_tweet_cache_enable" value="1" <?php echo ($set['tweet']['cache']['enable']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td><?php echo __('Cache Expiry (in minutes)', 'xmt'); ?></td>
							<td><input type="text" id="int_xmt_tweet_cache_expire" name="int_xmt_tweet_cache_expire" value="<?php echo $set['tweet']['cache']['expiry']; ?>" size="5"  maxlength="3"/></td>
						</tr>	
                        <tr><td colspan="5"><small><i><?php echo __('It is recommended to enable the cache since Twitter limit the number of API invokes per account and you may encounter Twitter API overuse issue', 'xmt'); ?></i></small></td></tr>
					</table>
					<br/>
                        	
                    <b><?php echo __('Advanced Features', 'xmt'); ?></b><br/><br/>
                    <small><?php echo __('<b>Note:</b> Advanced features will burden our web server because the Twitter application (Xhanch - MT) is hosted on our web server to handle OAuth authentication, retrieve your profile, tweets, replies, direct messages and more data. So, please consider to "Enable Cache" to reduce our server load and you may also <a href="http://xhanch.com/xhanch-my-twitter-donate"><b>donate us</b></a> so we can maintain our web server or even afford a much more reliable web server to keep Xhanch - My Twitter up, fast, reliable and stable. Thanks for your attention.', 'xmt'); ?></small><br/>
                    <br/>
                    <?php if(!$conn){ ?>                 
                        <?php echo __('To enable advanced features, you need to grant read-write permission to Xhanch - My Twitter (Xhanch - MT) by clicking the following button.', 'xmt'); ?><br/>
                        <a href="<?php echo $res['auth-url']; ?>"><img src="<?php echo xmt_base_url.'/img/button/sign-in.png'; ?>" alt="<?php echo __('Click here to connect this application with your Twitter Account', 'xmt'); ?>"/></a>
                   	<?php }else{ ?>
                    	<?php echo __('You are currently connected as', 'xmt'); ?> <b><?php echo $res_prof['name']; ?></b> (<b><?php echo $res_prof['scr_name']; ?></b>)<br/><br/>
                        <table cellpadding="0" cellspacing="0">
                            <tr>
                                <td colspan="5"><input type="checkbox" id="chk_xmt_tweet_include_direct_message" name="chk_xmt_tweet_show_post_form" value="1" <?php echo ($set['tweet']['show_post_form']?'checked="checked"':''); ?>/> <?php echo __('Show a form to post a tweet/status when logged in as Admin?', 'xmt'); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5"><input type="checkbox" id="chk_xmt_tweet_include_direct_message" name="chk_xmt_tweet_include_direct_message" value="1" <?php echo ($set['tweet']['include']['direct_message']?'checked="checked"':''); ?>/> <?php echo __('Show direct messages?', 'xmt'); ?></td>
                            </tr>
                            <tr>
                                <td width="150px"></td>
                                <td width="200px"></td>
                                <td width="10px"></td>
                                <td width="150px"></td>
                                <td width="200px"></td>
                            </tr>
                      	</table><br/>
                    	<input type="submit" name="cmd_xmt_disconnect" value="<?php echo __('Disconnect From Twitter', 'xmt'); ?>"/>
                    <?php } ?>
                    <br/><br/>                    
	
					<b><?php echo __('Display Mode', 'xmt'); ?></b><br/>					
					<br/>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="150px"><?php echo __('Selected mode', 'xmt'); ?></td>
							<td width="200px">	
								<select id="cbo_xmt_display_mode" name="cbo_xmt_display_mode" style="width:100%" onchange="show_mode_opt()">															
								<?php foreach($set['display_mode'] as $key=>$val){ ?>
									<option value="<?php echo $key; ?>" <?php echo ($val['enable'])?'selected="selected"':''; ?>><?php echo __(ucwords($key), 'xmt'); ?></option>									
								<?php } ?>
								</select>
							</td>
                            <td width="10px"></td>
                            <td width="150px"></td>
                            <td width="200px"></td>
						</tr>
						<tr>
							<td colspan="5">
                            	<div id="sct_md_scrolling" style="display:none;">	
                                	<table cellpadding="0" cellspacing="0">
                                		<tr>
                                            <td width="150px"><?php echo __('Area Height', 'xmt'); ?></td>
                                            <td width="200px"><input type="text" id="int_xmt_display_mode_scrolling_height" name="int_xmt_display_mode_scrolling_height" value="<?php echo $set['display_mode']['scrolling']['height']; ?>" size="5"  maxlength="5"/> px</td>
                                            <td width="10px"></td>
                                            <td width="150px"><?php echo __('Animate Scrolling?', 'xmt'); ?></td>
                                            <td width="200px"><input type="checkbox" id="chk_xmt_display_mode_scrolling_animate_enable" name="chk_xmt_display_mode_scrolling_animate_enable" value="1" <?php echo ($set['display_mode']['scrolling']['animate']['enable']?'checked="checked"':''); ?>/></td>
                                        </tr>
                                        <tr>
                                            <td><?php echo __('Scroll Amount', 'xmt'); ?></td>
                                            <td><input type="text" id="int_xmt_display_mode_scrolling_animate_amount" name="int_xmt_display_mode_scrolling_animate_amount" value="<?php echo $set['display_mode']['scrolling']['animate']['amount']; ?>" size="5"  maxlength="5"/> px</td>
                                            <td width="10px"></td>
                                            <td><?php echo __('Scroll Delay', 'xmt'); ?></td>
                                            <td><input type="text" id="int_xmt_display_mode_scrolling_animate_delay" name="int_xmt_display_mode_scrolling_animate_delay" value="<?php echo $set['display_mode']['scrolling']['animate']['delay']; ?>" size="5"  maxlength="5"/> ms</td>
                                        </tr>
                                   	</table>
                                </div>
                                <script type="text/javascript">show_mode_opt();</script>
                            </td>
						</tr>						
					</table>
					<br/>
										
					<b><?php echo __('Custom CSS', 'xmt'); ?></b><br/>
					<br/>
					<table cellpadding="0" cellspacing="0" width="710px">
						<tr>
							<td>
                            	<textarea style="width:710px" rows="5" id="txa_xmt_css_custom_css" name="txa_xmt_css_custom_css"><?php echo $set['css']['custom_css']; ?></textarea><br/>
                                <i>
                                	<?php echo __('{xmt_id} will be replaced with the DIV id for Xhanch - My Twitter Widget for this profile', 'xmt'); ?><br/>
                                    <a href="http://xhanch.com/wp-content/plugins/xhanch-my-twitter/css/css.css" target="_blank"><?php echo __('Need reference to set your custom CSS? Click here to view the default CSS codes', 'xmt'); ?></a>
                                </i>
                            </td>
						</tr>
					</table><br/>
										
					<b><?php echo __('Other Settings', 'xmt'); ?></b><br/>
					<br/>
					<table cellpadding="0" cellspacing="0">
						<tr>
							<td width="150px"><?php echo __('Convert Smilies?', 'xmt'); ?></td>
							<td width="200px"><input type="checkbox" id="chk_xmt_other_convert_similies" name="chk_xmt_other_convert_similies" value="1" <?php echo ($set['other']['convert_similies']?'checked="checked"':''); ?>/></td>
							<td width="10px"></td>
							<td width="150px"><?php echo __('Open link in new tab?', 'xmt'); ?></td>
							<td width="200px"><input type="checkbox" id="chk_xmt_open_link_on_new_window" name="chk_xmt_open_link_on_new_window" value="1" <?php echo ($set['other']['open_link_on_new_window']?'checked="checked"':''); ?>/></td>
						</tr>
						<tr>
							<td><?php echo __('Show credit?', 'xmt'); ?></td>
							<td><input type="checkbox" id="chk_xmt_other_show_credit" name="chk_xmt_other_show_credit" value="1" <?php echo ($set['other']['show_credit']?'checked="checked"':''); ?>/></td>
							<td></td>
							<td></td>
							<td></td>
						</tr>
					</table><br/>
										
					<b><?php echo __('Codes for Template and Post/Page', 'xmt'); ?></b><br/>
					<br/>
					<table cellpadding="0" cellspacing="0" width="710px">
						<tr>
							<td>
                            	<?php echo __('This plugin provides widgets for your dynamic sidebars.', 'xmt'); ?><br/>
                                <?php echo __('But, if your theme does not support dynamic sidebars, you can use these codes.', 'xmt'); ?><br/>
                                <br/>
                                
                                <a href="javascript:show_more('sct_php_code')"><?php echo __('Show/hide paste-able code (PHP version)', 'xmt'); ?></a>                                
                                <div id="sct_php_code" style="display:none;">	                                
                            	<?php echo __('Here is your template code', 'xmt'); ?>
                            	<textarea style="width:710px" onfocus="this.select()" onclick="this.select()" rows="7" readonly="readonly">&lt;?php
    $args = array(
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '',
		'after_title' => '',
    );
    xmt($args, '<?php echo $sel_account; ?>');
?&gt;</textarea><br/></div><br/>
                            	<a href="javascript:show_more('sct_scc_code')"><?php echo __('Show/hide paste-able code code (WordPress short code version)', 'xmt'); ?></a>                                
                                <div id="sct_scc_code" style="display:none;">	
                            	<textarea style="width:710px" onfocus="this.select()" onclick="this.select()" rows="2" readonly="readonly">[xmt profile=<?php echo $sel_account; ?> before_widget="" after_widget="" before_title="" after_title=""]</textarea><br/><br/></div>
                                
                            </td>
						</tr>
					</table><br/>
					
					<p class="submit">
						<input type="submit" name="cmd_xmt_update_profile" value="<?php echo __('Update Profile', 'xmt'); ?>"/>
						<input type="submit" name="cmd_xmt_clear_cache" value="<?php echo __('Clear Cache', 'xmt'); ?>"/>
						<input type="submit" name="cmd_xmt_delete_profile" value="<?php echo __('Delete Profile', 'xmt'); ?>"/>
					</p>
				</form>
			<?php } ?>		
				
			<br/><br/>
			<a name="guide"></a>
			<b><big><?php echo __('Support This Plugin Development', 'xmt'); ?></big></b><br/>		
			<br/>	
			<?php echo __('Do you like this plugin? Do you think this plugin very helpful?', 'xmt'); ?><br/>
			<?php echo __('Why don\'t you support this plugin developement by donating any amount you are willing to give?', 'xmt'); ?><br/>
			<br/>
			<?php echo __('If you wish to support the developer and make a donation, please click the following button. Thanks!', 'xmt'); ?><br/>
			<a href="http://xhanch.com/xhanch-my-twitter-donate" target="_blank"><img src="http://xhanch.com/image/paypal/btn_donate.gif" alt="<?php echo __('Donate', 'xmt'); ?>"></a></p>

			<br/><br/>
			<a name="guide"></a>
			<b><big><?php echo __('Complete Info and Share Room', 'xmt'); ?></big></b><br/>		
			<br/>	
			<div class="spoiler">
				<input type="button" onclick="show_spoiler(this);" value="<?php echo __('Complete information regarding Xhanch - My Twitter (Share Room)', 'xmt'); ?>"/>
				<div class="inner" style="display:none;">
					<br/>
					<iframe src="http://xhanch.com/wp-plugin-my-twitter/" style="width:700px;height:500px"></iframe>
				</div>
			</div>			
			<br/>			
			<br/>
		</div>
<?php
	}
?>