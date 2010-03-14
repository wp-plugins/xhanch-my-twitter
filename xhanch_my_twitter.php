<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: Twitter plugin for wordpress
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 1.3.4
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