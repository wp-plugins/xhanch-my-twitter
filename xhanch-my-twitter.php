<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 2.3.9
	*/
	
	define('xmt', true);
	define('xmt_base_dir', dirname(__FILE__));
		
	global $xmt_timed;
	global $xmt_accounts;
	global $xmt_default;
		
	load_plugin_textdomain('xmt', WP_PLUGIN_URL.'/xhanch-my-twitter/lang/', 'xhanch-my-twitter/lang/');
		
	$xmt_default = array(
		'widget' => array(
			'title' => 'Latest Tweets',
			'name' => '',
			'link_title' => 0,
			'header_style' => 'default',
			'custom_text' => array(
				'header' => '',
				'footer' => ''
			)
		),
		'tweet' => array(
			'username' => '',
			'oauth_use' => 0,
			'oauth_token' => '',
			'oauth_secret' => '',
			'order' => 'lto',	
			'count' => '5',
			'time_add' => '0',
			'date_format' => 'd/m/Y H:i:s',
			'layout' => '@tweet - posted on @date',
			'show_hr' => 0,
			'show_post_form' => 1,
			'show_origin_retweet' => 0,
			'make_clickable' => array(
				'user_tag' => 1,
				'hash_tag' => 1,
				'url' => 1
			),
			'avatar' => array(
				'show' => 1,
				'size' => array(
					'w' => 0,
					'h' => 0
				)
			),
			'include' => array(
				'replies' => 0,
				'replies_from_you' => 0,
				'retweet' => 0,
				'direct_message' => 0
			),
			'cache' => array(
				'enable' => 1,
				'expiry' => 60,
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
				'enable' => 1
			),
			'scrolling' => array(
				'enable' => 0,
				'height' => 200,
				'animate' => array(
					'enable' => 0,
					'direction' => 'up',
					'amount' => 1,
					'delay' => 50
				),
			)
		),
		'css' => array(
			'custom_css' => ''
		),
		'other' => array(
			'show_credit' => 1,
			'convert_similies' => 1,
			'open_link_on_new_window' => 1,
		),
		'temp' => array(
			'oauth_req_token' => '',
			'oauth_req_secret' => '',			
		)
	);
		
	$xmt_accounts = get_option('xmt_accounts');
	if($xmt_accounts === false)
		$xmt_accounts = array();	
		
	if(!is_array($xmt_accounts))
		$xmt_accounts = array();	
	
	foreach($xmt_accounts as $acc=>$acc_set){
		$php_wid_function = '
			function widget_xmt_'.$acc.'($args){
				widget_xmt($args, \''.$acc.'\');
			}
			function widget_xmt_control_'.$acc.'(){
				widget_xmt_control(\''.$acc.'\');
			}
		';
		eval($php_wid_function);
		
		if($acc_set['display_mode']['scrolling']['enable'] && $acc_set['display_mode']['scrolling']['animate']['enable']){
			add_action('init', 'xmt_init');}
	}
	
	function xmt_install () {
		require_once(xmt_base_dir.'/installer.php');
	}
	register_activation_hook(__FILE__,'xmt_install');
	
	function xmt_init() {
		if (!is_admin()) {
			wp_enqueue_script('jquery');
			wp_enqueue_script('xmt_marquee', xmt_get_dir('url').'/js/marquee.js');
		}
	}
	
	xmt_inc('inc');
	
	define('xmt_base_url', xmt_get_dir('url'));
	
	function xmt_css_cst($profile){
		global $xmt_accounts;
		$cfg = $xmt_accounts[$profile];
						
		$avatar_width = intval($cfg['tweet']['avatar']['size']['w']);
		$avatar_height = intval($cfg['tweet']['avatar']['size']['h']);
		$show_avatar = intval($cfg['tweet']['avatar']['show']);
		$custom_css = $cfg['css']['custom_css'];
				
		if($avatar_width && $avatar_height){
			$css .= '#xmt_'.$profile.'_wid.xmt .tweet_avatar{width:'.$avatar_width.'px;height:'.$avatar_height.'px} ';
			if($show_avatar)
				$css .= '#xmt_'.$profile.'_wid.xmt ul li.tweet_list{min-height:'.($avatar_height+7).'px} ';
		}else{
			if($show_avatar)
				$css .= '#xmt_'.$profile.'_wid.xmt ul li.tweet_list{min-height:57px} ';
		}
		
		if($custom_css){
			$custom_css = str_replace('{xmt_id}', '#xmt_'.$profile.'_wid', $custom_css);
			$css .= $custom_css.' ';
		}
		
		if($css)
			echo '<style type="text/css">/*<![CDATA[*/ '.$css.' /*]]>*/</style>';	
	}
	
	function xmt_css(){	
		global $xmt_accounts;
		
		$profiles = array_keys($xmt_accounts);
		echo '<link rel="stylesheet" href="'.xmt_get_dir('url').'/css/css.php" type="text/css" media="screen" />';
		
		foreach($xmt_accounts as $acc=>$acc_set)
			xmt_css_cst($acc);		
	}
	add_action('wp_print_styles', 'xmt_css');

	function xmt($args = array(), $profile){	
		widget_xmt($args, $profile);
	}

	function xmt_short_code($atts) {
		extract(shortcode_atts(array(
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
			'profile' => '',
		), $atts));

		$args = array(
			'before_widget' => $before_widget,
			'after_widget' => $after_widget,
			'before_title' => $before_title,
			'after_title' => $after_title,
		);
		
		ob_start();
		xmt($args, $profile);
		$res = ob_get_contents();
		ob_end_clean();
		
		return $res;
	}
	
	if(function_exists('add_shortcode'))
		add_shortcode('xmt', 'xmt_short_code');
	
	function widget_xmt($args, $profile){		
		global $xmt_timed;
		global $xmt_accounts;
				
		$xmt_timed = time();
		
		xmt_log('Starting to generate output');		

		if(!array_key_exists($profile, $xmt_accounts))
			return;
		$cfg = $xmt_accounts[$profile];
		
		extract($args);
		
		$cur_role = xmt_get_role();
		$allow_tweet = false;
		$msg = '';
		
		if($cur_role == 'administrator' && $cfg['tweet']['oauth_use'] && $cfg['tweet']['show_post_form'])
			$allow_tweet = true;
		
		if($allow_tweet && isset($_POST['cmd_xmt_'.$profile.'_post'])){
			$t_tweet = trim(xmt_form_post('txa_xmt_'.$profile.'_tweet'));
			if($t_tweet == '')
				$msg = 'Your tweet is empty!';
			if(strlen($t_tweet) > 140)
				$msg = 'Your tweet exceeds 140 characters!';
			if($msg == ''){			
				xmt_req('post-tweet', $profile,array('tweet' => $t_tweet), false);
				$msg = 'Your tweet has been posted';
				
				$cfg['tweet']['cache']['tweet_cache']['date'] = 0;
				$xmt_accounts[$profile] = $cfg;
				update_option('xmt_accounts', $xmt_accounts);
			}
		}
		
		$res = xmt_get_tweets($profile);
		
		$tweet_string = $cfg['tweet']['layout'];
		$show_avatar = intval($cfg['tweet']['avatar']['show']);
		
		$link_on_title = intval($cfg['widget']['link_title']);
		$show_hr = intval($cfg['tweet']['show_hr']);	
		
		$scroll_cfg = $cfg['display_mode']['scrolling'];
		$scroll_mode = intval($scroll_cfg['enable']);
		$scroll_h = intval($scroll_cfg['height']);
        $scroll_ani = intval($scroll_cfg['animate']['enable']);
		$scroll_ani_amount = intval($scroll_cfg['animate']['amount']);
		$scroll_ani_delay = intval($scroll_cfg['animate']['delay']);
        $scroll_ani_dir = $scroll_cfg['animate']['direction'];	
		
		$new_tab_link = intval($cfg['other']['open_link_on_new_window']);	
		
		$username = $cfg['tweet']['username'];
				
		xmt_timed('Build Body - Start');
		if(count($res) == 0) 
			return;		
		echo $before_widget;
		if ($cfg['widget']['title'] != ''){
			echo $before_title;
			
			if($link_on_title)
				echo '<a href="http://twitter.com/'.$username.'" rel="external nofollow" '.($new_tab_link?'target="_blank"':'').'>';
			echo $cfg['widget']['title'];

			if($link_on_title)
				echo '</a>';
			
			echo $after_title;		
		}

		echo '<div id="xmt_'.$profile.'_wid" class="xmt xmt_'.$profile.'">';
		xmt_header_style($profile);

		echo xmt_replace_vars($cfg['widget']['custom_text']['header'], $profile);
		
		if($allow_tweet){
			echo '<a name="xmt_'.$profile.'"></a>';
			if($msg)
				echo '<div>'.__($msg, 'xmt').'</div>';
			echo '<form action="#xmt_'.$profile.'" method="post">'.__('What\'s happening?', 'xmt').'<br/><textarea name="txa_xmt_'.$profile.'_tweet"></textarea><input type="submit" class="submit" name="cmd_xmt_'.$profile.'_post" value="'.__('Tweet', 'xmt').'"/><div class="clear"></div></form>';
		}

		if($scroll_mode){
			if($scroll_ani){
				echo '<div id="xmt_'.$profile.'_tweet_area_cont" style="height:'.$scroll_h.'px;overflow:hidden;position:relative"><div id="xmt_'.$profile.'_tweet_area">';
			}else{
				echo '<div style="max-height:'.$scroll_h.'px;overflow:auto">';		
			}
		} 
		echo '<ul class="tweet_area">';
		$tweet_string = convert_smilies(html_entity_decode($tweet_string));
		foreach($res as $sts_id=>$row){			
			echo '<li class="tweet_list">';
				if($show_hr) 
					echo '<hr />';
				
				if($show_avatar){					
					echo '<a href="'.$row['author_url'].'" '.($new_tab_link?'target="_blank"':'').'><img '.$avatar_style.' class="tweet_avatar" src="'.$row['author_img'].'" alt="'.$row['author_name'].'"/></a>';				
				}
							
				$status_link = 'http://twitter.com/'.$row['author'].'/status/'.$sts_id;
				$retweet_link = 'http://twitter.com/home?status='.urlencode('RT @'.$row['author'].' '.strip_tags($row['tweet']));
				$reply_link = 'http://twitter.com/home?status='.urlencode('@'.$row['author']).'&amp;in_reply_to_status_id='.$sts_id.'&amp;in_reply_to='.urlencode($row['author']);
				
				$tmp_str = str_replace('@screen_name_plain', $row['author'], $tweet_string);
				$tmp_str = str_replace('@screen_name', '<a href="'.$row['author_url'].'"  '.($new_tab_link?'target="_blank"':'').' rel="external nofollow">'.$row['author'].'</a>', $tmp_str);
				$tmp_str = str_replace('@name_plain', $row['author_name'], $tmp_str);
				$tmp_str = str_replace('@name', '<a href="'.$row['author_url'].'"  '.($new_tab_link?'target="_blank"':'').' rel="external nofollow">'.$row['author_name'].'</a>', $tmp_str);
				$tmp_str = str_replace('@date', $row['timestamp'], $tmp_str);
				$tmp_str = str_replace('@source', $row['source'], $tmp_str);
				$tmp_str = str_replace('@tweet', $row['tweet'], $tmp_str);
				$tmp_str = str_replace('@reply_url', $reply_link, $tmp_str);
				$tmp_str = str_replace('@reply_link', '<a href="'.$reply_link.'"  '.($new_tab_link?'target="_blank"':'').' rel="external nofollow">'.__('reply', 'xmt').'</a>', $tmp_str);
				$tmp_str = str_replace('@retweet_url', $retweet_link, $tmp_str);
				$tmp_str = str_replace('@retweet_link', '<a href="'.$retweet_link.'"  '.($new_tab_link?'target="_blank"':'').' rel="external nofollow">'.__('retweet', 'xmt').'</a>', $tmp_str);
				$tmp_str = str_replace('@status_url', $status_link, $tmp_str);
				
				echo $tmp_str;
			echo '</li>';
		}
		echo '</ul>';
		if($show_hr) 
			echo '<hr />';
		if($scroll_mode){
			if($scroll_ani){
				$pos_str = '';
				if($scroll_ani_dir == 'down')
					$pos_str = 'xmt_'.$profile.'_ta.style.top = xmt_'.$profile.'_ta_limit + "px";';
				else
					$pos_str = 'xmt_'.$profile.'_ta.style.top = '.$scroll_h.' + "px";';
					
				echo '</div></div>';							
				echo '
					<script language="javascript" type="text/javascript">
						//<![CDATA[
							jQuery(document).ready(function(){
								var xmt_'.$profile.'_ta = document.getElementById("xmt_'.$profile.'_tweet_area");
								var xmt_'.$profile.'_ta_limit = xmt_'.$profile.'_ta.offsetHeight * -1;							
								$xmt_marquee.config.refresh = '.$scroll_ani_delay.';
								$xmt_marquee.add("#xmt_'.$profile.'_tweet_area_cont","#xmt_'.$profile.'_tweet_area","'.$scroll_ani_dir.'",'.$scroll_ani_amount.',true);
								'.$pos_str.'
								$xmt_marquee.start();
							})
						//]]>
					</script>
				';
			}else
				echo '</div>';			
		} 
					
		echo xmt_replace_vars($cfg['widget']['custom_text']['footer'], $profile); 

		if ($cfg['other']['show_credit']){
			echo '<div class="credit"><a href="http://xhanch.com/wp-plugin-my-twitter/" rel="section" title="'.__('Xhanch My Twitter - The best WordPress plugin to integrate your WordPress website with your Twitter accounts', 'xmt').'">'.__('My Twitter', 'xmt').'</a>, <a href="http://xhanch.com/" rel="section" title="'.__('Developed by Xhanch Studio', 'xmt').'">'.__('by Xhanch', 'xmt').'</a></div>';
		}
		echo '</div>';
		echo $after_widget;
		xmt_timed('Build Body - Finished');
		xmt_timed('Finished');
	}

	function widget_xmt_control($id){	
?>
		<a href="admin.php?page=xhanch-my-twitter/admin/setting.php&profile=<?php echo $id; ?>"><?php echo __('Click here to configure this plugin', 'xmt'); ?></a>
<?php		
	}

	function widget_xmt_init(){
		global $xmt_accounts;
		foreach($xmt_accounts as $acc=>$acc_set){
			wp_register_sidebar_widget('xmt_'.$acc, __('Xhanch - My Twitter', 'xmt').' : '.$acc, 'widget_xmt_'.$acc);
			register_widget_control('xmt_'.$acc, 'widget_xmt_control_'.$acc, 300, 200 );
		}
	}
	add_action("plugins_loaded", "widget_xmt_init");

	if(is_admin()){
		function xmt_admin_menu() {	
			if(!defined('xhanch_root')){
				add_menu_page(
					'Xhanch', 
					'Xhanch', 
					8, 
					'xhanch-my-twitter/admin/xhanch.php', 
					'',
					'http://xhanch.com/icon-16x16.jpg'
				);
				define('xhanch_root', 'xhanch-my-twitter/admin/xhanch.php');
			}
			add_submenu_page(
				xhanch_root, 
				__('My Twitter', 'xmt'), 
				__('My Twitter', 'xmt'), 
				8, 
				'xhanch-my-twitter/admin/setting.php', 
				''
			);
		}
		add_action('admin_menu', 'xmt_admin_menu');
	}
	
	function xmt_inc($rel_path){	
		$path = xmt_base_dir.'/'.$rel_path;		
		$dir = dir($path);	
		while($file = $dir->read()){
			if($file == '.' || $file == '..')
				continue;
			$target = $path.'/'.$file;			
			if(is_dir($target))
				 xmt_inc($rel_path.'/'.$file);
			elseif(substr($target,-4) == '.php'){				
				require_once $target;	
			}
		}
		$dir->close();
	}
?>