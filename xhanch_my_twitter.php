<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 1.3.5
	*/

	define('xhanch_my_twitter', true);

	function xhanch_my_twitter_install () {
		require_once(dirname(__FILE__).'/installer.php');
	}
	register_activation_hook(__FILE__,'xhanch_my_twitter_install');

	require_once(dirname(__FILE__).'/xhanch_my_twitter.function.php');	
	require_once(dirname(__FILE__).'/xhanch_my_twitter_header_style.php');	
	
	function xhanch_my_twitter_css() {
		echo '<link rel="stylesheet" href="'.xhanch_my_twitter_get_dir('url').'/css.css" type="text/css" media="screen" />';
	}
	add_action('wp_print_styles', 'xhanch_my_twitter_css');

	function xhanch_my_twitter($args = array()){	
		widget_xhanch_my_twitter($args);
	}
	
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
		if (get_option("xhanch_my_twitter_title")!='')
			echo $before_title.get_option("xhanch_my_twitter_title").$after_title;				
		
		echo '<div id="xhanch_my_twitter">';
		xhanch_my_twitter_header_style();

		echo convert_smilies(html_entity_decode(get_option("xhanch_my_twitter_text_header")));

		if($scroll_mode){
			if($scroll_animate){
				echo '<marquee direction="up" onmouseover="this.stop()" onmouseout="this.start()" scrolldelay="'.$scroll_animate_delay.'" scrollamount="'.$scroll_animate_amount.'" height="'.$scroll_h.'px" style="height:'.$scroll_h.'px;overflow:hidden">';
			}else
				echo '<div style="max-height:'.$scroll_h.'px;overflow:auto">';			
		} 
		echo '<ul id="xhanch_my_twitter_list">';
		foreach($res as $row){
			echo '<li class="tweet_list">';
				if($show_hr) 
					echo '<hr />';
				echo '<div>';
				if($show_post_by != '' && $show_post_by != 'hidden_personal'){
					echo '<a href="'.$row['author_url'].'">';
					if($show_post_by == 'avatar'){
						echo '<img class="avatar" src="'.$row['author_img'].'" alt="'.$row['author_name'].'"/></a>';					
					}else if($show_post_by == 'avatar_name'){
						echo '<img class="avatar" src="'.$row['author_img'].'" alt="'.$row['author_name'].'"/> '.$row['author_name'].': </a>';			
					}else{ 
						echo $row['author_name'].'</a>: '; 
					}
				}
				echo $row['tweet'];
				echo $row['timestamp']; 
				echo '<div class="clear"></div>';
				echo '</div>';
			echo '</li>';
		}
		echo '</ul>';
		if($scroll_mode){
			if($scroll_animate)
				echo '</marquee>';
			else
				echo '</div>';			
		} 
					
		echo convert_smilies(html_entity_decode(get_option("xhanch_my_twitter_text_footer"))); 

		if (get_option("xhanch_my_twitter_credit")){
			echo '<div class="credit"><a href="http://xhanch.com/wp-plugin-my-twitter/" rel="section" title="Xhanch My Twitter - A free WordPress plugin to display your latest tweets from Twitter">My Twitter</a>, <a href="http://xhanch.com/" rel="section" title="Developed by Xhanch Studio">by Xhanch</a></div>';
		}
		echo '</div>';
		echo $after_widget;
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