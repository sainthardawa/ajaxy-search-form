jQuery(document).click(function(){ jQuery("#sf_sb").hide(); });
jQuery(document).ready(function(){
	jQuery(".sf_input").focus(function (){
		if(jQuery(this).val() == sf_defaultText){
			jQuery(this).val('');
			jQuery(this).attr('class', jQuery(this).attr('class') + ' sf_focused');
			if(sf_expand){
				jQuery("#" + jQuery(this).attr('container') + " .sf_search").animate({width:sf_width});
			}
		}
	});
	jQuery(".sf_input").blur(function () {
		if(jQuery(this).val() == ''){
			jQuery(this).val(sf_defaultText);
			jQuery(this).attr('class', jQuery(this).attr('class').replace(/ sf_focused/g, ''));
			if(sf_expand){
				jQuery("#" + jQuery(this).attr('container') + " .sf_search").animate({width:sf_expand});
			}
		}
	});
});
function sf_get_results(id)
{
	jQuery("#" + id + " .sf_input").attr('autocomplete', 'off');
	if(jQuery('#sf_sb').length == 0){
		jQuery('body').append('<div id="sf_sb" class="sf_sb" style="position:absolute;display:none;width:'+ sf_swidth + 'px;z-index:9999">'+
								'<div class="sf_sb_cont">' +
									'<div class="sf_sb_top"></div>' +
									'<div id="sf_results" style="width:100%">' +
										'<div id="sf_val" ></div>' +
										'<div id="sf_more"></div>' +
									'</div>' +
									'<div class="sf_sb_bottom"></div>' +
								'</div>' +
							'</div>');
	}
	if(jQuery("#" + id + " .sf_input").val() != "")
	{
		var loading  = 	"<li class=\"sf_lnk sf_more sf_selected\">"+
			"<a id=\"sf_loading\" href=\"/?s=" + escape(jQuery("#" + id + " .sf_input").val()) + "\">"+
			"</a>"+
		"</li>";
		jQuery("#sf_val").html("<ul>"+loading+"</ul>");
		var pos = false;
		if(jQuery("#sf_search").length > 0){
			pos = jQuery("#sf_search").offset();
			jQuery("#sf_sb").css({top:pos.top + jQuery("#sf_search").innerHeight(), left:pos.left});
			jQuery("#sf_sb").show();
		}
		else if(jQuery("#" + id + " .sf_input").length > 0){
			pos = jQuery("#" + id + " .sf_input").offset();
			jQuery("#sf_sb").css({top:pos.top + jQuery("#" + id + " .sf_input").innerHeight(), left:pos.left});
			jQuery("#sf_sb").show();
		}
		var data = { action: "ajaxy_sf", sf_value: jQuery("#" + id + " .sf_input").val()};
		jQuery.post(sf_ajaxurl, data, function(resp) { 
			var results = eval("("+ resp + ")");
			var m = "";
			var s = 0;
			if(typeof(results.order) != "undefined")
			{
				for(var mindex in results.order)
				{
					var c = sf_load(results[mindex]);
					m += c[0];
					s += c[1];
				}
			}
			
			
			var sf_selected = "";
			if(s == 0)
			{
				sf_selected = " sf_selected";
			}
			m += "<li class=\"sf_lnk sf_more" + sf_selected + "\">" + sf_templates + "</li>";
			m = m.replace(/{search_value_escaped}/g, jQuery("#" + id + " .sf_input").val());
			m = m.replace(/{search_value}/g, jQuery("#" + id + " .sf_input").val());
			m = m.replace(/{total}/g, s);
			if(s > 0)
			{
				jQuery("#sf_val").html("<ul>"+m+"</ul>");
			}
			else
			{
				jQuery("#sf_val").html("<ul>"+m+"</ul>");
			}
			sf_load_events();
			jQuery("#sf_sb").show();
		 });

	 }
	 else
	 {
		jQuery("#sf_sb").hide();
	 }
}
function sf_load_events()
{
	jQuery("#sf_val li.sf_lnk").mouseover(function(){
		jQuery(".sf_lnk").each(function() { jQuery(this).attr("class",jQuery(this).attr("class").replace(" sf_selected" , "")); });
		jQuery(this).attr("class", jQuery(this).attr("class") + " sf_selected");
	});
	
}
function sf_replace_results(results, template)
{
	for(var s in results)
	{
		template = template.replace(new RegExp("{"+s+"}", "g"), results[s]);
	}
	return template;
}

function sf_load(results)
{
	var m = "";
	var s = 0;
	if(typeof(results) != "undefined")
	{
		if(results.all.length > 0)
		{
			m += "<li class=\"sf_header\">" + results.title + "</li>";
			for(var i in results.all)
			{
				s ++;
				m += "<li class=\"sf_lnk "+results.class_name +"\">"+ sf_replace_results(results.all[i], results.template) + "</li>";
			}
		}
	}
	return new Array(m, s);
}

jQuery(window).keydown(function(event){
	if(jQuery("#sf_sb").css("display") != "none" && jQuery("#sf_sb").css("display") != "undefined" && jQuery("#sf_sb").length > 0)
	{
		if(event.keyCode == "38" || event.keyCode == "40")
		{
			if(jQuery.browser.webkit)
			{
				jQuery("#sf_sb").focus();
			}
			var s_item = null;
			var after_s_item = null;
			var s_sel = false;
			var all_items = jQuery("#sf_val li.sf_lnk");
			var s_found = false;
			event.stopPropagation();
			event.preventDefault();
			for(var i = 0; i < all_items.length; i++)
			{
				if(jQuery(all_items[i]).attr("class").indexOf("sf_selected") >= 0 && s_found == false)
				{
					s_sel = true;
					if(i < all_items.length - 1 && event.keyCode == "40")
					{
						jQuery(all_items[i]).attr("class",jQuery(all_items[i]).attr("class").replace(" sf_selected", ""));
						jQuery(all_items[i+1]).attr("class", jQuery(all_items[i+1]).attr("class")+ " sf_selected");
						i = i+1;
						s_found = true;
					}
					else if(i > 0 && event.keyCode == "38")
					{
						jQuery(all_items[i]).attr("class",jQuery(all_items[i]).attr("class").replace(" sf_selected", ""));
						jQuery(all_items[i-1]).attr("class", jQuery(all_items[i-1]).attr("class")+ " sf_selected");
						i = i+1;
						s_found = true;
					}
				}
				else
				{
					jQuery(all_items[i]).attr("class",jQuery(all_items[i]).attr("class").replace(" sf_selected", ""));
				}
			}
			if(s_sel == false)
			{
				if(all_items.length > 0)
				{
					jQuery(all_items[0]).attr("class", jQuery(all_items[0]).attr("class")+ " sf_selected");
				}
			}
			//jQuery(window).unbind("keypress");

		}
		else if(event.keyCode == 27)
		{
			jQuery("#sf_sb").hide();
		}
		else if(event.keyCode == 13)
		{
			var b = jQuery("#sf_val li.sf_selected a");
			if(typeof(b) != 'undefined' && b != '')
			{
				window.location.href = jQuery("#sf_val li.sf_selected a").attr("href");
				return false;
			}
			else
			{
				window.location.href = "/?s=".jQuery('#s').val();
				return false;
			}
		}
	}
});
