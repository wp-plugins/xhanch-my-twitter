<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 1.7.1
	*/

	define('xhanch_my_twitter', true);
	global $xhanch_my_twitter_timed;

	function xhanch_my_twitter_install () {
		require_once(dirname(__FILE__).'/installer.php');
	}
	register_activation_hook(__FILE__,'xhanch_my_twitter_install');

	require_once(dirname(__FILE__).'/xhanch_my_twitter.function.php');	
	require_once(dirname(__FILE__).'/xhanch_my_twitter_header_style.php');	
	
	function xhanch_my_twitter_css() {		
		$avatar_width = get_option('xhanch_my_twitter_avatar_width');
		$avatar_height = get_option('xhanch_my_twitter_avatar_height');
		$show_post_by = get_option('xhanch_my_twitter_show_post_by');

		echo '<link rel="stylesheet" href="'.xhanch_my_twitter_get_dir('url').'/css.css" type="text/css" media="screen" />';

		$css = '';
		if($avatar_width && $avatar_height){
			$css .= '#xhanch_my_twitter .tweet_avatar{width:'.$avatar_width.'px;height:'.$avatar_height.'px} ';
			if($show_post_by == 'avatar' || $show_post_by == 'avatar_name')
				$css .= '#xhanch_my_twitter .tweet_list{min-height:'.($avatar_height+5).'px} ';
		}else{
			if($show_post_by == 'avatar' || $show_post_by == 'avatar_name')
				$css .= '#xhanch_my_twitter .tweet_list{min-height:53px} ';
		}

		if($css)
			echo '<style type="text/css">/*<![CDATA[*/ '.$css.' /*]]>*/</style>';		
	}
	add_action('wp_print_styles', 'xhanch_my_twitter_css');

	function xhanch_my_twitter($args = array()){	
		widget_xhanch_my_twitter($args);
	}

	function xhanch_my_twitter_short_code($atts) {
		extract(shortcode_atts(array(
			'before_widget' => '',
			'after_widget' => '',
			'before_title' => '',
			'after_title' => '',
		), $atts));

		$args = array(
			'before_widget' => $before_widget,
			'after_widget' => $after_widget,
			'before_title' => $before_title,
			'after_title' => $after_title,
		);

		xhanch_my_twitter($args);
	}
	add_shortcode('xhanch_my_twitter', 'xhanch_my_twitter_short_code');
	
	function widget_xhanch_my_twitter($args) {		
		global $xhanch_my_twitter_timed;
		$xhanch_my_twitter_timed = time();

		xhanch_my_twitter_log('Starting to generate output');		

		extract($args);

		$res = xhanch_my_twitter_get_tweets();
		$date_string = get_option('xhanch_my_twitter_date_string');
		$show_post_by = get_option('xhanch_my_twitter_show_post_by');
		$scroll_mode = intval(get_option('xhanch_my_twitter_scroll_enable'));
		$scroll_h = intval(get_option('xhanch_my_twitter_scroll_area_height'));
        $show_hr = intval(get_option('xhanch_my_twitter_show_hr'));
		$scroll_animate = intval(get_option('xhanch_my_twitter_scroll_animate'));
		$scroll_animate_amount = intval(get_option('xhanch_my_twitter_scroll_amount'));
		$scroll_animate_delay = intval(get_option('xhanch_my_twitter_scroll_delay'));
		$link_on_title = intval(get_option('xhanch_my_twitter_link_on_title'));	
		$username = get_option('xhanch_my_twitter_id');
		
		xhanch_my_twitter_timed('Build Body - Start');
		if(count($res) == 0) 
			return;		
		echo $before_widget;
		if (get_option("xhanch_my_twitter_title")!=''){
			echo $before_title;
			
			if($link_on_title)
				echo '<a href="http://twitter.com/'.$username.'" target="_blank">';
			echo get_option("xhanch_my_twitter_title");

			if($link_on_title)
				echo '</a>';
			
			echo $after_title;				
		}

		echo '<div id="xhanch_my_twitter">';
		xhanch_my_twitter_header_style();

		echo xhanch_my_twitter_replace_vars(get_option("xhanch_my_twitter_text_header"));

		if($scroll_mode){
			if($scroll_animate){
				echo '<div onmouseover="xmt_scroll_stop()" onmouseout="xmt_scroll()"  style="'.(xhanch_my_twitter_is_ie6()?'':'max-').'height:'.$scroll_h.'px;overflow:hidden"><div id="xhanch_my_twitter_tweet_area" style="margin-bottom:'.$scroll_h.'px">';
			}else{				
				echo '<div style="max-height:'.$scroll_h.'px;overflow:auto">';		
			}
		} 
		echo '<ul>';
		foreach($res as $row){
			echo '<li class="tweet_list">';
				if($show_hr) 
					echo '<hr />';
				
				if($show_post_by != '' && $show_post_by != 'hidden_personal'){					
					echo '<a href="'.$row['author_url'].'">';
					if($show_post_by == 'avatar'){
						echo '<img '.$avatar_style.' class="tweet_avatar" src="'.$row['author_img'].'" alt="'.$row['author_name'].'"/></a>';					
					}else if($show_post_by == 'avatar_name'){
						echo '<img class="tweet_avatar" src="'.$row['author_img'].'" alt="'.$row['author_name'].'"/> '.$row['author_name'].': </a>';			
					}else{ 
						echo $row['author_name'].'</a>: '; 
					}
				}
				echo $row['tweet'];
				if($row['timestamp'])
					echo ' '.str_replace('@date', $row['timestamp'], convert_smilies(html_entity_decode($date_string))); 
			echo '</li>';
		}
		echo '</ul>';
		if($show_hr) 
			echo '<hr />';
		if($scroll_mode){
			if($scroll_animate){
				echo '</div></div>';							
				echo '
					<script language="javascript" type="text/javascript">
						//<![CDATA[
							var xmt_pos = '.$scroll_h.';
							var xmt_ti;
							var xmt_tweet_area = document.getElementById("xhanch_my_twitter_tweet_area");
							var xmt_ta_limit = xmt_tweet_area.offsetHeight * -1;

							function xmt_scroll(){
								xmt_scroll_stop();
								xmt_pos = xmt_pos - '.$scroll_animate_amount.';
								if(xmt_pos < xmt_ta_limit)
									xmt_pos = '.$scroll_h.';
								xmt_tweet_area.style.marginTop = xmt_pos.toString() + "px";
								xmt_ti = setTimeout("xmt_scroll()",50);
							}
							function xmt_scroll_stop(){
								if(xmt_ti)
									clearTimeout(xmt_ti);
							}
							xmt_tweet_area.style.marginTop = xmt_pos.toString() + "px";
							xmt_scroll();
						//]]>
					</script>
				';
			}else
				echo '</div>';			
		} 
					
		echo xhanch_my_twitter_replace_vars(get_option("xhanch_my_twitter_text_footer")); 

		if (get_option("xhanch_my_twitter_credit")){
			echo '<div class="credit"><a href="http://xhanch.com/wp-plugin-my-twitter/" rel="section" title="Xhanch My Twitter - A free WordPress plugin to display your latest tweets from Twitter">My Twitter</a>, <a href="http://xhanch.com/" rel="section" title="Developed by Xhanch Studio">by Xhanch</a></div>';
		}
		echo '</div>';
		echo $after_widget;
		xhanch_my_twitter_timed('Build Body - Finished');
		xhanch_my_twitter_timed('Finished');
	}

	function xhanch_my_twitter_control(){	
?>
		<a href="admin.php?page=xhanch-my-twitter">Click here to configure this plugin</a>
<?php		
	}

	function widget_xhanch_my_twitter_init(){
		register_sidebar_widget('Xhanch - My Twitter', 'widget_xhanch_my_twitter');
		register_widget_control('Xhanch - My Twitter', 'xhanch_my_twitter_control', 300, 200 );     
	}
	add_action("plugins_loaded", "widget_xhanch_my_twitter_init");

	if(is_admin()){
		function xhanch_my_twitter_admin_menu() {	
			if(!defined('xhanch_root')){
				add_menu_page(
					'Xhanch', 
					'Xhanch', 
					8, 
					'xhanch', 
					'xhanch_intro',
					'http://xhanch.com/icon-16x16.jpg'
				);
				define('xhanch_root', true);
			}
			add_submenu_page(
				'xhanch', 
				'My Twitter',
				'My Twitter', 
				8, 
				'xhanch-my-twitter', 
				'xhanch_my_twitter_setting'
			);
		}
		require_once(dirname(__FILE__).'/admin/xhanch.php');
		require_once(dirname(__FILE__).'/admin/setting.php');
		add_action('admin_menu', 'xhanch_my_twitter_admin_menu');
	}
?>