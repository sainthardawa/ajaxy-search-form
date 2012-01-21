<?php
/**
 * @package Ajaxy
 */
/*
	Plugin Name: Ajaxy Live Search
	Plugin URI: http://ajaxy.org
	Description: Transfer wordpress form into an advanced ajax search form the same as facebook live search
	Version: 1.0.0
	Author: Ajaxy Team
	Author URI: http://ajaxy.org
	License: GPLv2 or later
*/

define('AJAXY_SF_VERSION', '1.0.0');
define('AJAXY_SF_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define('AJAXY_SF_NO_IMAGE', plugin_dir_url( __FILE__ ) ."images/no-image.gif");

add_action( "admin_menu", "ajaxy_sf_admin");
function ajaxy_sf_admin()
{
	add_submenu_page( 'options-general.php', __('Ajaxy Search Form'), __('Ajaxy Search Form'), 'manage_options', 'ajaxy_sf_admin', 'ajaxy_sf_admin_page'); 
}
function ajaxy_sf_admin_page()
{
	include_once('sf_admin.php');
}
function sf_get_post_types()
{
	$post_types = get_post_types(array('_builtin' => false),'objects');
	$post_types['post'] = get_post_type_object('post');
	$post_types['page'] = get_post_type_object('page');
	unset($post_types['wpsc-product-file']);
	return $post_types;
}
function sf_get_excerpt_count()
{
	return 10;
}
function sf_show_posts()
{
	$post_types = sf_get_post_types();
	$show_posts = array();
	$show_m_posts = array();
	foreach($post_types as $post_type)
	{
		$setting = sf_get_setting($post_type->name);
		if($setting -> show == 1)
		{
		$show_posts[$post_type->name] = $setting->order;
		}
	}
	$scat = (array)sf_get_setting('category');
	$show_posts['category'] = $scat['order'];
	asort($show_posts);
	foreach($show_posts as $key => $value)
	{
		$setting = sf_get_setting($key);
		$show_m_posts[$key] = $setting->title;
	}
	return $show_m_posts;
}
function sf_show()
{
	$m = sf_show_posts();
	return $m;
}
function sf_search_content()
{
	return false;
}
function sf_set_templates($template, $html)
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
function sf_set_setting($name, $value)
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
function sf_get_setting($name)
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
				'search_content' => 0,
				'limit' => 5,
				'order' => 0
				);
		return (object)$values;
	}
}
function sf_set_style_setting($name, $value)
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
function sf_get_style_setting($name, $default = '')
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
function sf_get_templates($template)
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
			$template_post = '<a href="{category_link}" >{name}</a>';
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
			$template_post = '<a href="?s={search_value_escaped}">
								<span class="sf_text">See more results for "{search_value}"</span>
								<span class="sf_small">Displaying top {total} results</span>
							</a>';
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
			$template_post = '<a href="{post_link}" >
									<img src="{post_image}" height="50" width="50" />
									<span class="sf_text">{post_title} </span>
									<span class="sf_small">Posted by {post_author} on {post_date_formatted}</span>
								</a>';
		}
	}
	return $template_post;
}

function ajaxy_search_form($form)
{
	$width = sf_get_style_setting('width', 180);
	$border = sf_get_style_setting('border-width', '1') . "px " . sf_get_style_setting('border-type', 'solid') . " #" . sf_get_style_setting('border-color', 'dddddd');
	$form = '<form role="search" method="get" id="searchform" action="' . home_url( '/' ) . '" >
	<div><label class="screen-reader-text" for="s">' . __('Search for:') . '</label>
	<div class="sf_search" style="width:'.($width).'px; border:'.$border.'">
		<span class="sf_block">
		<input class="sf_input" style="width:'.($width - 35).'px" autocomplete="off" type="text" value="' . get_search_query() . '" name="s" id="s" />
		<button class="sf_button" type="submit" id="searchsubmit"><span class="sf_hidden">'. esc_attr__('Search') .'</span></button>
		</span>
	</div>
	<div class="sf_sb" style="position:relative;margin-top: -2px;"><div id="sf_results" style="display:none;position:absolute; background:#fff;width:'.($width).'px"><div id="sf_val" ></div><div id="sf_more"></div></div></div>
	
	</div>
	</form>
	<a style="display:none" href="http://ajaxy.org">Ajaxy.org</a>
	';
	$script = '<script type="text/javascript">
				/* <![CDATA[ */
				var sf_timeout = null;
				jQuery("#s").keyup(function(event){
					if(event.keyCode != "38" && event.keyCode != "40" && event.keyCode != "13" && event.keyCode != "27" && event.keyCode != "39" && event.keyCode != "37")
					{
						if(sf_timeout != null)
						{
							clearTimeout(sf_timeout);
						}
						sf_timeout = setTimeout("sf_get_results()", 500);
					}
				});
			/* ]]> */
			</script>';
	return $form.$script;
}

function ajaxy_sf_category($name, $limit = 5)
{
	global $wpdb;
	$categories = array();
	$setting = (object)sf_get_setting('category');
	$results = $wpdb->get_results($wpdb->prepare("select $wpdb->terms.term_id from $wpdb->terms, $wpdb->term_taxonomy where name like '%%%s%%' and $wpdb->term_taxonomy.taxonomy='category' and $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id limit 0, ".$setting->limit,  $name));
	if(sizeof($results) > 0 && is_array($results) && !is_wp_error($results))
	{
		$unset_array = array('term_group', 'term_taxonomy_id', 'taxonomy', 'parent', 'count', 'cat_ID', 'cat_name', 'category_parent');
		foreach($results as $result)
		{
			$cat = get_category($result->term_id);
			if($cat != null)
			{
				$category_link = get_category_link($result->term_id);
				$cat->category_link = $category_link;
				foreach($unset_array as $uarr)
				{
					unset($cat->{$uarr});
				}
				$categories[] = $cat; 
			}
		}
	}
	return $categories;
}

function ajaxy_sf_posts($name, $post_type='post')
{
	global $wpdb;
	$posts = array();
	$setting = (object)sf_get_setting($post_type);
	$results = $wpdb->get_results( $wpdb->prepare("select $wpdb->posts.ID from $wpdb->posts where (post_title like '%%%s%%' ".($setting->search_content == 1 ? "or post_content like '%%%s%%')":")")." and post_status='publish' and post_type='".$post_type."' limit 0,".$setting->limit,  ($setting->search_content == true ? array($name, $name):$name)));
	$date_format = get_option( 'date_format' );
	$unset_array = array('post_type', 'post_date_gmt', 'post_status', 'comment_status', 'ping_status', 'post_password', 'post_name', 'post_content_filtered', 'to_ping', 'pinged', 'post_modified', 'post_modified_gmt', 'post_parent', 'guid', 'menu_order', 'post_mime_type', 'comment_count', 'ancestors', 'filter');
	if(sizeof($results) > 0 && is_array($results) && !is_wp_error($results))
	{
		foreach($results as $result)
		{
			$pst = get_post($result->ID);
			if($pst != null)
			{
				$post_link = get_permalink($result->ID);
				$post_thumbnail_id = get_post_thumbnail_id( $pst->ID);
				if( $post_thumbnail_id > 0)
				{
					$thumb = wp_get_attachment_image_src( $post_thumbnail_id, array(50,50) );
					$pst->post_image =  (trim($thumb[0]) == "" ? AJAXY_SF_NO_IMAGE : $thumb[0]);
				}
				else
				{
					$pst->post_image = AJAXY_SF_NO_IMAGE;
				}
				if($post_type == "wpsc-product")
				{
					if(function_exists('wpsc_calculate_price'))
					{
						global $post;
						$post = $pst;
						$pst->wpsc_price = wpsc_the_product_price();
						$pst->wpsc_shipping = strip_tags(wpsc_product_postage_and_packaging());
						$pst->wpsc_image = wpsc_the_product_image(50, 50);
					}
				}
				$pst->post_author = get_the_author_meta('user_nicename', $pst->post_author);
				$pst->post_link = $post_link;
				$pst->post_content = sf_get_text_words($pst->post_content ,sf_get_excerpt_count());
				$pst->post_date_formatted = date($date_format,  strtotime( $pst->post_date) );
				foreach($unset_array as $uarr)
				{
					unset($pst->{$uarr});
				}
				$posts[] = $pst; 
			}
		}
	}
	return $posts;
}

function sf_get_text_words($text, $count)
{
	$tr = explode(' ', strip_tags($text));
	$s = "";
	for($i = 0; $i < $count && $i < sizeof($tr); $i++)
	{
		$s[] = $tr[$i];
	}
	return implode(' ', $s);
}

function sf_head()
{
	$style = AJAXY_SF_PLUGIN_URL."style.css";
	echo '<!-- AJAXY SEARCH SCRIPT -->
	<link rel="stylesheet" type="text/css" href="'.$style.'" />';
	
	$x = AJAXY_SF_PLUGIN_URL."js/sf.js";
	$script = '
	<script type="text/javascript">
		/* <![CDATA[ */
			var sf_templates = '.json_encode(sf_get_templates('more')).';
			var sf_ajaxurl = "'.admin_url('admin-ajax.php').'";
			var sf_loading = "'.AJAXY_SF_PLUGIN_URL.'images/fb_loading.gif";
		/* ]]> */
	</script>';
	echo $script.'<script src="'.$x.'" type="text/javascript"></script>
	<!-- END -->';
}

function get_search_results()
{
	$results = array();
	if(!empty($_POST['sf_value']))
	{
		/*
		if(sf_show_categories())
		{
			$results['category']['all'] = ajaxy_sf_category($_POST['sf_value']);
			$results['category']['template'] = sf_get_templates('category');
			$results['category']['title'] = 'Categories';
			$results['category']['class_name'] = 'sf_category';
		}*/
		$show_post = sf_show_posts();
		foreach($show_post as $pst_type => $title)
		{
			$results[$pst_type]['all'] = ($pst_type == 'category' ? ajaxy_sf_category($_POST['sf_value']) : ajaxy_sf_posts($_POST['sf_value'], $pst_type));
			$results[$pst_type]['template'] = sf_get_templates($pst_type);
			$results[$pst_type]['title'] = $title;
			$results[$pst_type]['class_name'] = ($pst_type == 'category' ? 'sf_category' : 'sf_item');
		}
		$results['order'] = sf_show();
		echo json_encode($results);
	}
	exit;
}

//ACTIONS
add_action('wp_head', 'sf_head');
add_action('wp_ajax_ajaxy_sf', 'get_search_results');
add_action('wp_ajax_nopriv_ajaxy_sf', 'get_search_results');

//FILTERS
add_filter('get_search_form', 'ajaxy_search_form', 1);

?>