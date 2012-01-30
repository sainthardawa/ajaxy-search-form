jQuery(document).click(function(){ jQuery("#sf_results").hide(); });
function sf_get_results()
{
	if(jQuery("#s").val() != "")
	{
		var loading  = 	"<li class=\"sf_lnk sf_more sf_selected\">"+
			"<a href=\"?s=" + escape(jQuery("#s").val()) + "\">"+
			"<img src=\"" + sf_loading + "\" style=\"width:16px;height:11px\"/>"+
			"</a>"+
		"</li>";
		jQuery("#sf_val").html("<ul>"+loading+"</ul>");
		jQuery("#sf_results").show();
		var data = { action: "ajaxy_sf", sf_value: jQuery("#s").val()};
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
			m = m.replace(/{search_value_escaped}/g, escape(jQuery("#s").val()));
			m = m.replace(/{search_value}/g, jQuery("#s").val());
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
			jQuery("#sf_results").show();
		 });

	 }
	 else
	 {
		jQuery("#sf_results").hide();
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
	if(jQuery("#sf_results").css("display") != "none")
	{
		if(event.keyCode == "38" || event.keyCode == "40")
		{
			if(jQuery.browser.webkit)
			{
				jQuery("#sf_results").focus();
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
			jQuery("#sf_results").hide();
		}
		else if(event.keyCode == 13)
		{
			window.location.href = jQuery("#sf_val li.sf_selected a").attr("href");
			return false;
		}
	}
});
