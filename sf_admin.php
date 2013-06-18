<?php 

global $AjaxyLiveSearch;

if(isset($_POST['sf_submit']) && wp_verify_nonce($_POST['_wpnonce'])){
	$styles = $_POST['sf']['style'];
	$templates = $_POST['sf']['template'];
	$AjaxyLiveSearch->set_style_setting('search_label'	, $styles['label']); 
	$AjaxyLiveSearch->set_style_setting('input_id'	, $styles['input_id']); 
	$AjaxyLiveSearch->set_style_setting('width'			, (int)$styles['width']);
	if(isset($styles['allow_expand'])){
		$AjaxyLiveSearch->set_style_setting('expand'		, (int)$styles['expand']);
	}
	else{
		$AjaxyLiveSearch->set_style_setting('expand'		, 0);
	}
	if(isset($styles['credits'])){
		$AjaxyLiveSearch->set_style_setting('credits'		, 1);
	}
	else{
		$AjaxyLiveSearch->set_style_setting('credits'		, 0);
	}
	if(isset($styles['aspect_ratio'])){
		$AjaxyLiveSearch->set_style_setting('aspect_ratio'		, 1);
	}
	else{
		$AjaxyLiveSearch->set_style_setting('aspect_ratio'		, 0);
	}
	$AjaxyLiveSearch->set_style_setting('delay'			, (int)$styles['delay']);
	$AjaxyLiveSearch->set_style_setting('border-width' 	, (int)$styles['b_width']);
	$AjaxyLiveSearch->set_style_setting('border-type'	, $styles['b_type']);
	$AjaxyLiveSearch->set_style_setting('search_url'	, $styles['url']);
	$AjaxyLiveSearch->set_style_setting('border-color'	, $styles['b_color']);
	$AjaxyLiveSearch->set_style_setting('results_width'	, (int)$styles['results_width']); 
	$AjaxyLiveSearch->set_style_setting('excerpt' 		, $styles['excerpt']);
	$AjaxyLiveSearch->set_style_setting('css'			, $styles['css']);
	//$AjaxyLiveSearch->set_style_setting('results_position'	, $styles['results_position']);
	$AjaxyLiveSearch->set_style_setting('thumb_width'	, $styles['thumb_width']);
	$AjaxyLiveSearch->set_style_setting('thumb_height'	, $styles['thumb_height']);
	$AjaxyLiveSearch->set_templates('more'	, $templates['more_results']);
	$message = "Settings saved";
}
?>
<?php if ( $message ) : ?>
<div id="message" class="updated"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<div class="wrap">
	<h3>Search Form box</h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label>Search label</label></th>
				<td>
					<input type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('search_label',  _('Search')); ?>" name="sf[style][label]" class="regular-text">
					<span class="description">This label appears inside the search form and will be hidden when the user clicks inside.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Search Input ID or class name</label></th>
				<td>
					<input type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('input_id',  ""); ?>" name="sf[style][input_id]" class="regular-text">
					<span class="description">keep this blank to use ajaxy search form, or else put the id of the search or the class name in the form (#ID for id (# before the id) or else (.className) ( "." before the className).</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Width</label></th>
				<td>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('width', 180); ?>" name="sf[style][width]" class="regular-text">
					<span class="description">The width of the search form (width is per pixel) - the value should be integer.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Allow expand</label></th>
				<td>
					<input type="checkbox" name="sf[style][allow_expand]" <?php echo  $AjaxyLiveSearch->get_style_setting('expand', 0 ) > 0 ? 'checked="checked"' : ''; ?>/>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('expand', false); ?>" name="sf[style][expand]" class="regular-text">
					<span class="description">The reduced width of the search form (this will allow the form to expand its width when it gains focus).</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Delay time</label></th>
				<td>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('delay', 500); ?>" name="sf[style][delay]" class="regular-text">
					<span class="description">The delay time before showing the results (this will allow the user to input more text before searching) -  <b>(in millisecond, i.e 5000 = 5sec)</b>.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Border width</label></th>
				<td>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('border-width' , 1); ?>" name="sf[style][b_width]" class="regular-text">
					<span class="description">The width of the search form border.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Border type</label></th>
				<td>
					<select name="sf[style][b_type]">
						<option value="solid" <?php echo ($AjaxyLiveSearch->get_style_setting('border-type',  'solid') == 'solid' ? 'selected="selected"' : ""); ?>>solid</option>
						<option value="dotted" <?php echo ($AjaxyLiveSearch->get_style_setting('border-type') == 'dotted' ? 'selected="selected"' : ""); ?>>dotted</option>
						<option value="dashed" <?php echo ($AjaxyLiveSearch->get_style_setting('border-type') == 'dashed' ? 'selected="selected"' : ""); ?>>dashed</option>
					</select>
					<span class="description">The type of the search form border.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Border color</label></th>
				<td>
					<input style="width:52px" type="text" value="<?php echo $AjaxyLiveSearch->get_style_setting('border-color','eee'); ?>" name="sf[style][b_color]" class="regular-text">
					<span class="description">The color of the search form border (color value is hexa-decimal).</span>
				</td>
			</tr>
			
		</tbody>
	</table>
	<hr/>
	<h3>Search Results box</h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label>Width</label></th>
				<td>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('results_width', 315); ?>" name="sf[style][results_width]" class="regular-text">
					<span class="description">The width of the results box (width is per pixel) - the value should be integer.</span>
				</td>
			</tr>
			<?php /*
			<tr valign="top">
				<th scope="row"><label>Position</label></th>
				<td>
					<select name="sf[style][results_position]">
						<option value="0" <?php echo ($AjaxyLiveSearch->get_style_setting('results_position', 0) == 0 ? 'selected="selected"' : ""); ?>>left</option>
						<option value="1" <?php echo ($AjaxyLiveSearch->get_style_setting('results_position', 0) == 1 ? 'selected="selected"' : ""); ?>>right</option>
					</select>
					<span class="description">The position of the results box (it can be displayed starting from the left or from the right)</span>
				</td>
			</tr>
			*/ ?>
			<tr valign="top">
				<th scope="row"><label>Total words</label></th>
				<td>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('excerpt' , 10); ?>" name="sf[style][excerpt]" class="regular-text">
					<span class="description">The post content total number of words to be shown under each result.</span>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label>Thumb size</label></th>
				<td>
					<label>height</label>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('thumb_height' , 50); ?>" name="sf[style][thumb_height]" class="regular-text">
					<label>X width</label>
					<input style="width:40px" type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('thumb_width' , 50); ?>" name="sf[style][thumb_width]" class="regular-text">
					<input type="checkbox" name="sf[style][aspect_ratio]" <?php echo  $AjaxyLiveSearch->get_style_setting('aspect_ratio', 0 ) > 0 ? 'checked="checked"' : ''; ?>/><label>Maintain aspect ratio</label>
					<br class="clear" />
					<span class="description">The thumbnail size used in the post template it will modify {post_image_html} only, Maintaining aspect ratio is relatively slow so be aware, modifing the thumb size will need some css changes.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<hr/>
	<h3>More results box</h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<th scope="row"><label>Search Url</label></th>
				<td>
					<input type="text" value="<?php echo  $AjaxyLiveSearch->get_style_setting('search_url',  home_url().'/?s=%s'); ?>" name="sf[style][url]" class="regular-text">
					<span class="description">This search URL for the "See more results"</span>
				</td>
			</tr>
			<tr valign="top">
				<td colspan="2">
					<textarea style="width:99%; height:150px" name="sf[template][more_results]" class="regular-text"><?php echo $AjaxyLiveSearch->get_templates('more'); ?></textarea>
					<br class="clear"/>
					<span class="description">More results text (allowed parameters ( <b>{search_value}</b> <b>{search_value_escaped}</b> <b>{search_url_escaped}</b>).</span>
				</td>
			</tr>
		</tbody>
	</table>
	<hr/>
	<h3>Custom styles (<a href="http://www.w3schools.com/css/css_syntax.asp" target="_blank" rel="nofollow">CSS</a>)</h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td colspan="2">
					<textarea style="width:99%; height:150px" name="sf[style][css]" class="regular-text"><?php echo $AjaxyLiveSearch->get_style_setting('css', ''); ?></textarea>
					<br class="clear"/>
					<span class="description">Custom styles to be added in the plugin css. add ( .screen-reader-text { display:none; } ) if you want to hide the search form title.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<hr/>
	<h3>Credits</h3>
	<table class="form-table">
		<tbody>
			<tr valign="top">
				<td colspan="2">
					<input type="checkbox" name="sf[style][credits]" <?php echo  $AjaxyLiveSearch->get_style_setting('credits', 1 ) == 1 ? 'checked="checked"' : ''; ?>/>
					<span class="description">Author "Powered by" link and credits.</span>
				</td>
			</tr>
		</tbody>
	</table>
	<hr class="clear"/>
	<input class="button-primary" name="sf_submit" type="submit" value="Save Changes" />
	<br class="clear"/>
	<br class="clear"/>
</div>