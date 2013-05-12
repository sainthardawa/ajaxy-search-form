<?php
/**
 * @package Ajaxy
 */
/*
	Plugin Name: Ajaxy Live Search
	Plugin URI: http://ajaxy.org
	Description: Transfer wordpress form into an advanced ajax search form the same as facebook live search, This version supports themes and can work with almost all themes without any modifications
	Version: 2.1.8
	Author: Ajaxy Team
	Author URI: http://www.ajaxy.org
	License: GPLv2 or later
*/



define('AJAXY_SF_VERSION', '2.1.8');
define('AJAXY_SF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('AJAXY_THEMES_DIR', dirname(__FILE__)."/themes/");
define('AJAXY_SF_NO_IMAGE', plugin_dir_url( __FILE__ ) ."themes/default/images/no-image.gif");

require_once('widgets/search.php');
	
class AjaxyLiveSearch {
	private $noimage = '';
	
	function __construct(){
		$this->actions();
		$this->filters();
	}
	function actions(){
		//ACTIONS
		if(class_exists('AJAXY_SF_WIDGET')){
			add_action( 'widgets_init', create_function( '', 'return register_widget( "AJAXY_SF_WIDGET" );' ) );
		}
		add_action( 'wp_enqueue_scripts', array(&$this, "enqueue_scripts"));
		add_action( "admin_menu",array(&$this, "menu_pages"));
		add_action( 'wp_head', array(&$this, 'head'));
		add_action( 'admin_head', array(&$this, 'head'));
		add_action( 'wp_footer', array(&$this, 'footer'));
		add_action( 'admin_footer', array(&$this, 'footer'));
		add_action( 'wp_ajax_ajaxy_sf', array(&$this, 'get_search_results'));
		add_action( 'wp_ajax_nopriv_ajaxy_sf', array(&$this, 'get_search_results'));
		add_action( 'admin_notices', array(&$this, 'admin_notice') );
	}
	function filters(){
		//FILTERS
		add_filter( 'get_search_form', array(&$this, 'form'), 1);
		add_filter( 'ajaxy-overview', array(&$this, 'admin_page'), 10 );
	}
	function overview(){
		echo apply_filters('ajaxy-overview', 'main');
	}
	
	function menu_page_exists( $menu_slug ) {
		global $menu;
		foreach ( $menu as $i => $item ) {
				if ( $menu_slug == $item[2] ) {
						return true;
				}
		}
		return false;
	}
	
	function menu_pages(){
		if(!$this->menu_page_exists('ajaxy-page')){
			add_menu_page( _n( 'Ajaxy', 'Ajaxy', 1, 'ajaxy' ), _n( 'Ajaxy', 'Ajaxy', 1 ), 'Ajaxy', 'ajaxy-page', array(&$this, 'overview'));
		}
		add_submenu_page( 'ajaxy-page', __('Live Search'), __('Live Search'), 'manage_options', 'ajaxy_sf_admin', array(&$this, 'admin_page')); 
	}
	function admin_page(){
		require_once('classes/class-wp-ajaxy-sf-list-table.php');
		require_once('classes/class-wp-ajaxy-sf-themes-list-table.php');
		if(isset($_GET['edit'])){
			include_once('settings/sf_edit-form-advanced.php');
			return true;
		}
		$tab = (!empty($_GET['tab']) ? trim($_GET['tab']) : false);
		//include_once('sf_admin.php');
		
			?>
		<style type="text/css">
		.column-order, .column-limit_results, .column-show_on_search
		{
			text-align: center !important;
			width: 75px;
		}
		tr.row-no{
			color:#444 !important;
			background: #F3F3F3;
		}
		tr.row-no a.row-title{
			color:#444 !important;
		}
		</style>
		<div class="wrap">
			<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
			<h2>Ajaxy Live Search</h2>
			
			<ul class="subsubsub">
				<li class="active"><a href="<?php echo menu_page_url('ajaxy_sf_admin', false); ?>" class="<?php echo (!$tab ? 'current' : ''); ?>">General settings <span class="count"></span></a> |</li>
				<li class="active"><a href="<?php echo menu_page_url('ajaxy_sf_admin', false).'&tab=templates'; ?>" class="<?php echo ($tab == 'templates' ? 'current' : ''); ?>">Templates<span class="count"></span></a> |</li>
				<li class="active"><a href="<?php echo menu_page_url('ajaxy_sf_admin', false).'&tab=themes'; ?>" class="<?php echo ($tab == 'themes' ? 'current' : ''); ?>">Themes<span class="count"></span></a> |</li>
				<li class="active"><a href="<?php echo menu_page_url('ajaxy_sf_admin', false).'&tab=preview'; ?>" class="<?php echo ($tab == 'preview' ? 'current' : ''); ?>">Preview<span class="count"></span></a></li>
			</ul>
			<hr style="clear:both; display:block"/>
			<form action="" method="post">
			<?php wp_nonce_field(); ?>
			<?php if($tab == 'templates'): ?>
				<?php 
					if(isset($_POST['action'])){
						$action = trim($_POST['action']);
						$ids = (isset($_POST['template_id']) ? (array)$_POST['template_id'] : false);
						if($action == 'hide' && $ids){
							global $AjaxyLiveSearch;
							$k = 0;
							foreach($ids as $id){
								$setting = (array)$AjaxyLiveSearch->get_setting($id);
								$setting['show'] = 0;
								$AjaxyLiveSearch->set_setting($id, $setting);
								$k ++;
							}
							$message = $k.' templates hidden';
						}
						elseif($action == 'show' && $ids){
							global $AjaxyLiveSearch;
							$k = 0;
							foreach($ids as $id){
								$setting = (array)$AjaxyLiveSearch->get_setting($id);
								$setting['show'] = 1;
								$AjaxyLiveSearch->set_setting($id, $setting);
								$k ++;
							}
							$message = $k.' templates shown';
						}
					}
					elseif(isset($_GET['show']) && isset($_GET['type'])){
						global $AjaxyLiveSearch;
						$setting = (array)$AjaxyLiveSearch->get_setting($_GET['type']);
						$setting['show'] = (int)$_GET['show'];
						$AjaxyLiveSearch->set_setting($_GET['type'], $setting);
						$message = 'Template modified';
					}
				?>
				<?php $list_table = new WP_SF_List_Table(); ?>
				<div>
					<?php if ( $message ) : ?>
					<div id="message" class="updated"><p><?php echo $message; ?></p></div>
					<?php endif; ?>
					<?php $list_table->display(); ?>
				</div>
			<?php elseif($tab == 'themes'): ?>
				<?php 
					if(isset($_GET['theme']) && isset($_GET['apply'])){
						global $AjaxyLiveSearch;
						$AjaxyLiveSearch->set_style_setting('theme', $_GET['theme']);
						$message = $_GET['theme'].' theme applied';
					}
					$list_table = new WP_SF_THEMES_List_Table(); 
				?>
				<div>
					<?php if ( $message ) : ?>
					<div id="message" class="updated"><p><?php echo $message; ?></p></div>
					<?php endif; ?>
					<?php $list_table->display(); ?>
				</div>
			<?php elseif($tab == 'preview'): ?>
				<br class="clear" />
				<hr style="margin-bottom:20px"/>
				<div class="wrap">
				<?php ajaxy_search_form(); ?>
				</div>
				<hr style="margin:20px 0 10px 0"/>
				<p class="description">Use the form above to preview theme changes and settings, please note that the changes could vary from one theme to another, please contact the author of this plugin for more help</p>
				<hr style="margin:10px 0"/>
			<?php else:
				include_once('sf_admin.php');
			 endif; ?>
			 </form>
			 <div id="message-bottom" class="updated">
				<table>
					<tr>
						<td>
						<p>
							please donate some dollars for this project development and themes to be created, we are trying to make this project better, if you think it is worth it then u should support it.
							contact me at <a href="mailto:icu090@gmail.com">icu090@gmail.com</a> for support and development, please include your paypal id or donation id in your message.
						</p>
						</td>
						<td>
						<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=BGT78F6QUJC42"><img class="aligncenter size-full wp-image-180" title="btn_donateCC_LG" alt="" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" width="147" height="47"></a>
						</td>
					</tr>
				</table>
			</div>
		</div>
		<?php
	}
	
	function get_image_from_content($content, $width_max, $height_max){
		//return false;
		$theImageSrc = false;
		preg_match_all ('/<img[^>]+>/i', $content, $matches);
		$imageCount = count ($matches);
		if ($imageCount >= 1) {
			if (isset ($matches[0][0])) {
				preg_match_all('/src=("[^"]*")/i', $matches[0][0], $src);
				if (isset ($src[1][0])) {
					$theImageSrc = str_replace('"', '', $src[1][0]);
				}
			}
		}
		if($this->get_style_setting('aspect_ratio', 0 ) > 0){
			set_time_limit(0);
			try{
				set_time_limit(1);
				list($width, $height, $type, $attr) = @getimagesize( $theImageSrc );
				if($width > 0 && $height > 0){
					if($width < $width_max && $height < $height_max){
						return array('src' => $theImageSrc, 'width' => $width, 'height' => $height);	
					}
					elseif($width > $width_max && $height > $height_max){
						$percent_width = $width_max * 100/$width;
						$percent_height = $height_max * 100/$height;
						$percent = ($percent_height < $percent_width ? $percent_height : $percent_width);
						return array('src' => $theImageSrc, 'width' => intval($width * $percent / 100), 'height' => intval($height * $percent / 100));	
					}
					elseif($width < $width_max && $height > $height_max){
						$percent = $height * 100/$height_max;
						return array('src' => $theImageSrc, 'width' => intval($width * $percent / 100), 'height' => intval($height * $percent / 100));		
					}
					else{
						$percent = $width * 100/$width_max;
						return array('src' => $theImageSrc, 'width' => intval($width * $percent / 100), 'height' => intval($height * $percent / 100));	
					}
				}
			}
			catch(Exception $e){
				set_time_limit(60);
				return array('src' => $theImageSrc, 'width' => $this->get_style_setting('thumb_width', 50 ) , 'height' => $this->get_style_setting('thumb_height', 50 ) );
			}
		}
		else{
			return array('src' => $theImageSrc, 'width' => $this->get_style_setting('thumb_width', 50 ) , 'height' => $this->get_style_setting('thumb_height', 50 ) );	
		}
		return false;
	}
	function get_post_types()
	{
		$post_types = get_post_types(array('_builtin' => false),'objects');
		$post_types['post'] = get_post_type_object('post');
		$post_types['page'] = get_post_type_object('page');
		unset($post_types['wpsc-product-file']);
		return $post_types;
	}
	function get_excerpt_count()
	{
		return $this->get_style_setting('excerpt', 10);
	}
	function show_posts()
	{
		$post_types = $this->get_post_types();
		$show_posts = array();
		$show_m_posts = array();
		foreach($post_types as $post_type)
		{
			$setting = $this->get_setting($post_type->name);
			if($setting -> show == 1)
			{
			$show_posts[$post_type->name] = $setting->order;
			}
		}
		$scat = (array)$this->get_setting('category');
		if($scat['show'] == 1){
			$show_posts['category'] = $scat['order'];
		}
		if($scat['ushow'] == 1){
			$show_posts['post_category'] = $scat['order'];
		}
		asort($show_posts);
		foreach($show_posts as $key => $value)
		{
			$setting = $this->get_setting($key);
			$show_m_posts[$key] = $setting->title;
		}
		return $show_m_posts;
	}
	function show()
	{
		$m = $this->show_posts();
		return $m;
	}
	function set_templates($template, $html)
	{
		if(get_option('sf_template_'.$template) !== false)
		{
			update_option('sf_template_'.$template, stripslashes($html));
		}
		else
		{
			add_option('sf_template_'.$template, stripslashes($html));
		}
	}
	function set_setting($name, $value)
	{
		if(get_option('sf_setting_'.$name) !== false)
		{
			update_option('sf_setting_'.$name, json_encode($value));
		}
		else
		{
			add_option('sf_setting_'.$name, json_encode($value));
		}
	}
	function get_setting($name)
	{
		if(get_option('sf_setting_'.$name) !== false)
		{
			return json_decode(get_option('sf_setting_'.$name));
		}
		else
		{
			$values = array(
					'title' => $name, 
					'show' => 1,
					'ushow' => 0,
					'search_content' => 0,
					'limit' => 5,
					'order' => 0
					);
			return (object)$values;
		}
	}
	function set_style_setting($name, $value)
	{
		if(get_option('sf_style_'.$name) !== false)
		{
			update_option('sf_style_'.$name, $value);
		}
		else
		{
			add_option('sf_style_'.$name, $value);
		}
	}
	function get_style_setting($name, $default = '')
	{
		if(get_option('sf_style_'.$name) !== false)
		{
			return get_option('sf_style_'.$name);
		}
		else
		{
			return $default;
		}
	}
	function get_templates($template)
	{
		$template_post = "";
		if($template == 'category')
		{
			if(get_option('sf_template_category') !== false)
			{
				$template_post = get_option('sf_template_category');
			}
			else
			{
				$template_post = '<a href="{category_link}">{name}</a>';
			}
		}
		elseif($template == 'more')
		{
			if(get_option('sf_template_more') !== false)
			{
				$template_post = get_option('sf_template_more');
			}
			else
			{
				$template_post = '<a href="{search_url_escaped}"><span class="sf_text">See more results for "{search_value}"</span><span class="sf_small">Displaying top {total} results</span></a>';
			}
		}
		else
		{
			if(get_option('sf_template_'.$template) !== false)
			{
				$template_post = get_option('sf_template_'.$template);
			}
			else
			{
				$template_post = '<a href="{post_link}">{post_image_html}<span class="sf_text">{post_title} </span><span class="sf_small">Posted by {post_author} on {post_date_formatted}</span></a>';
			}
		}
		return $template_post;
	}
	function category($name, $pst_type = 'category', $limit = 5)
	{
		global $wpdb, $sitepress;

		$categories = array();
		$setting = (object)$this->get_setting($pst_type);

		$excludes = "";
		$excludes_array = array();
		if(isset($setting->excludes) && sizeof($setting->excludes) > 0 && is_array($setting->excludes)){
			$excludes = " AND $wpdb->terms.term_id NOT IN (".implode(',', $setting->excludes).")";
			$excludes_array = $setting->excludes;
		}
		$results = null;
		
		$query = "select distinct($wpdb->terms.name), $wpdb->terms.term_id,  $wpdb->term_taxonomy.taxonomy from $wpdb->terms, $wpdb->term_taxonomy where name like '%%%s%%' and $wpdb->term_taxonomy.taxonomy<>'link_category' and $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id $excludes limit 0, %d";
		$query = apply_filters("sf_category_query", $wpdb->prepare($query,  $name, $setting->limit), $name, $excludes_array, $setting->limit);

		$results = $wpdb->get_results($query);

		if(sizeof($results) > 0 && is_array($results) && !is_wp_error($results))
		{
			$unset_array = array('term_group', 'term_taxonomy_id', 'taxonomy', 'parent', 'count', 'cat_ID', 'cat_name', 'category_parent');
			foreach($results as $result)
			{
				$cat = get_term($result->term_id, $result->taxonomy);
				if($cat != null && !is_wp_error($cat))
				{
					$category_link = get_term_link($cat);
					$cat->category_link = $category_link;
					foreach($unset_array as $uarr)
					{
						unset($cat->{$uarr});
					}
					if($pst_type == 'post_category') {
						$limit = isset($setting->limit_posts) ? $setting->limit_posts : 5;
						$psts = $this->posts_by_term($cat->term_id, $limit);
						if(sizeof($psts) > 0) {
							$categories[$cat->term_id] = array('name' => $cat->name,'posts' => $this->posts_by_term($cat->term_id, $limit)); 
						}
					}
					else {
						$categories[] = $cat; 
					}
				}
			}
		}
		return $categories;
	}
	function posts($name, $post_type='post', $term_id = false)
	{
		global $wpdb;
		$posts = array();
		
		$setting = (object)$this->get_setting($post_type);
		$excludes = "";
		$excludes_array = array();
		if(isset($setting->excludes) && sizeof($setting->excludes) > 0 && is_array($setting->excludes)){
			$excludes = " AND $wpdb->terms.term_id NOT IN (".implode(',', $setting->excludes).")";
			$excludes_array = $setting->excludes;
		}
		$order_results = (isset($setting->order_results) ? " order by ".$setting->order_results : "");
		$results = array();
		$query = "select $wpdb->posts.ID from $wpdb->posts where (post_title like '%%%s%%' ".($setting->search_content == 1 ? "or post_content like '%%%s%%')":")")." and post_status='publish' and post_type='".$post_type."' $excludes $order_results limit 0, %d";
		$query = apply_filters("sf_posts_query", $wpdb->prepare($query,  ($setting->search_content == true ? array($name, $name):$name), $setting->limit), $name, $post_type, $excludes_array, $setting->search_content, $setting->order_results, $setting->limit);
		
		$results = $wpdb->get_results( $query );
		$date_format = get_option( 'date_format' );

		if(sizeof($results) > 0 && is_array($results) && !is_wp_error($results))
		{
			foreach($results as $result)
			{
				$pst = $this->post_object($result->ID, $term_id);
				if($pst){
					$posts[] = $pst; 
				}
			}
		}
		return $posts;
	}
	function posts_by_term($term_id, $limit = 5){
		$psts = get_posts(array('category' => $term_id, 'numberposts' => $limit));
		$posts = array();
		if(sizeof($psts) > 0) {
			foreach($psts as $p) {
				$posts[] = $this->post_object($p->ID);
			}
		}
		return $posts;
	}
	function post_object($id, $term_id = false) {
		$unset_array = array('post_type', 'post_date_gmt', 'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'post_content_filtered', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'guid', 'menu_order', 'post_mime_type', 'comment_count', 'ancestors', 'filter');
		$pst = get_post($id);
		if($term_id) {	
			if(!in_category($term_id, $pst->ID)){
				return false;
			}
		}
		$size = array('height' => $this->get_style_setting('thumb_height' , 50), 'width' => $this->get_style_setting('thumb_weight' , 50));
		if($pst != null)
		{
			$post_link = get_permalink($pst->ID);
			$post_thumbnail_id = get_post_thumbnail_id( $pst->ID);
			if( $post_thumbnail_id > 0)
			{
				$thumb = wp_get_attachment_image_src( $post_thumbnail_id, array($size['height'], $size['width']) );
				$pst->post_image =  (trim($thumb[0]) == "" ? AJAXY_SF_NO_IMAGE : $thumb[0]);
				$pst->post_image_html = '<img src="'.$pst->post_image.'" width="'.$size['width'].'" height="'.$size['height'].'"/>';
			}
			else
			{
				if($src = $this->get_image_from_content($pst->post_content, $size['height'], $size['width'])){
					$pst->post_image = $src['src'] ? $src['src'] : AJAXY_SF_NO_IMAGE;
					$pst->post_image_html = '<img src="'.$pst->post_image.'" width="'.$src['width'].'" height="'.$src['height'].'" />';

				}
				else{
					$pst->post_image = AJAXY_SF_NO_IMAGE;
					$pst->post_image_html = '';
				}
			}
			if($post_type == "wpsc-product")
			{
				if(function_exists('wpsc_calculate_price'))
				{
					global $post;
					$post = $pst;
					$pst->wpsc_price = wpsc_the_product_price();
					$pst->wpsc_shipping = strip_tags(wpsc_product_postage_and_packaging());
					$pst->wpsc_image = wpsc_the_product_image($size['height'], $size['width']);
				}
			}
			$pst->post_title = get_the_title($pst->ID);
			$pst->post_author = get_the_author_meta('user_nicename', $pst->post_author);
			$pst->post_link = $post_link;
			$pst->post_content = $this->get_text_words(get_the_content($pst->ID) ,(int)$this->get_excerpt_count());
			$pst->post_date_formatted = date($date_format,  strtotime( $pst->post_date) );
			foreach($unset_array as $uarr)
			{
				unset($pst->{$uarr});
			}
			$pst = apply_filters('sf_post', $pst);
			return $pst;
		}
		return false;
	}
	function get_text_words($text, $count)
	{
		$tr = explode(' ', strip_tags($text));
		$s = "";
		for($i = 0; $i < $count && $i < sizeof($tr); $i++)
		{
			$s[] = $tr[$i];
		}
		return implode(' ', $s);
	}
	function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
	}
	function head()
	{
		//wp_register_script('jquery');
		
		$themes = $this->get_installed_themes(AJAXY_THEMES_DIR, 'themes');
		$style = AJAXY_SF_PLUGIN_URL."themes/default/style.css";
		$style_common = AJAXY_SF_PLUGIN_URL."themes/common.css";
		$theme = $this->get_style_setting('theme');
		$css = $this->get_style_setting('css');
		if(isset($themes[$theme])){
			$style = $themes[$theme]['stylesheet_url'];
		}
		?><!-- AJAXY SEARCH V <?php echo AJAXY_SF_VERSION; ?>-->
		<link rel="stylesheet" type="text/css" href="<?php echo $style_common; ?>" />
		<link rel="stylesheet" type="text/css" href="<?php echo $style; ?>" />
		<?php if(trim($css) != ''): ?>
		<style type="text/css"><?php echo $css; ?></style>
		<?php
		endif;
		
		$label = $this->get_style_setting('search_label', _('Search'));
		
		$x = AJAXY_SF_PLUGIN_URL."js/sf.js";
		$script = '
		<script type="text/javascript">
			/* <![CDATA[ */
				var sf_position = '.$this->get_style_setting('results_position', 0).';
				var sf_templates = '.json_encode($this->get_templates('more')).';
				var sf_input = "'.(trim($this->get_style_setting('input_id', '.sf_input')) == "" ? '.sf_input' : $this->get_style_setting('input_id', '.sf_input')).'";
				jQuery(document).ready(function(){
					jQuery(sf_input).ajaxyLiveSearch({expand: '.$this->get_style_setting('expand', 'false').', searchUrl: "'.str_replace('"', '\"', $this->get_style_setting('search_url',  home_url().'/?s=%s')).'", text: "'.$label.'", delay:'.$this->get_style_setting('delay', 500).', iwidth:'.$this->get_style_setting('width', 180).', width:'.$this->get_style_setting('results_width', 315).', ajaxUrl:"'.$this->get_ajax_url().'"});
				});
			/* ]]> */
		</script>';
		echo $script.'<script src="'.$x.'" type="text/javascript"></script>
		<!-- END -->';
	}
	function get_ajax_url(){
		if(defined('ICL_LANGUAGE_CODE')){
			return admin_url('admin-ajax.php').'?lang='.ICL_LANGUAGE_CODE;
		}
		if(function_exists('qtrans_getLanguage')){

			return admin_url('admin-ajax.php').'?lang='.qtrans_getLanguage();
		}
		return admin_url('admin-ajax.php');
	}
	function footer()
	{
		echo $script;
	}
	function get_search_results()
	{
		$results = array();
		if(!empty($_POST['sf_value']))
		{
			$show_post = $this->show_posts();
			foreach($show_post as $pst_type => $title)
			{
				if($pst_type == 'post_category') {
					$cats = $this->category($_POST['sf_value'], $pst_type);
					foreach($cats as $key => $val) {
						$results[$pst_type]['all'] = $val['posts'];
						$results[$pst_type]['template'] = $this->get_templates($pst_type);
						$results[$pst_type]['title'] = $val['name'];
						$results[$pst_type]['class_name'] = 'sf_item';
					}
				}
				else{
					$results[$pst_type]['all'] = ($pst_type == 'category' || $pst_type == 'post_category' ? $this->category($_POST['sf_value'], $pst_type) : $this->posts($_POST['sf_value'], $pst_type));
					$results[$pst_type]['template'] = $this->get_templates($pst_type);
					$results[$pst_type]['title'] = $title;
					$results[$pst_type]['class_name'] = ($pst_type == 'category' ? 'sf_category' : 'sf_item');
				}
			}
			$results['order'] = $this->show();
			echo json_encode($results);
		}
		exit;
	}
	function install_theme_zip($file_to_open, $target) { 
		$error = "There was a problem extracting the theme files. Please check if you have enough permissions or else contact theme administrator at ajaxy.org!";
		global $wp_filesystem;
		if(class_exists('ZipArchive'))
		{
			$zip = new ZipArchive();  
			$x = $zip->open($file_to_open);  
			if($x === true) 
			{  
				$zip->extractTo($target);  
				$zip->close();                
				unlink($file_to_open);  
			} else {  
				die($error);  
			}
		}
		else
		{
			WP_Filesystem();
			$my_dirs = ''; 
			$m = _unzip_file_pclzip($file_to_open, $target, $my_dirs);
			if(is_wp_error($m)){
				die($error); 
			}
		}
	} 
	function get_installed_themes($themeDir, $themeFolder){
		$dirs = array();
		if ($handle = opendir($themeDir)) {
		  while (($file = readdir($handle)) !== false) {
			if('dir' == filetype($themeDir.$file) ){
				if(trim($file) != '.' && trim($file) != '..'){ 
					$dirs[] = $file;
				}
			}
		  }
		  closedir($handle);
		}
		$themes = array();
		if(sizeof($dirs) > 0){
			foreach($dirs as $dir){
				if(file_exists($themeDir.$dir.'/style.css')){
					$themes[$dir] = array(
								'title' => $dir,
								'stylesheet_dir' => $themeDir.$dir.'/style.css', 
								'stylesheet_url' => plugins_url( $themeFolder.'/'.$dir.'/style.css', __FILE__),
								'dir' => $themeDir.$dir,
								'url' => plugins_url( $themeFolder.'/'.$dir , __FILE__ )
								);
				}
			}
		}
		return $themes;
	}
	function save_zip($url, $path){
		$url_array = explode('/', $url);
		$path = $path.'/'.$url_array[(sizeof($url_array) - 1)];
	 
		$fp = fopen($path, 'w');
	 
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
	 
		$data = curl_exec($ch);
	 
		curl_close($ch);
		fclose($fp);
		if(file_exists($path)){
			return $path;
		}
		return false;
	}
	function admin_notice()
	{
		if(locate_template('searchform.php') != '' && false)
		{
			global $current_screen;
			if ( $current_screen->parent_base == 'options-general' )
				  echo '<div style="padding: 5px 10px; background: #FFB; border: 1px solid yellow; margin: 10px 0;"><p><b>Warning</b> - the file <b>searchform.php</b> should be renamed or removed for this plugin to work (your theme uses its own search form), rename that file for this plugin to work<br/>To disable the theme search form, go to <b>/wp-content/themes/YOUR_THEME_NAME/</b> using your ftp client and rename <b>searchform.php</b> to searchforma.php, this will keep the file but remove its reference (in case you want to restore it back).<br/>In case you don\'t know how to, please email me to <a href="mailto:icu090@gmail.com">icu090@gmail.com</a> and i will do it for you.</p></div>';
		}
	}
	function form($form = '')
	{
		$label = $this->get_style_setting('search_label', 'Search');
		$expand = $this->get_style_setting('expand', false);
		$width = $this->get_style_setting('width', 180);
		if($expand){
			$width = $expand;
		}
		$border = $this->get_style_setting('border-width', '1') . "px " . $this->get_style_setting('border-type', 'solid') . " #" .$this->get_style_setting('border-color', 'dddddd');
		$form = '<!-- Ajaxy Search Form v'.AJAXY_SF_VERSION.' --><div class="sf_container">
		<form role="search" method="get" class="searchform" action="' . home_url( '/' ) . '" >
		<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
		<div class="sf_search" style="border:'.$border.'"><span class="sf_block">
		<input style="width:'.($width).'px;" class="sf_input" autocomplete="off" type="text" value="' . (get_search_query() == '' ? $label : get_search_query()). '" name="s"/>
		<button class="sf_button searchsubmit" type="submit"><span class="sf_hidden">'. esc_attr__('Search') .'</span></button></span></div></div></form></div>';
		if($this->get_style_setting('credits', 1 ) == 1) {
			$form = $form.'<a style="display:none" href="http://www.ajaxy.org">Powered by Ajaxy</a>';
		}
		return $form;
	}
}
add_filter('sf_category_query', 'sf_category_query', 4, 10);
function sf_category_query($query, $search, $excludes, $limit){
	global $wpdb;
	$wpml_lang_code = (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE: false);
	if(	$wpml_lang_code ) {
		if(sizeof($excludes) > 0){
			$excludes = " AND $wpdb->terms.term_id NOT IN (".implode(",", $excludes).")";
		}
		else{
			$excludes = "";
		}
		$query = "select * from (select distinct($wpdb->terms.name), $wpdb->terms.term_id,  $wpdb->term_taxonomy.taxonomy,  $wpdb->term_taxonomy.term_taxonomy_id from $wpdb->terms, $wpdb->term_taxonomy where name like '%%%s%%' and $wpdb->term_taxonomy.taxonomy<>'link_category' and $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id $excludes limit 0, ".$limit.") as c, ".$wpdb->prefix."icl_translations as i where c.term_taxonomy_id = i.element_id and i.language_code = %s and SUBSTR(i.element_type, 1, 4)='tax_' group by c.term_id";
		return $wpdb->prepare($query,  $search, $wpml_lang_code);
	}
	return $query;
}
add_filter('sf_posts_query', 'sf_posts_query', 5, 10);
function sf_posts_query($query, $search, $post_type, $excludes, $search_content, $order_results, $limit){
	global $wpdb;
	$wpml_lang_code = (defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE: false);
	if(	$wpml_lang_code ) {
		if(sizeof($excludes) > 0){
			$excludes = " AND $wpdb->terms.term_id NOT IN (".implode(",", $excludes).")";
		}
		else{
			$excludes = "";
		}
		$order_results = (!empty($order_results) ? " order by ".$order_results : "");
		$query = $wpdb->prepare("select * from (select $wpdb->posts.ID from $wpdb->posts where (post_title like '%%%s%%' ".($search_content == true ? "or post_content like '%%%s%%')":")")." and post_status='publish' and post_type='".$post_type."' $excludes $order_results limit 0,".$limit.") as p, ".$wpdb->prefix."icl_translations as i where p.ID = i.element_id and i.language_code = %s group by p.ID",  ($search_content == true ? array($search, $search):$search), $wpml_lang_code);
		return $query;
	}
	return $query;
}
add_filter('sf_post', 'sf_post', 1);
function sf_post($pst){
	return $pst;
}
function ajaxy_search_form($form = '')
{
	global $AjaxyLiveSearch;
	echo $AjaxyLiveSearch->form($form);
}
global $AjaxyLiveSearch;
$AjaxyLiveSearch = new AjaxyLiveSearch();


?>