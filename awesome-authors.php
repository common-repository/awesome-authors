<?php
	/*
	Plugin Name: Awesome Authors
	Plugin URL: http://wordpress.org/extend/plugins/awesome-authors/
	Description: Displays your most active authors in a widget
	Version: 1.0.1
	Author: Stephen Coley
	Author URI: http://coley.co

	Copyright 2010  DK New Media

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as 
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	*/

	function aa_install() {
		add_option('aa_opts', 'gravatar:60;number:10;min:1;sticky:0;excluded:0');
	}

	function aa_uninstall() {
		delete_option('aa_opts');
	}

	function widget_awesome_authors($args) {
		extract($args);
		echo $before_widget;
		echo $before_title;?><?php echo $after_title;
		echo '<div id="aa_subdiv">';
		awesome_authors();
		echo '</div>';
		echo $after_widget;
	}

	function awesome_authors_widget_init() {
		register_sidebar_widget(__('Awesome Authors'), 'widget_awesome_authors');
	}

	function awesome_authors() {

		$url = get_bloginfo('wpurl');
		
$ajaxcall =<<<eod
	<script type="text/javascript">
		jQuery.ajax({
			type: 'POST',
			url: '$url' + '/wp-admin/admin-ajax.php',
			data: "action=aa_bar",
			success: function(data) {
				jQuery("#aa_subdiv").css('background-image', 'none').html(data);
				aa_scripts();
			},
			error: function(){
				jQuery("#aa_subdiv").css('background-image', 'none').html("<p>There was a problem retrieving author data</p>");
			}
		});
	</script>
eod;

		echo $ajaxcall;

	}
	
	function aa_get_authors() {
		$aa_opts = get_option('aa_opts');
		$aa_opts_arr = explode(";", $aa_opts);
		$aa_size = $aa_opts_arr[0];
		$aa_num = $aa_opts_arr[1];
		$aa_min = $aa_opts_arr[2];
		$aa_sticky_authors_arr = explode(",", $aa_opts_arr[3]);
		$aa_exclude_authors_arr = explode(",", $aa_opts_arr[4]);
		$aa_output = "";
		$aa_output .= "<div id='awesomeAuthors'>\r\n";
		$aa_output .= "<a id='aa_prev' href='#'>Prev</a>\r\n";
		$aa_output .= "<div id='aa_wrap'>\r\n";
		$aa_output .= "<ul>\r\n";
		$aa_sticky_output = "";

		global $wpdb;
		$aa_num = $wpdb->escape($aa_num);
		$aa_min = $wpdb->escape($aa_min);
		$aa_query = "SELECT $wpdb->users.ID, display_name, user_url, user_email, MAX(post_date) as date, COUNT(post_date) as post_count FROM $wpdb->users, $wpdb->posts WHERE $wpdb->users.ID = $wpdb->posts.post_author AND $wpdb->posts.post_status = 'publish' AND $wpdb->posts.post_type = 'post' GROUP BY display_name HAVING post_count > $aa_min ORDER BY date DESC LIMIT $aa_num;";
		$authors = $wpdb->get_results($aa_query, OBJECT);

		if($authors) {
			foreach($authors as $author) :
				if(in_array($author->ID, $aa_sticky_authors_arr)) {
					$gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($author->user_email))) . "?s=".$aa_size;
					$aa_sticky_output .= "<li id='author-$author->ID'><img src='$gravatar' alt='$author->display_name' /></li>\r\n";
					continue;
				} elseif(in_array($author->ID, $aa_exclude_authors_arr)) {
					continue;
				}
				$gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($author->user_email))) . "?s=".$aa_size;
				$aa_output_buffer .= "<li id='author-$author->ID'><img src='$gravatar' alt='$author->display_name' /></li>\r\n";
			endforeach;
		} else {
			echo "<p>There are no authors with published posts for this blog.</p>";
		}
		
		$aa_output .= $aa_sticky_output . $aa_output_buffer;
		$aa_output .= "</ul>\r\n";
		$aa_output .= "</div>\r\n";
		$aa_output .= "<a id='aa_next' href='#'>Next</a>\r\n";
		$aa_output .= "</div>\r\n";

		echo $aa_output;
		die();
	}
	
	function awesome_authors_admin() {
		include('awesome_authors_admin.php');
	}
	
	function awesome_authors_admin_actions() {
		$plugin_page = add_options_page("Awesome Authors", "Awesome Authors", 1, "Awesome-Authors", "awesome_authors_admin");
		add_action('admin_head-' . $plugin_page, 'aa_admin_init');
	}

	function aa_admin_init() {
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_head();
		echo "<link href='" . get_bloginfo('url') ."/wp-content/plugins/awesome-authors/admin-style.css' rel='stylesheet' />";
		echo "<script type='text/javascript' src='" . get_bloginfo('url') . "/wp-content/plugins/awesome-authors/admin-script.js'></script>";
	}

	


	function add_resources() {
		$url = get_bloginfo('wpurl');

$ajaxcall =<<<eod
	<script type="text/javascript">
		function get_author_info(author_id) {
			jQuery.ajax({
				type: 'POST',
				url: '$url' + '/wp-admin/admin-ajax.php',
				data: "action=author_info&author=" + author_id,
				success: function(data) {
					jQuery("#aa_tt_top").css('background-image', 'none').html(data);
				},
				error: function(){
					jQuery("#aa_tt_top").css('background-image', 'none').html("<p>There was a problem retrieving author data</p>");
				}
			});
		}
	</script>
eod;

		echo '<link rel="stylesheet" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/awesome-authors/style.css" />';
		wp_enqueue_script('jquery');
		echo '<script type="text/javascript" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/awesome-authors/script.js" ></script>';
		echo $ajaxcall;

	}

	function author_info_response() {
		global $wpdb;
		$aid = $wpdb->escape($_POST['author']);
		$aa_tt_query = "SELECT display_name, user_url, meta_value FROM $wpdb->users, $wpdb->usermeta WHERE $wpdb->users.ID = $wpdb->usermeta.user_id AND $wpdb->usermeta.meta_key = 'description' AND $wpdb->users.ID = '$aid';";
		$info = $wpdb->get_results($aa_tt_query, OBJECT);
		$aa_tt_output = "";
		if($info) {
			foreach($info as $author) :
				$aa_tt_output .= "<h3><a href='$author->user_url'>$author->display_name</a></h3>\r\n";
				$aa_tt_output .= "<p>$author->meta_value</p>\r\n";
				$aa_tt_link_ouput = "<p><a href='$author->user_url'>Author homepage &raquo;</a></p>";
			endforeach;
			echo $aa_tt_output;
		} else {
			$aa_tt_query = "SELECT display_name, user_url FROM $wpdb->users WHERE $wpdb->users.ID = '$aid';";
			$info = $wpdb->get_results($aa_tt_query, OBJECT);
			$aa_tt_output = "";
			if($info) {
				foreach($info as $author) :
					$aa_tt_output .= "<h3><a href='$author->user_url'>$author->display_name</a></h3>\r\n";
					$aa_tt_output .= "<p>This author does not have a bio.</p>\r\n";
					$aa_tt_link_ouput = "<p><a href='$author->user_url'>Author homepage &raquo;</a></p>";
				endforeach;
				echo $aa_tt_output;
			} else {
				echo "<p>There was a problem retrieving this author's info.</p>";
			}
		}
		$aa_tt_rp_query = "SELECT post_title, guid FROM $wpdb->posts WHERE post_author = '$aid' AND post_type = 'post' AND post_status = 'publish' ORDER BY post_date DESC LIMIT 5;";
		$posts = $wpdb->get_results($aa_tt_rp_query, OBJECT);
			if($posts) {
				$aa_tt_rp_output = "<ul>\r\n";
				foreach($posts as $post) :
					$aa_tt_rp_output .= "<li><a href='$post->guid'>$post->post_title</a></li>\r\n";
				endforeach;
				$aa_tt_rp_output .= "</ul>\r\n";
				echo $aa_tt_rp_output;
			} else {
				echo "<p>No recent posts</p>";
			}
			echo $aa_tt_link_ouput;
		die();
	}

	function aa_save_opts() {
		$aa_opts = $_POST['aa_opts'];
		if(update_option('aa_opts', $aa_opts)) {
			echo "saved";
		} else {
			echo "error";
		}
		die();
	}

	function aa_gettwitter() {
		$rss = fetch_feed('http://twitter.com/statuses/user_timeline/60321100.rss');
		$maxitems = $rss->get_item_quantity(5); 
		$rss_items = $rss->get_items(0, $maxitems); 
		echo "<ul style='list-style:square; margin-left: 20px'>";
		foreach ( $rss_items as $item ) :
			$title = auto_link_twitter($item->get_title());
			if (substr($title,0,5)!="links") {
				echo "<li>";
				echo $title;
				echo "</li>";
		} endforeach;
		echo "</ul>";
		die;
	}

	function aa_getblog() {
		$rss = fetch_feed('http://feeds.feedburner.com/DouglasKarr');
		$maxitems = $rss->get_item_quantity(5); 
		$rss_items = $rss->get_items(0, $maxitems); 
		echo "<ul style='list-style:square; margin-left: 20px'>";
		foreach ( $rss_items as $item ) :
			$title = $item->get_title();
			if (substr($title,0,5)!="links") {
			echo "<li>";
			echo $title;
			echo " <a href='".$item->get_permalink()."' title='".$title."' target='_blank'>read&nbsp;&raquo;</a>";
			echo "</li>";
		 } endforeach;
		echo "</ul>";
		die;
	}

	function auto_link_twitter($text) {
    		// properly formatted URLs
    		$urls = "/(((http[s]?:\/\/)|(www\.))?(([a-z][-a-z0-9]+\.)?[a-z][-a-z0-9]+\.[a-z]+(\.[a-z]{2,2})?)\/?[a-z0-9._\/~#&=;%+?-]+[a-z0-9\/#=?]{1,1})/is";
    		$text = preg_replace($urls, " <a href='$1'>$1</a>", $text);

    		// URLs without protocols
    		$text = preg_replace("/href=\"www/", "href=\"http://www", $text);

    		// Twitter usernames
    		$twitter = "/@([A-Za-z0-9_]+)/is";
    		$text = preg_replace ($twitter, " <a href='http://twitter.com/$1'>@$1</a>", $text);

    		// Twitter hashtags
    		$hashtag = "/#([A-Aa-z0-9_-]+)/is";
    		$text = preg_replace ($hashtag, " <a href='http://hashtags.org/$1'>#$1</a>", $text);
    		return $text;
	}
	
	register_activation_hook(__FILE__, 'aa_install');
	register_deactivation_hook(__FILE__, 'aa_uninstall');
	add_action("plugins_loaded", "awesome_authors_widget_init");
	add_action('admin_menu', 'awesome_authors_admin_actions');
	add_action('wp_ajax_aa_bar', 'aa_get_authors');
	add_action('wp_ajax_nopriv_aa_bar', 'aa_get_authors');
	add_action('wp_ajax_author_info', 'author_info_response');
	add_action('wp_ajax_nopriv_author_info', 'author_info_response');
	add_action('wp_ajax_aa_gettwitter', 'aa_gettwitter' );
	//add_action('wp_ajax_wpwt_gettwitter2', 'wpwt_gettwitter2' );
	add_action('wp_ajax_aa_getblog', 'aa_getblog' );
	add_action('wp_ajax_aa_save_opts', 'aa_save_opts');
	add_action('wp_footer', 'add_resources');
	
	
	
?>
