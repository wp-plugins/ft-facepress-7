<?php
/*
Plugin Name: FT FacePress
Plugin URI: http://fullthrottledevelopment.com/facepress
Description: This plugin has been replaced by FT FacePress II. This plugin publishes the title and permalink of your post as the status of your Facebook profile. Each WordPress author can setup his or her own Facebook access. Also, the WordPress post information can be published to a Facebook page for which the WordPress author is an administrator.
Author: Alan Knox @ FullThrottle Development
Version: 1.5
Author URI: http://fullthrottledevelopment.com/
*/

/*
Copyright 2009  FullThrottle Development

*/

$fbStatusUpdatePath = __FILE__;
$fbStatusUpdatePath = substr($fbStatusUpdatePath, 0,  strrpos($fbStatusUpdatePath, "/"));
$fbStatusCookieFile = $fbStatusUpdatePath."/FacepressSessionFile.txt";

include('Facepress-functions.php');

include('Facepress-updater.php');

include('Facepress-option.php');

include('Facepress-activation.php');

function facepress_activation_notice(){
		echo '<div class="error fade"><p><a href="http://wordpress.org/extend/plugins/facepress-ii/" target="_blank">FacePress plugin has been replaced by FacePress II. Remove FacePress and install FacePress II.</a></p></div>';
}

if (isSet($_GET["checkLogin"]) && $_GET["checkLogin"] == "true") {
	include('Facepress-check.php');
} else {
	function addFacepressOptionPage() {
		add_options_page('FT-FacePress', 'FT-FacePress', 9, basename(__FILE__), "FTFacepressOptionPage");
		add_submenu_page('users.php', 'FT-FacePress', 'FT-FacePress', 2, __FILE__, "FTFacepressUserProfilePage");
	}

	register_activation_hook( __FILE__, "fbStatusAct");
	add_action('admin_menu', 'addFacepressOptionPage');
	add_action('future_to_publish', 'FacepressUpdate');
	add_action('new_to_publish', 'FacepressUpdate');
	add_action('draft_to_publish', 'FacepressUpdate');
	add_action('edit_form_advanced', 'facepress_add_meta_tags');
	add_action('edit_page_form', 'facepress_add_meta_tags');
	add_action('edit_post', 'facepress_meta_tags');
	add_action('publish_post', 'facepress_meta_tags');
	add_action('save_post', 'facepress_meta_tags');
	add_action('edit_page_form', 'facepress_meta_tags');
	add_action( 'admin_notices', 'facepress_activation_notice');
}

?>

