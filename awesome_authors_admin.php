<?php
/*
Author: Stephen Coley
Author URI: http://www.dknewmedia.com
Description: Embed an Author Bar into your theme
*/
	
	$aa_opts = get_option('aa_opts');
	$aa_opts_arr = explode(";", $aa_opts);
	$aa_size = $aa_opts_arr[0];
	$aa_num = $aa_opts_arr[1];
	$aa_min = $aa_opts_arr[2];
	$aa_sticky = explode(",", $aa_opts_arr[3]);
	$aa_excluded = explode(",", $aa_opts_arr[4]);
	$author_list = "";
	$sticky_list = "";
	$excluded_list = "";
	global $wpdb;
	$aa_query = "SELECT $wpdb->users.ID, display_name, user_url, user_email FROM $wpdb->users ORDER BY display_name ASC;";
	$authors = $wpdb->get_results($aa_query, OBJECT);
	
	foreach($authors as $author) {
		$gravatar = "http://www.gravatar.com/avatar/" . md5(strtolower(trim($author->user_email))) . "?s=40";
		if(in_array($author->ID, $aa_sticky)) {
			$sticky_list .= "<li id='author-$author->ID'><img src='$gravatar' alt='$author->display_name' title='$author->display_name' /></li>\r\n";
		} else if(in_array($author->ID, $aa_excluded)) {
			$excluded_list .= "<li id='author-$author->ID'><img src='$gravatar' alt='$author->display_name' title='$author->display_name' /></li>\r\n";
		} else {
			$author_list .= "<li id='author-$author->ID'><img src='$gravatar' alt='$author->display_name' title='$author->display_name' /></li>\r\n";
		}
	}

?>
<?php if ( !empty($_POST['submit'] ) ) : ?>
<div id="message" class="updated fade"><p><strong><?php _e('Options saved.') ?></strong></p></div>
<?php endif; ?>
<div class="wrap">
	<h2>Awesome Authors Configuration</h2>
	<div class="postbox-container" style="width: 600px;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				<div class="postbox">
					<h3 class="hndle"><span>Author Bar</span></h3>
					<h4>Gravatar Size</h4>
					<p>The size (in pixels) you want your author gravatars to be</p>
					<input type="text" id="aa_gravatar_size" name="aa_gravatar_size" value="<?php if($aa_size != "" || $aa_size != "0") { echo $aa_size; } ?>" size="2" />
					<h4>Number of Authors to Display</h4>
					<p>Your Author Bar will never exceed this number (includes sticky authors)</p>
					<input type="text" id="aa_author_display" name="aa_author_display" value="<?php if($aa_num != "" || $aa_num != "0") { echo $aa_num; } ?>" size="2" />
					<h4>Author Minimum Post</h4>
					<p>This number represents the number of posts the author must have published in order to appear on the Author Bar</p>
					<input type="text" id="aa_minimum_posts" name="aa_minimum_posts" value="<?php if($aa_min != "" || $aa_min != "0") { echo $aa_min; } ?>" size="2" />
					<h4>Authors</h4>
					<p>These are the authors that will be displayed in your Author Bar. If you want one or more of them to be excluded from this list, or pushed to the front of this list, drag and drop them to the respective box.</p>
					<ul id="all_authors" class="author_list"><?php echo $author_list; ?></ul>
					<h4>Sticky Authors</h4>
					<p>These authors will always appear at the front of the Author Bar.</p>
					<ul id="sticky_authors" class="author_list"><?php echo $sticky_list; ?></ul>
					<h4>Excluded Authors</h4>
					<p>These authors will never appear in the Author Bar.</p>
					<ul id="excluded_authors" class="author_list"><?php echo $excluded_list; ?></ul>
					<button id="aa_save" class="button-primary">Save Changes</button>
				<div class="inside" style="padding:15px">
			</div>
		</div>
	</div></div></div>
<?php include('awesome_authors_sidebar.php'); ?>
