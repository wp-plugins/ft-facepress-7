<?php
function fbStatusAct() {
	// clean everything @ activation
	//delete_option('fb-status-update');
	
	if (!function_exists("curl_init")) {
		deactivate_plugins(__FILE__);
		die("This plugin needs <a href=\"http://www.php.net/curl\"></a> to be installed on your server in order to run properly.");
	}
}
?>