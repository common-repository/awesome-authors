jQuery(function(){
	jQuery(".author_list").droppable({
		drop: function(e, ui){
			jQuery(this).append(ui.draggable);
			ui.draggable.css({
				top: "0px",
				left: "0px"
			});
			jQuery(this).css("border-style", "solid");
		},
		over: function(e, ui) {
			jQuery(this).css("border-style", "dashed");
		},
		out: function(e, ui) {
			jQuery(this).css("border-style", "solid");
		}
	});
	jQuery(".author_list li").draggable();
	jQuery("#aa_save").click(function(){
		var aa_gravatar = jQuery("#aa_gravatar_size").val();
		var aa_number = jQuery("#aa_author_display").val();
		var aa_min = jQuery("#aa_minimum_posts").val();
		var aa_sticky_str = "";
		var aa_excluded_str = "";
		jQuery("#sticky_authors li").each(function(i){
			if(i+1 == jQuery("#sticky_authors li").length) {
				aa_sticky_str += jQuery(this).attr("id").split("-")[1];
			} else {
				aa_sticky_str += jQuery(this).attr("id").split("-")[1] + ",";
			}
		});
		jQuery("#excluded_authors li").each(function(i){
			if(i+1 == jQuery("#excluded_authors li").length) {
				aa_excluded_str += jQuery(this).attr("id").split("-")[1];
			} else {
				aa_excluded_str += jQuery(this).attr("id").split("-")[1] + ",";
			}
		});

		var nounce = Math.floor(Math.random()*11);
		var aa_opts = aa_gravatar + ";" + aa_number + ";" + aa_min + ";" + aa_sticky_str + ";" + aa_excluded_str + ";" + nounce;
		jQuery("#aa_save").text("Saving Changes...");
		jQuery.ajax({
			type: 'POST',
			url: ajaxurl,
			data: "action=aa_save_opts&aa_opts=" + aa_opts,
			success: function(data) {
				if(data == "saved") {
					jQuery("#aa_save").text("Changes Saved!");
					setTimeout(function(){
						jQuery("#aa_save").text("Save Changes");
					}, 2000);
				} else {
					alert("There was a problem saving your changes. Try again in a few seconds.");
				}
			},
			error: function(){
				alert("There was a problem saving your changes. Try again in a few seconds.");
			}
		});
		return false;
	});
});
