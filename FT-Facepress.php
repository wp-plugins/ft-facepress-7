<?php
/*
Plugin Name: FT FacePress
Plugin URI: http://fullthrottledevelopment.com/facepress
Description: This plugin publishes the title and permalink of your post as the status of your Facebook profile. Each WordPress author can setup his or her own Facebook access. Also, the WordPress post information can be published to a Facebook page for which the WordPress author is an administrator.
Author: FullThrottle Development - Alan Knox
Version: 1.1
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

}
?>