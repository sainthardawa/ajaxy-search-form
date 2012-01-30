<?php 

$post_types = sf_get_post_types();

$allowed_tags = array('ID', 'post_title', 'post_author', 'post_date', 'post_date_formatted', 'post_content', 'post_excerpt', 'post_image', 'post_link');
?>
<style type="text/css">
.sf_admin_block
{
padding: 10px;
background: #F1F1F1;
border: 1px solid #DDD;
font-size: 12px;
margin: 10px 10px 20px 0;
}
</style>
<h2>Support us</h2>
<p>
	please donate some dollars for this project development and themes to be created, we are trying to make this project better, if you think it is worth it then u should support it ...<br/>
	contact me at <a href="mailto:icu090@gmail.com">icu090@gmail.com</a> for support and development, please include your paypal id or donation id in your message.
	</p>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="THNE9CQKJDETS">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>
<hr/>
<form action="" method="POST">
<?php
foreach( $post_types as $post_type )
{
	if(!empty($_POST['sf_'.$post_type->name]))
	{
		sf_set_templates($post_type->name, $_POST['sf_'.$post_type->name]);
	}
	if(!empty($_POST['sf_title_'.$post_type->name]))
	{
		$values = array(
			'title' => $_POST['sf_title_'.$post_type->name], 
			'show' => $_POST['sf_show_'.$post_type->name],
			'search_content' => $_POST['sf_search_content_'.$post_type->name],
			'limit' => $_POST['sf_limit_'.$post_type->name],
			'order' => $_POST['sf_order_'.$post_type->name]
			);
		sf_set_setting($post_type->name, $values);
	}
	$setting = (array)sf_get_setting($post_type->name);
	?>
	<div>
		<span><b><?php echo $post_type->labels->name; ?></b></span>
		<select name="sf_show_<?php echo $post_type->name; ?>">
			<option value="1"<?php echo ($setting['show'] == 1 ? ' selected="selected"':''); ?>>Show on search</option>
			<option value="0"<?php echo ($setting['show'] == 0 ? ' selected="selected"':''); ?>>hide on search</option>
		</select>
	</div>
	<div class="sf_admin_block">
	<div><span>Search Title: </span>
	<input type="text" value="<?php echo $setting['title']; ?>" name="sf_title_<?php echo $post_type->name; ?>"/>
	
	<select name="sf_search_content_<?php echo $post_type->name; ?>">
		<option value="0"<?php echo ($setting['search_content'] == 0 ? ' selected="selected"':''); ?>>Search only title</option>
		<option value="1"<?php echo ($setting['search_content'] == 1 ? ' selected="selected"':''); ?>>Search title and content (Slow)</option>
	</select>
	<span>Limit search results to </span><input type="text" value="<?php echo $setting['limit'] ; ?>" name="sf_limit_<?php echo $post_type->name; ?>"/>
	<span>Sort order:</span>
	<select name="sf_order_<?php echo $post_type->name; ?>">
	<?php for($i = 0; $i < sizeof($post_types) + 1; $i ++) {?>
		<?php $selected = ""; 
		if($i == $setting['order']) { $selected = ' selected=""';} ?>
		<option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?></option>
	<?php } ?>
	</select>
	</div>
	<textarea name="sf_<?php echo $post_type->name; ?>" style="height:170px; width:99%"><?php echo sf_get_templates($post_type->name); ?></textarea>
	<p style="font-size:11px">you can add the following tag to the template (Each will be replaced with its value respectively):<br/><br/>
	<?php
	if($post_type->name != 'wpsc-product')
	{
		?>
		<b>{<?php echo implode("}, {", $allowed_tags);?>}</b>
		<?php
	}
	else
	{
		?>
		<b>{<?php echo implode("}</b>, <b>{", $allowed_tags);?>}</b>, <b>{wpsc_price}</b>, <b>{wpsc_shipping}</b>, <b>{wpsc_image}</b>
		<?php
	}
	?>
	</p>
	</div>
	<hr/>
	<?php
}

if(!empty($_POST['sf_category']))
{
	sf_set_templates('category', $_POST['sf_category']);
}
if(!empty($_POST['sf_title_category']))
{
	$values = array(
			'title' => $_POST['sf_title_category'], 
			'show' => $_POST['sf_show_category'],
			'search_content' => false,
			'limit' => $_POST['sf_limit_category'],
			'order' => $_POST['sf_order_category']
			);
	sf_set_setting('category', $values);
}
$setting = sf_get_setting('category');
$setting = (array)$setting;
?>
<div><span><b>Categories</b></span>
<select name="sf_show_category">
	<option value="1"<?php echo ($setting['show'] == 1 ? ' selected="selected"':''); ?>>Show on search</option>
	<option value="0"<?php echo ($setting['show'] == 0 ? ' selected="selected"':''); ?>>hide on search</option>
</select>
</div>
<div class="sf_admin_block">
<div><span>Search Title: </span>
<input type="text" value="<?php echo $setting['title']; ?>" name="sf_title_category"/>
<span>Limit search results to </span><input type="text" value="<?php echo $setting['limit']; ?>" name="sf_limit_category"/>
<span>Sort order:</span>
<select name="sf_order_category">
<?php for($i = 0; $i < sizeof($post_types) + 1; $i ++) {?>
	<?php $selected = ""; 
	if($i == $setting['order']) { $selected = ' selected=""';} ?>
	<option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?></option>
<?php } ?>
</select>
</div>
<textarea name="sf_category" style="height:170px; width:99%"><?php echo sf_get_templates('category'); ?></textarea>
</div>
<div class="sf_style_setting">
<div><b>Customize Search box:</b><br/><br/>
<div>
<?php if(!empty($_POST['sf_style_width'])): sf_set_style_setting('width', $_POST['sf_style_width']); endif; ?></div>
<div>Width:<input type="text" value="<?php echo  sf_get_style_setting('width', 180); ?>" name="sf_style_width"/></div>
<?php if(!empty($_POST['sf_style_b_width'])): sf_set_style_setting('border-width', $_POST['sf_style_b_width']); endif; ?>
<div>border width:<input type="text" value="<?php echo  sf_get_style_setting('border-width' , 1); ?>" name="sf_style_b_width"/></div>
<?php if(!empty($_POST['sf_style_b_type'])): sf_set_style_setting('border-type', $_POST['sf_style_b_type']); endif; ?>
<div>border type:<select name="sf_style_b_type">
	<option value="solid" <?php echo (sf_get_style_setting('border-type',  'solid') == 'solid' ? 'selected="selected"' : ""); ?>>solid</option>
	<option value="dotted" <?php echo (sf_get_style_setting('border-type') == 'dotted' ? 'selected="selected"' : ""); ?>>dotted</option>
	<option value="dashed" <?php echo (sf_get_style_setting('border-type') == 'dashed' ? 'selected="selected"' : ""); ?>>dashed</option>
</select></div>
<?php if(!empty($_POST['sf_style_b_color'])): sf_set_style_setting('border-color', $_POST['sf_style_b_color']); endif; ?>
<div>border color:<input type="text" value="<?php echo  sf_get_style_setting('border-color','eee'); ?>" name="sf_style_b_color"/></div>

</div>
<br/>
<input class="button-primary" type="submit" value="Save Changes" />
</form>
