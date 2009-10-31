<?php
function FacepressUpdate($post_ID, $cron = false) {

	global $fbStatusCookieFile;
	$Facepress_data = array();
	$Facepress_data = get_option("Facepress_options", $Facepress_data);

	// post data
	$post = get_post($post_ID);
	$authorID = $post->post_author;
	$authorLogin = get_the_author_meta('user_login', $authorID);

	$optionIndex = array_search('wplog:' . $authorLogin, $Facepress_data);

	if ($optionIndex && $post->post_type == 'post') 
	{
//		Check for email and password - necessary to login
		$fbUserEmail = substr($Facepress_data[$optionIndex+1],6,strlen($Facepress_data[$optionIndex+1])-6);
		$fbUserPassword = substr($Facepress_data[$optionIndex+2],6,strlen($Facepress_data[$optionIndex+2])-6);

		if ( strlen($fbUserEmail) == 0 || strlen($fbUserPassword) == 0 )
		{
			return;
		}

		$fbShortenURL = 'false';

		if ( substr($Facepress_data[$optionIndex+4],0,6) == 'fbsho:' ) 
		{	
			if ( substr($Facepress_data[$optionIndex+4],6,4) == 'true' )
			{
				$fbShortenURL = 'true';
			}
		}
		if ( substr($Facepress_data[$optionIndex+5],0,6) == 'fbsho:' ) 
		{	
			if ( substr($Facepress_data[$optionIndex+5],6,4) == 'true' )
			{
				$fbShortenURL = 'true';
			}
		}

		$plugins = get_option('active_plugins');
		$required_plugin = 'twitter-friendly-links/twitter-friendly-links.php';
		//check to see if Twitter Friendly Links plugin is activated			
		if ( ( $fbShortenURL == 'true' ) && in_array( $required_plugin , $plugins ) ) {
			$postUrl = permalink_to_twitter_link(get_permalink($post_ID)); // if yes, we want to use that for our URL shortening service.
		}
		else {
			$postUrl = get_permalink($post_ID);
		}
		
		$postTitle = $post->post_title;
		$postStatus = $post->post_status;
		unset($post);

		$title = $postTitle." ".$postUrl;

		$loginUrl = "https://login.facebook.com/login.php?m&locale=en_US&next=http://m.facebook.com/home.php%3Flocale=en_US";
		$postData = "locale=en_US&email=".$fbUserEmail."&pass=".$fbUserPassword."&persistent=1&login=".urlencode("Log In");

		// clean the session data before starting
		// the file should already be clean but... you never know if someone else touches it with some malicious plugin
		deleteFbSessionData();

		$loginResponse = getPage($loginUrl, $postData);

		// if the redirect can't be followed due to php restrictions, add a second call to the FB home
		if(ini_get('safe_mode')) 
		{
			$homeUrl = "http://m.facebook.com/home.php?locale=en_US";
			$loginResponse = getPage($homeUrl);
		}

		if (strpos($loginResponse, "type=\"password\"") !== false) 		{
			return;
		}

//		Check for post to profile
		if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbpst:' ) 
		{	
			if ( substr($Facepress_data[$optionIndex+3],6,4) == 'true' )
			{
//			Post to profile

	array_push($Facepress_data, 'post to profile');
	update_option("Facepress_options", $Facepress_data);


				preg_match('/name="post_form_id" value="(.*?)"/i', $loginResponse, $postFormId);

				if (isSet($postFormId[1])) 
				{

					$postData = "post_form_id=".$postFormId[1]."&status=".urlencode($title)."&update=Update+Status&locale=en_US";
					$statusFormUrl = "http://m.facebook.com/a/home.php?locale=en_US";

					$updateResponse = getPage($statusFormUrl, $postData);
				}
			}
		}
  
//			Check for page id
		if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbwal:' ) 
		{
			$facebookPageID = substr($Facepress_data[$optionIndex+3],6,strlen($Facepress_data[$optionIndex+3])-6); 
		}
		else
		{
			$facebookPageID = substr($Facepress_data[$optionIndex+4],6,strlen($Facepress_data[$optionIndex+4])-6); 
		}
		if (strlen($facebookPageID) > 0)
		{
			$wallResponse = getPage("http://m.facebook.com/wall.php?id=".$facebookPageID."&locale=en_US");
			preg_match('/name="post_form_id" value="(.*?)"/i', $wallResponse, $postFormId);

			if (isSet($postFormId[1])) 
			{
				$postData = "post_form_id=".$postFormId[1]."&comment=".urlencode($title)."&post=Post&locale=en_US";
				$statusFormUrl = "http://m.facebook.com/wall.php?id=".$facebookPageID."&locale=en_US";

				// no way to check if this has gone right, FB do not return any message
				$updateResponse = getPage($statusFormUrl, $postData);

			}
		}
	}
	else
	{
		return;
	}

	getPage("http://m.facebook.com/logout.php");

	// everything has been done, clean the fbSessionData.txt file so that if someone tries to download it, it's empty
	deleteFbSessionData();

}
?>