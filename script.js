var aa_timer;

function aa_scripts() {
	var $ = jQuery;
	var parent = $("#aa_subdiv").parent();
	var aa_width = parent.width();
	var aa_height = parent.height();
	var arrow_margin = ((aa_height - 15) / 2);
	$("#aa_div").width(aa_width).height(aa_height);
	$("#awesomeAuthors").width(aa_width).height(aa_height);
	var aa_prev_width = $("#aa_prev").width() + 15;
	var aa_next_width = $("#aa_next").width() + 15;
	$("#aa_next, #aa_prev").each(function(){
		$(this).css({
			marginTop: arrow_margin + "px",
			marginBottom: arrow_margin + "px"
		});
	});
	$("#aa_wrap").width(aa_width - (aa_prev_width + aa_next_width)); 
	var num_of_authors = $("#aa_wrap ul").children('li').length;
	$("#aa_wrap ul").width(num_of_authors * 66 + "px");
	var distance = $("#aa_wrap ul").width() - $("#aa_wrap").width();
	
	var space = $("#aa_wrap").width();
	var increments = distance / space;
	var remainder = distance%space;

	var positions = [];
	positions[0] = 0;

	for(i=1;i<=increments;i++) {
		positions[i] = space * -(i);
	}

	if(remainder != 0) {
		var last = positions[positions.length - 1];
		positions[positions.length] = last - remainder;
	}

	var position = 0;
	
	$("#aa_next").click(function(){
		if(position != positions.length - 1) {
			position++;
			$("#aa_wrap ul").animate({
				left: positions[position] + "px"
			}, 1000 );
		}
		return false;
	});
	
	$("#aa_prev").click(function(){
		if(position != 0) {
			position--;
			$("#aa_wrap ul").animate({
				left: positions[position] + "px"
			}, 1000 );
		}
		return false;
	});
	
	$("#aa_wrap ul li img").mouseover(function(){
		clearTimeout(aa_timer);
		var aid = $(this).parent().attr('id');
		aid = aid.split('-');
		$("#aa_tooltip").fadeOut(function(){
			$(this).remove();
		});
		$('body').prepend('<div id="aa_tooltip"><div id="aa_tt_top"></div><div id="aa_tt_bottom"></div></div>');
		$("#aa_tooltip").mouseover(function(){
			clearTimeout(aa_timer);
		}).mouseout(function(){
			aa_timer = setTimeout(function(){
			$("#aa_tooltip").fadeOut(function(){
				$(this).remove();
			});	
		}, 300);
		});
		var li = $(this);
		var tt = $("#aa_tooltip");
		var li_width = li.width();
		var li_pos_left = li.offset().left;
		var li_pos_top = li.offset().top;
		var tt_width = tt.width();
		var tt_height = tt.height();
		if(tt.css('padding-left') != "0px") {
			var padding_arr = tt.css('padding-left').split('p');
			var tt_pos_left = ((li_width / 2) + li_pos_left) - (tt_width / 2) - padding_arr[0];
		} else {
			var tt_pos_left = ((li_width / 2) + li_pos_left) - (tt_width / 2);
		}
		var tt_pos_top = li_pos_top - 15 - tt_height;
		$("#aa_tooltip").css({
			top: tt_pos_top + "px",
			left: tt_pos_left + "px"
		}).fadeIn(function(){			
			get_author_info(aid[1]);
		});
	});
	
	$("#aa_wrap ul li img").mouseout(function(){
		aa_timer = setTimeout(function(){
			$("#aa_tooltip").fadeOut(function(){
				$(this).remove();
			});	
		}, 300);
	});
}
