<?php
	/*
		Plugin Name: Xhanch - My Twitter
		Plugin URI: http://xhanch.com/wp-plugin-my-twitter/
		Description: The best Twitter plugin to integrate your Wordpress and your Twitter accounts
		Author: Susanto BSc (Xhanch Studio)
		Author URI: http://xhanch.com
		Version: 2.7.0
	*/
	
	define('xmt', true);
	define('xmt_base_dir', dirname(__FILE__));
		
	global $xmt_tmd;
	global $xmt_cfg_def;
		
	load_plugin_textdomain('xmt', WP_PLUGIN_URL.'/xhanch-my-twitter/lang/', 'xhanch-my-twitter/lang/');
	
	xmt_inc('inc');

	$xmt_cfg_def = array(
		'ttl' => 'Latest Tweets',
		'nme' => '',
		'lnk_ttl' => 0,
		'hdr_sty' => 'default',
		'cst_hdr_txt' => '',
		'cst_ftr_txt' => '',
		'twt_usr_nme' => '',
		'oah_use' => 0,
		'csm_key' => '',
		'csm_sct' => '',
		'oah_tkn' => '',
		'oah_sct' => '',
		'oah_tkn' => '',
		'oah_sct' => '',
		'ord' => 'lto',	
		'cnt' => '5',
		'trc_len' => '0',
		'trc_chr' => '...',
		'gmt_add' => '0',
		'dtm_fmt' => 'd/m/Y H:i:s',
		'twt_lyt' => '@tweet - posted on @date',
		'shw_hrl' => 0,
		'shw_pst_frm' => 1,
		'shw_org_rtw' => 0,
		'twt_new_pst' => 0,
		'twt_new_pst_lyt' => '@title - @url',
		'twt_upd_pst' => 0,
		'twt_upd_pst_lyt' => '@title - @url (Updated)',
		'twt_new_pag' => 0,
		'twt_new_pag_lyt' => '@title - @url',
		'twt_upd_pag' => 0,
		'twt_upd_pag_lyt' => '@title - @url (Updated)',
		'clc_usr_tag' => 1,
		'clc_hsh_tag' => 1,
		'shw_hsh_tag' => 1,
		'clc_url' => 1,
		'url_lyt' => '',
		'avt_shw' => 1,
		'avt_szw' => 0,
		'avt_szh' => 0,
		'inc_rpl_fru' => 0,
		'inc_rpl_tou' => 0,
		'inc_rtw' => 0,
		'inc_drc_msg' => 0,
		'ctn_kwd' => '',
		'ecl_kwd' => '',
		'cch_enb' => 1,
		'cch_exp' => 60,	
		'imp_itv' => 60,	
		'thm' => 'default',
		'cst_css' => '',
		'shw_crd' => 0,
		'cvr_sml' => 1,
		'lnk_new_tab' => 1,
		'tmp_oah_tkn' => '',
		'tmp_oah_sct' => ''
	);
	$path = xmt_base_dir.'/theme';		
	$dir = dir($path);	
	while($thm = $dir->read()){
		if($thm == '.' || $thm == '..' || substr($thm,-5) == '.html')
			continue;
		$target = $path.'/'.$thm.'/conf.php';
		$tpl_cfg = array();
		if(file_exists($target))
			require_once $target;
		$xmt_cfg_def = array_merge($xmt_cfg_def, $tpl_cfg);
	}
	$dir->close();

	define('xmt_base_url', xmt_get_dir('url'));
	
	function xmt_itl(){
		$upd_res = false;
		require_once(xmt_base_dir.'/installer.php');	
		return $upd_res;
	}
	$upd_res = xmt_itl();
	if(!$upd_res){
		define('xmt_itl_wrn_msg', $wpdb->last_query);
		function xmt_itl_wrn(){
			global $wpdb;
			echo '
				<div id="xmt-itl-wrn" class="updated fade"><p>					
					Oops, there has been a problem when upgrading <b>Xhanch - My Twitter</b><br/>
					Cannot execute this query:<br/>
					'.xmt_itl_wrn_msg.'					
				</p></div>
			';
		}
		add_action('admin_notices', 'xmt_itl_wrn');		
	}
					
	function xmt_css(){			
		echo '<link rel="stylesheet" href="'.xmt_get_dir('url').'/css/css.php" type="text/css" media="screen" />';
		
		$acc_lst = xmt_acc_lst();
		foreach($acc_lst as $acc){
			$cfg = xmt_acc_cfg_get($acc);

			$avt_szw = intval($cfg['avt_szw']);
			$avt_szh = intval($cfg['avt_szh']);
			$avt_shw = intval($cfg['avt_shw']);
			$cst_css = $cfg['cst_css'];
					
			if($avt_szw && $avt_szh){
				$css .= '#xmt_'.$acc.'_wid.xmt .tweet_avatar{width:'.$avt_szw.'px;height:'.$avt_szh.'px} ';
				if($avt_shw)
					$css .= '#xmt_'.$acc.'_wid.xmt ul li.tweet_list{min-height:'.($avt_szh+7).'px} ';
			}else{
				if($avt_shw)
					$css .= '#xmt_'.$acc.'_wid.xmt ul li.tweet_list{min-height:57px} ';
			}
			
			if($cst_css){
				$cst_css = str_replace('{xmt_id}', '#xmt_'.$acc.'_wid', $cst_css);
				$css .= xmt_css_minify($cst_css).' ';
			}
			
			if($css)
				echo '<style type="text/css">/*<![CDATA[*/ '.$css.' /*]]>*/</style>';
		}		
	}
	add_action('wp_print_styles', 'xmt_css');

	function xmt($args = array(), $acc){	
		widget_xmt($args, $acc);
	}

	function xmt_short_code($atts){
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
	
	function widget_xmt($args, $acc){
		global $wpdb;
		global $xmt_tmd;
				
		$xmt_tmd = time();		
		xmt_log('Starting to generate output');		

		$cfg = xmt_acc_cfg_get($acc);

		extract($args);
		
		$cur_role = xmt_get_role();
		$alw_twt = false;
		$msg = '';
		
		if($cur_role == 'administrator' && $cfg['oah_use'] && $cfg['shw_pst_frm'])
			$alw_twt = true;

		xmt_twt_imp($acc, $cfg);

		if($alw_twt && isset($_POST['cmd_xmt_'.$acc.'_post'])){
			$t_tweet = trim(xmt_form_post('txa_xmt_'.$acc.'_tweet'));
			if($t_tweet == '')
				$msg = 'Your tweet is empty!';
			if(strlen($t_tweet) > 140)
				$msg = 'Your tweet exceeds 140 characters!';
			if($msg == ''){			
				xmt_twt_oah_twt_pst($cfg, $t_tweet);
				$msg = 'Your tweet has been posted';
				xmt_twt_cch_rst($acc);
				xmt_twt_imp($acc, $cfg);
			}
		}

		if($cur_role == 'administrator' && isset($_GET['xmt_'.$acc.'_twt_id'])){
			$twt_id = trim(xmt_form_get('xmt_'.$acc.'_twt_id'));
			xmt_twt_del($acc, $twt_id);
			xmt_twt_cch_rst($acc);
			$msg = 'Your tweet has been deleted';
		}
		
		$res = xmt_twt_get($acc, $cfg);	
		if(!$res || !is_array($res))
			$res = array();
		
		$tpl = xmt_base_dir.'/theme/'.$cfg['thm'].'/widget.php';
		if(!file_exists($tpl))
			$tpl = xmt_base_dir.'/theme/default/widget.php';

		include $tpl;
		
		xmt_tmd('Finished');
	}
		
	function xmt_tweet_post($post_id){
		$info = get_post($post_id);
		$url = get_permalink($post_id);		

		$acc_lst = xmt_acc_lst();
		foreach($acc_lst as $acc){	
			$cfg = xmt_acc_cfg_get($acc);
			if($cfg['oah_use'] && $cfg['twt_new_pst']){
				if($info->post_type == 'post' && !$cfg['twt_new_pst'])
					return;
				if($info->post_type == 'page' && !$cfg['twt_new_pag'])
					return;
				
				if($info->post_type == 'post')
					$t_tweet = $cfg['twt_new_pst_lyt'];
				elseif($info->post_type == 'page')
					$t_tweet = $cfg['twt_new_pag_lyt'];
				
				$t_tweet = str_replace('@title', $info->post_title, $t_tweet);
				$t_tweet = str_replace('@url', $url, $t_tweet);
				$t_tweet = str_replace('@summary', substr(strip_tags($info->post_content), 0, 100), $t_tweet);
				
				xmt_twt_oah_twt_pst($cfg, $t_tweet);				
				xmt_twt_cch_rst($acc);
			}
		}
	}
	add_action('new_to_publish', 'xmt_tweet_post');
	add_action('draft_to_publish', 'xmt_tweet_post');
	add_action('pending_to_publish', 'xmt_tweet_post');
		
	function xmt_tweet_updated_post($post_id){
		$info = get_post($post_id);
		$url = get_permalink($post_id);		

		$acc_lst = xmt_acc_lst();
		foreach($acc_lst as $acc){	
			$cfg = xmt_acc_cfg_get($acc);
			if($cfg['oah_use'] && $cfg['twt_upd_pst']){
				if($info->post_type == 'post' && !$cfg['twt_upd_pst'])
					return;
				if($info->post_type == 'page' && !$cfg['twt_upd_pag'])
					return;
				
				if($info->post_type == 'post')
					$t_tweet = $cfg['twt_upd_pst_lyt'];
				elseif($info->post_type == 'page')
					$t_tweet = $cfg['twt_upd_pag_lyt'];
				
				$t_tweet = str_replace('@title', $info->post_title, $t_tweet);
				$t_tweet = str_replace('@url', $url, $t_tweet);
				$t_tweet = str_replace('@summary', substr(strip_tags($info->post_content), 0, 100), $t_tweet);
				
				xmt_twt_oah_twt_pst($cfg, $t_tweet);				
				xmt_twt_cch_rst($acc);
			}
		}
	}
	add_action('publish_to_publish', 'xmt_tweet_updated_post');

	if(is_admin()){
		function xmt_admin_menu() {	
			if(!defined('xhanch_root')){
				add_menu_page(
					'Xhanch', 
					'Xhanch', 
					8, 
					'xhanch-my-twitter/admin/xhanch.php', 
					'',
					xmt_get_dir('url').'/img/icon.jpg'
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
	
	class xmt_wgt extends WP_Widget{
		function xmt_wgt(){
			$wgt_opt = array('classname' => 'xmt', 'description' => 'Display your latest tweets from a profile');
			$ctr_opt = array('width' => 300, 'height' => 200);
			$this->WP_Widget(false, 'XMT: Latest Tweets', $wgt_opt, $ctr_opt);
		}

		function widget($arg, $cfg){
			widget_xmt($arg, $cfg['prf']);
		}

		function update($new_cfg, $old_cfg) {
			$cfg = $old_cfg;
			$cfg['prf'] = $new_cfg['prf'];
			return $cfg;
		}

		function form($cfg){
			$acc_lst = xmt_acc_lst();
			$cbo_prf = '<option value="" '.(''==$cfg['prf']?'selected="selected"':'').'>- Select Profile -</option>';
			foreach($acc_lst as $acc){
				$cbo_prf .= '<option value="'.$acc.'" '.($acc==$cfg['prf']?'selected="selected"':'').'>'.ucfirst($acc).'</option>';
			}
?>
			<table style="width:350px">
				<tr>
					<td style="width:100px"><label for="Profile">Profile</label></td>
					<td style="width:200px"><select id="<?php echo $this->get_field_id('prf'); ?>" name="<?php echo $this->get_field_name('prf'); ?>" style="width:200px"><?php echo $cbo_prf; ?></select></td>
				</tr>
				<tr>
					<td colspan="2"><a href="admin.php?page=xhanch-my-twitter/admin/setting.php"><?php echo __('Click here to manage your profiles', 'xmt'); ?></a></td>
				</tr>
			</table>
<?php 
		}
	}

	add_action('widgets_init', create_function('', 'return register_widget("xmt_wgt");'));
?>