<?php
/**
 * Advanced form for inclusion in the administration panels.
 *
 * @package WordPress
 * @subpackage Administration
 */
 
$type = isset($_GET['type']) ? $_GET['type'] : exit();

$post_type = false;
if($type == 'category'){
	$post_type = (object)array('name' => 'category', 'labels'=> (object)array('name' => 'categories'));
}
else{
	$post_type = get_post_type_object($type);
}
global $AjaxyLiveSearch;
$message = false;
if(!empty($post_type)){
	if(!empty($_POST['sf_post'])){
		if(wp_verify_nonce($_REQUEST['_wpnonce'], 'sf_edit')){
			if(!empty($_POST['sf_'.$post_type->name])){
				$AjaxyLiveSearch->set_templates($post_type->name, $_POST['sf_'.$post_type->name]);
			}
			if(!empty($_POST['sf_title_'.$post_type->name])){
				$values = array(
					'title' => $_POST['sf_title_'.$post_type->name], 
					'show' => $_POST['sf_show_'.$post_type->name],
					'search_content' => $_POST['sf_search_content_'.$post_type->name],
					'limit' => $_POST['sf_limit_'.$post_type->name],
					'order' => $_POST['sf_order_'.$post_type->name]
					);
				$AjaxyLiveSearch->set_setting($post_type->name, $values);
			}
			$message = "Settings saved";
		}
		else{
			$message = "Settings have been already saved";
		}
	}

	
	$setting = (array)$AjaxyLiveSearch->get_setting($type);

	$fields = $AjaxyLiveSearch->get_post_types();
	$fields[] = (object)array('name' => 'category', 'labels'=> (object)array('name' => 'categories'));

	$allowed_tags = array('id', 'post_title', 'post_author', 'post_date', 'post_date_formatted', 'post_content', 'post_excerpt', 'post_image', 'post_image_html', 'post_link');


		
	$title  = 'Edit '.$post_type->labels->name.' template & settings';
	$notice = '';


			
	?>

	<div class="wrap">
	<?php screen_icon('post'); ?>
	<h2><?php echo esc_html( $title ); ?></h2>
	<?php if ( $notice ) : ?>
	<div id="notice" class="error"><p><?php echo $notice ?></p></div>
	<?php endif; ?>
	<?php if ( $message ) : ?>
	<div id="message" class="updated"><p><?php echo $message; ?></p></div>
	<?php endif; ?>
	<form name="post" action="" method="post" id="post">
	<?php wp_nonce_field('sf_edit'); ?>
	<input type="hidden" name="sf_post" value="<?php echo $post_type->name; ?>"/>
	<div id="poststuff" class="metabox-holder has-right-sidebar">
	<div id="side-info-column" class="inner-sidebar">
		<div id="side-sortables" class="meta-box-sortables ui-sortable">
			<div id="submitdiv" class="postbox ">
				<div class="handlediv" title="Click to toggle"><br></div>
				<h3 class="hndle"><span>Save Settings</span></h3>
				<div class="inside">
					<div class="submitbox" id="submitpost">
						<div id="minor-publishing">
							<div id="misc-publishing-actions">
								<div class="misc-pub-section"><label>Status:</label>
									<select name="sf_show_<?php echo $post_type->name; ?>">
										<option value="1"<?php echo ($setting['show'] == 1 ? ' selected="selected"':''); ?>>Show on search</option>
										<option value="0"<?php echo ($setting['show'] == 0 ? ' selected="selected"':''); ?>>hide on search</option>
									</select>
								</div>
								<div class="misc-pub-section"><label>Search mode:</label>
									<select name="sf_search_content_<?php echo $post_type->name; ?>">
										<option value="0"<?php echo ($setting['search_content'] == 0 ? ' selected="selected"':''); ?>>Only title</option>
										<option value="1"<?php echo ($setting['search_content'] == 1 ? ' selected="selected"':''); ?>>Title and content (Slow)</option>
									</select>
								</div>
								<div class="misc-pub-section " id="visibility">Order: 
								<input type="text" style="width:50px" value="<?php echo $setting['order'] ; ?>" name="sf_order_<?php echo $post_type->name; ?>"/>
								</div>
								<div class="misc-pub-section " id="limit_results"><span>Limit results to:</span>
									<input type="text" style="width:50px" value="<?php echo $setting['limit'] ; ?>" name="sf_limit_<?php echo $post_type->name; ?>"/>
								</select>
								</div>
							</div>
							<div class="clear"></div>
						</div>
						<div id="major-publishing-actions">
							<div id="publishing-action">
								<input type="submit" name="save" id="save" class="button-primary" value="save" tabindex="5" accesskey="p">
							</div>
							<div class="clear"></div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<div id="post-body">
		<div id="post-body-content">
			<div id="titlediv">
				<div id="titlewrap">
					<label class="hide-if-no-js" style="visibility:hidden" id="title-prompt-text" for="title"><?php echo __( 'Enter title here' ); ?></label>
					<input type="text" name="sf_title_<?php echo $post_type->name; ?>" size="30" tabindex="1" value="<?php echo $setting['title']; ?>" id="title" autocomplete="off" />
				</div>
				<div class="inside">
				</div>
			</div>
			<div id="postdivrich" class="postarea">
				<h2>Template</h2>
				<p>Changes are live, use the tags below to customize the data replaced by each template</p>
				<textarea rows="20" cols="40" name="sf_<?php echo $post_type->name; ?>" id="content" style="width:100%"><?php echo $AjaxyLiveSearch->get_templates($post_type->name); ?></textarea>
				<table id="post-status-info" cellspacing="0"><tbody><tr>
					<td><b>Tags: </b>
					<?php
					if($post_type->name != 'wpsc-product')
					{
						?>
						{<?php echo implode("}, {", $allowed_tags);?>}
						<?php
					}
					else
					{
						?>
						{<?php echo implode("}, {", $allowed_tags);?>}, {wpsc_price}, {wpsc_shipping}, {wpsc_image}
						<?php
					}
					?>
					
					</td>
				</tr></tbody>
				</table>
			</div>
		</div>
	</div>
	<br class="clear" />
	</div>
	</form>
	</div>
	<script type="text/javascript">
	try{document.post.title.focus();}catch(e){}
	</script>
<?php }
else {
?>
<h3>Oops it looks like this page is no longer available or have been deleted :(</h3>
<?php
}
?>
