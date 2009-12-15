<?php
function FTFacepressOptionPage(){
	global $fbStatusCookieFile;
	global $FacePressEncryptionKey;

	if (isSet($_POST["action"]) && $_POST["action"] == "fb-status-update") {

		$Facepress_data = array();
		$Facepress_data[0] = "FT-FacePress1.2.1";
		$array_keys = array_keys($_POST);
		for( $i=0;$i<count($array_keys)-2;$i++ ) {
// 			echo '<br />' . $array_keys[$i] . ' - ' . $_POST[$array_keys[$i]] . '<br />';
 			if ( substr($array_keys[$i],0,7) == 'wplogin') { $keyString = 'wplog:'; }
 			if ( substr($array_keys[$i],0,7) == 'fbemail') { $keyString = 'fbeml:'; }
 			if ( substr($array_keys[$i],0,10) == 'fbpassword') { $keyString = 'fbpas:'; }
 			if ( substr($array_keys[$i],0,6) == 'fbpost') { 
    $keyString = 'fbpst:true'; }
 			if ( substr($array_keys[$i],0,6) == 'fbwall') { $keyString = 'fbwal:'; }
			if ( substr($array_keys[$i],0,7) == 'fbshort') { $keyString = 'fbsho:true'; }
			if ( substr($array_keys[$i],0,8) == 'fbformat') { $keyString = 'fbfor:'; }

 			array_push($Facepress_data, $keyString . $_POST[$array_keys[$i]]);
		}

		update_option("Facepress_options", $Facepress_data);

	}
?>

<div class="wrap">
	<h2>FT-FacePress Options</h2>

	<form method="post" autocomplete="off">
		<div id="poststuff" class="metabox-holder">
			<div class="postbox">
				<h3 class="hndle"><span>FT-FacePress Information</h3>
				<div class="inside">
					<p>This plugin will update the status of Facebook Profile of the WordPress author with the post title and link. Also, if the Facebook user (WordPress author) is the admin of a Facebook page, the plugin can also add the post title link to that Facebook page.</p>
					<p>In order to update either your Facebook status profile or a Facebook wall, you must enter your Facebook login email address and password.</p>
					<p>If you check "Publish to Facebook Profile Status", then your WordPress post information will be published to your Facebook status.</p>
					<p>If you enter a valid Facebook page ID, then your WordPress post information will be published to that Facebook Page.</p>
					<p>If "Publish to Facebook Profile Status" is unchecked and there is nothing entered in the Page Id field, then nothing will be published to Facebook.</p>
					<?php if (!is_writable($fbStatusCookieFile)) { ?>
						<p class="error">Php/Wordpress cannot write into this file: <strong><?php echo($fbStatusCookieFile) ?></strong>. Please ensure PHP has the correct permissions set to write and update that file. If you don't know what I'm speaking about, please contact your server admin / webmaster. If you don't want to see this message every time you publish a new post while you try solving the problem, just <a href="/wp-admin/plugins.php">disable this plugin</a>. <a href="http://codex.wordpress.org/Changing_File_Permissions">More about file permissions on Wordpress</a></p>
					<?php } ?>

					<p>* = required field</p>

					<?php if ($message != false) { ?>
						<div id="message" class="updated fade"><p><?php echo($message); ?></p></div>
					<?php } ?>
					<?php if ($error != false) { ?>
						<div id="message" class="error"><p><?php echo($error); ?></p></div>
					<?php } ?>
</div>
</div>

<?php

$Facepress_data = array();
$Facepress_data = get_option("Facepress_options", $Facepress_data);
// print_r ($Facepress_data);

// Get the authors from the database ordered by user nicename
	global $wpdb;
	
	$query = "SELECT ID, user_login from $wpdb->users ORDER BY user_login";
	$author_ids = $wpdb->get_results($query);

//	print_r ($author_ids);
	
// Loop through each author
	foreach($author_ids as $author) :

    $user_cap = array();
	$user_cap = get_usermeta($author->ID,'wp_capabilities');
// 	print_r ($user_cap);
//	if ($user_cap[subscriber] <> 1) echo '  not a subscriber  ';
//	if ($user_cap[subscriber] == 1) echo '****SUBSCRIBER****';
	
	// Get user data
		$curauth = get_userdata($author->ID);
//		print_r (' / ' . $curauth->user_login . ' ' . $curauth->user_level . ' / ');

	// If user level is above 0 or login name is "admin", display profile
//		if($curauth->user_level > 0 || $curauth->user_login == 'admin') : 
		if($user_cap[subscriber] <> 1) : ?>


<div class="wrap">
    <div class="postbox">
	<h3>Author / Facebook Information for <?php echo $curauth->display_name . ' (' . $curauth->user_login . ')'; ?></h3>
			<input type="hidden" name="wplogin-<?php echo $author->ID; ?>" value="<?php echo $curauth->user_login; ?>" />


					<table class="form-table" >
						<tr valign="top">
							<td style="width:150px;"><label for="fb-email"><strong>Facebook email*</strong></label></td>
							<td><input style="width: 250px;" id="fbemail-<?php echo $author->ID; ?>" name="fbemail-<?php echo $author->ID; ?>" type="text" value="<?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); if ($optionIndex) { echo substr($Facepress_data[$optionIndex+1],6,strlen($Facepress_data[$optionIndex+1])-6); } ?>" /></td>
							<td rowspan="2">
								<p></p>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="fb-password"><strong>Facebook password*</strong></label></td>
							<td><input style="width: 250px;" id="fbpassword-<?php echo $author->ID; ?>" name="fbpassword-<?php echo $author->ID; ?>" type="password" value="<?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); if ($optionIndex) { echo substr($Facepress_data[$optionIndex+2],6,strlen($Facepress_data[$optionIndex+2])-6); } ?>" /></td>
						</tr>
						<tr valign="top">
							<td><label for="fb-post-to-profile"><strong>Publish to Facebook Profile Status</strong></label></td>
							<td><input id="fbpost-to-profile-<?php echo $author->ID; ?>" name="fbpost-to-profile-<?php echo $author->ID; ?>" type="checkbox" value="" <?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); if ($optionIndex) { if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbpst:' ) { echo 'checked="checked"'; } } ?> /></td>
							<td>Check this if you want the status of the profile to be updated. If you do not want the profile status updated, please provide a Page ID.</td>
						</tr>
						<tr valign="top">
							<td><label for="fb-wall-id"><strong>Facebook Page</strong></label></td>
							<td><input style="width: 250px;" id="fbwall-id-<?php echo $author->ID; ?>" name="fbwall-id-<?php echo $author->ID; ?>" type="text" value="<?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); if ($optionIndex) { if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbwal:' ) { echo substr($Facepress_data[$optionIndex+3],6,strlen($Facepress_data[$optionIndex+3])-6); } else { echo substr($Facepress_data[$optionIndex+4],6,strlen($Facepress_data[$optionIndex+4])-6); } } ?>" /></td>
							<td>Optional <b>numeric</b> Page ID where you want to publish your post information. You can find your numeric Facebook Page ID in the page URL: <br />http://www.facebook.com/pages/YourPageName/<strong>1234567890</strong>?ref=ts<br />If the numeric Facebook Page ID is not displayed in your page URL, then choose "Edit Page" from the left side Facebook menu (under the page picture). The numeric id will be listed in the "Edit Page" URL.<br /><strong>If you are not an administrator for the specified Facebook Page, the plugin will not do anything and Facebook may ban you.</strong></td>
						</tr>
						<tr valign="top">
							<td><label for="fb-shorten-url"><strong>Use Shortened URLs</strong></label></td>
							<td><input id="fbshort-<?php echo $author->ID; ?>" name="fbshort-<?php echo $author->ID; ?>" type="checkbox" value="" <?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); if ($optionIndex) { if ( substr($Facepress_data[$optionIndex+4],0,6) == 'fbsho:' or substr($Facepress_data[$optionIndex+5],0,6) == 'fbsho:') { echo 'checked="checked"'; } } ?>" /></td>
							<td>If you check this box and the <a href\"http://wordpress.org/extend/plugins/twitter-friendly-links/\">Twitter Friendly Links Plugin</a> is installed, then FacePress will post a shortened form of your post URL instead of the full URL.</td>
						</tr>                        
						<tr valign="top">
							<td><label for="fb-format"><strong>Post Format</strong></label></td>
							<td><input style="width: 250px;" id="fbformat-<?php echo $author->ID; ?>" name="fbformat-<?php echo $author->ID; ?>" type="text" value="<?php $optionIndex = array_search('wplog:' . $curauth->user_login, $Facepress_data); 
							if ($optionIndex) { 
								if ( substr($Facepress_data[$optionIndex+4],0,6) == 'fbfor:' ) {
									$formatDisplay = htmlspecialchars(substr($Facepress_data[$optionIndex+4],6,strlen($Facepress_data[$optionIndex+4])-6));
									echo $formatDisplay; } 
								elseif ( substr($Facepress_data[$optionIndex+5],0,6) == 'fbfor:' ) { 
									$formatDisplay = htmlspecialchars(substr($Facepress_data[$optionIndex+5],6,strlen($Facepress_data[$optionIndex+5])-6));
									echo $formatDisplay; } 
								elseif ( substr($Facepress_data[$optionIndex+6],0,6) == 'fbfor:' ) { 
									$formatDisplay = htmlspecialchars(substr($Facepress_data[$optionIndex+6],6,strlen($Facepress_data[$optionIndex+6])-6));
									echo $formatDisplay; } 
								else { echo '%TITLE% %URL%'; } } ?>" /></td>
							<td>Describe the format you would like FacePress to use to publish your post information on Facebook. <strong>NOTE: Do not use double quotation marks in your format.</strong><br />Use the following descriptors: <br />%TITLE% = the title of your post<br />%URL% = the url of your post<br />%EXCERPT% = the excerpt field of your post</td>
						</tr>
					</table>
				</div>
			</div>

	
		<?php endif; ?>

	<?php endforeach; ?>

			<input type="hidden" name="action" value="fb-status-update" />
			<p class="submit"><input type="submit" name="Submit" value="Submit" /></p>

		</div>
	</form>
</div>
<?php

}
function FTFacepressUserProfilePage(){
	
	global $fbStatusCookieFile;
	global $current_user;
      get_currentuserinfo();

	$Facepress_data = array();
	$Facepress_data = get_option("Facepress_options", $Facepress_data);
//	 print_r ($Facepress_data);
	 
	if ( count($Facepress_data) == 0 )
	{
		array_push($Facepress_data, 'FT-Facepress1.1');
	}

	if (isSet($_POST["action"]) && $_POST["action"] == "fb-status-update") 
	{

		$optionIndex = array_search('wplog:' . $current_user->user_login, $Facepress_data);

		if ($optionIndex)
		{
			$clearCount = 4;
			if ( substr($Facepress_data[$optionIndex+3], 0, 6) == 'fbpst:' )
			{
				$clearCount++;
			}
			if (substr($Facepress_data[$optionIndex+4], 0, 6) == 'fbsho:' || substr($Facepress_data[$optionIndex+5], 0, 6) == 'fbsho:')
			{
				$clearCount++;
			}
			if (substr($Facepress_data[$optionIndex+4], 0, 6) == 'fbfor:' || substr($Facepress_data[$optionIndex+5], 0, 6) == 'fbfor:' || substr($Facepress_data[$optionIndex+6], 0, 6) == 'fbfor:')
			{
				$clearCount++;
			}
			$tempArray = array_splice($Facepress_data, $optionIndex, $clearCount);
		}

		array_push($Facepress_data, 'wplog:' . $current_user->user_login);

		if (isSet($_POST["fbemail"]))
		{
			array_push($Facepress_data, 'fbeml:' . trim($_POST["fbemail"]));
		}
		else
		{
			arraypush($Facepress_data, 'fbeml:');
		}
		if (isSet($_POST["fbpassword"]))
		{
			array_push($Facepress_data, 'fbpas:' . trim($_POST["fbpassword"]));
		}
		else
		{
			arraypush($Facepress_data, 'fbpas:');
		}
		if (isSet($_POST["fbpost-to-profile"]))
		{
			array_push($Facepress_data, 'fbpst:' . 'true');
		}
		if (isSet($_POST["fbwall-id"]))
		{
			array_push($Facepress_data, 'fbwal:' . trim($_POST["fbwall-id"]));
		}
		else
		{
			arraypush($Facepress_data, 'fbwal:');
		}
		if (isSet($_POST["fbshort"]))
		{
			array_push($Facepress_data, 'fbsho:' . 'true');
		}
		if (isSet($_POST["fbformat"]))
		{
			array_push($Facepress_data, 'fbfor:' . trim($_POST["fbformat"]));
		}
		array_push($Facepress_data, '-placeholder1-');
		array_push($Facepress_data, '-placeholder2-');
		update_option("Facepress_options", $Facepress_data);

	}

	$optionIndex = array_search('wplog:' . $current_user->user_login, $Facepress_data);

	$fbUserEmail = '';
	$fbUserPassword = '';
	$fbPostToProfile = 'false';
	$facebookPageID = '';
	$fbShortenURL = 'false';

	if ($optionIndex)
	{
		$fbUserEmail = substr($Facepress_data[$optionIndex+1],6,strlen($Facepress_data[$optionIndex+1])-6);
		$fbUserPassword = substr($Facepress_data[$optionIndex+2],6,strlen($Facepress_data[$optionIndex+2])-6);
		if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbpst:' ) 
		{	
			if ( substr($Facepress_data[$optionIndex+3],6,4) == 'true' )
			{
				$fbPostToProfile = 'true';
			}
		}
		if ( substr($Facepress_data[$optionIndex+3],0,6) == 'fbwal:' ) 
		{
			$facebookPageID = substr($Facepress_data[$optionIndex+3],6,strlen($Facepress_data[$optionIndex+3])-6); 
		}
		else
		{
			$facebookPageID = substr($Facepress_data[$optionIndex+4],6,strlen($Facepress_data[$optionIndex+4])-6); 
		}
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
		if ( substr($Facepress_data[$optionIndex+4],0,6) == 'fbfor:' ) 
		{
			$facebookPostFormat = substr($Facepress_data[$optionIndex+4],6,strlen($Facepress_data[$optionIndex+4])-6); 
		}
		elseif ( substr($Facepress_data[$optionIndex+5],0,6) == 'fbfor:' ) 
		{
			$facebookPostFormat = substr($Facepress_data[$optionIndex+5],6,strlen($Facepress_data[$optionIndex+5])-6); 
		}
		elseif ( substr($Facepress_data[$optionIndex+6],0,6) == 'fbfor:' ) 
		{
			$facebookPostFormat = substr($Facepress_data[$optionIndex+6],6,strlen($Facepress_data[$optionIndex+6])-6); 
		}
		else
		{
			$facebookPostFormat = "%TITLE% %URL%";
		}
	}
?>
<div class="wrap">
	<h2>FT-FacePress Options</h2>

	<form method="post" autocomplete="off">
		<div id="poststuff" class="metabox-holder">
			<div class="postbox">
				<h3 class="hndle"><span>FT-FacePress Information</h3>
				<div class="inside">
					<p>This plugin will update the status of Facebook Profile of the WordPress author with the post title and link. Also, if the Facebook user (WordPress author) is the admin of a Facebook page, the plugin can also add the post title link to that Facebook page.</p>
					<p>In order to update either your Facebook status profile or a Facebook wall, you must enter your Facebook login email address and password.</p>
					<p>If you check "Publish to Facebook Profile Status", then your WordPress post information will be published to your Facebook status.</p>
					<p>If you enter a valid Facebook page ID, then your WordPress post information will be published to that Facebook Page.</p>
					<p>If "Publish to Facebook Profile Status" is unchecked and there is nothing entered in the Page Id field, then nothing will be published to Facebook.</p>
					<?php if (!is_writable($fbStatusCookieFile)) { ?>
						<p class="error">Php/Wordpress cannot write into this file: <strong><?php echo($fbStatusCookieFile) ?></strong>. Please ensure PHP has the correct permissions set to write and update that file. If you don't know what I'm speaking about, please contact your server admin / webmaster. If you don't want to see this message every time you publish a new post while you try solving the problem, just <a href="/wp-admin/plugins.php">disable this plugin</a>. <a href="http://codex.wordpress.org/Changing_File_Permissions">More about file permissions on Wordpress</a></p>
					<?php } ?>

					<p>* = required field</p>

					<?php if ($message != false) { ?>
						<div id="message" class="updated fade"><p><?php echo($message); ?></p></div>
					<?php } ?>
					<?php if ($error != false) { ?>
						<div id="message" class="error"><p><?php echo($error); ?></p></div>
					<?php } ?>
</div>
</div>

<div class="wrap">
    <div class="postbox">
	<h3>Author / Facebook Information for <?php echo $current_user->display_name . ' (' . $current_user->user_login . ')'; ?></h3>
			<input type="hidden" name="wplogin" value="<?php echo $current_user->user_login; ?>" />


					<table class="form-table" >
						<tr valign="top">
							<td style="width:150px;"><label for="fb-email"><strong>Facebook email*</strong></label></td>
							<td><input style="width: 250px;" id="fbemail" name="fbemail" type="text" value="<?php echo $fbUserEmail; ?>" /></td>
							<td rowspan="2">
								<p></p>
							</td>
						</tr>
						<tr valign="top">
							<td><label for="fb-password"><strong>Facebook password*</strong></label></td>
							<td><input style="width: 250px;" id="fbpassword" name="fbpassword" type="password" value="<?php echo $fbUserPassword; ?>" /></td>
						</tr>
						<tr valign="top">
							<td><label for="fb-post-to-profile"><strong>Publish to Facebook Profile Status</strong></label></td>
							<td><input id="fbpost-to-profile" name="fbpost-to-profile" type="checkbox" value="" <?php if ( $fbPostToProfile == 'true' ) { echo 'checked="checked"'; } ?> /></td>
							<td>Check this if you want the status of the profile to be updated. If you do not want the profile status updated, please provide a Page ID.</td>
						</tr>
						<tr valign="top">
							<td><label for="fb-wall-id"><strong>Facebook Page</strong></label></td>
							<td><input style="width: 250px;" id="fbwall-id" name="fbwall-id" type="text" value="<?php echo $facebookPageID; ?>" /></td>
							<td>Optional <b>numeric</b> Page ID where you want to publish your post information. You can find your numeric Facebook Page ID in the page URL: <br />http://www.facebook.com/pages/YourPageName/<strong>1234567890</strong>?ref=ts<br />If the numeric Facebook Page ID is not displayed in your page URL, then choose "Edit Page" from the left side Facebook menu (under the page picture). The numeric id will be listed in the "Edit Page" URL.<br /><strong>If you are not an administrator for the specified Facebook Page, the plugin will not do anything and Facebook may ban you.</strong></td>
						</tr>
						<tr valign="top">
							<td><label for="fb-shorten"><strong>Use Shortened URLs</strong></label></td>
							<td><input id="fbshort" name="fbshort" type="checkbox" value="" <?php if ($fbShortenURL == 'true' ) { echo 'checked="checked"'; } ?> /></td>
							<td>If you check this box and the <a href\"http://wordpress.org/extend/plugins/twitter-friendly-links/\">Twitter Friendly Links Plugin</a> is installed, then FacePress will post a shortened form of your post URL instead of the full URL.</td>
						</tr>
						<tr valign="top">
							<td><label for="fb-format"><strong>Post Format</strong></label></td>
							<td><input style="width: 250px;" id="fbformat" name="fbformat" type="text" value="<?php echo $facebookPostFormat; ?>" /></td>
							<td>Describe the format you would like FacePress to use to publish your post information on Facebook. <strong>NOTE: Do not use double quotation marks in your format.</strong><br />Use the following descriptors: <br />%TITLE% = the title of your post<br />%URL% = the url of your post<br />%EXCERPT% = the excerpt field of your post</td>
						</tr>
					</table>
				</div>
			</div>

			<input type="hidden" name="action" value="fb-status-update" />
			<p class="submit"><input type="submit" name="Submit" value="Submit" /></p>

		</div>
	</form>

<?php
}
function facepress_meta_tags($id) {
			$awmp_edit = $_POST["facepress_edit"];
			
			if (isset($awmp_edit) && !empty($awmp_edit)) {
				$exclude = $_POST["facepress_exclude"];
				$format = $_POST["facepress_format"];
				
	
				delete_post_meta($id, 'facepress_exclude');
				delete_post_meta($id, 'facepress_format');
				
				if (isset($exclude) && !empty($exclude)) {
					add_post_meta($id, 'facepress_exclude', $exclude);
				}
				if (isset($format) && !empty($format)) {
					add_post_meta($id, 'facepress_format', $format);
				}
			}
		}
		

function facepress_add_meta_tags() {
			global $post;
			$post_id = $post;
			
			if (is_object($post_id)) {
				$post_id = $post_id->ID;
			}
			
            $exclude = get_post_meta($post_id, 'facepress_exclude', true);
            $format = get_post_meta($post_id, 'facepress_format', true); ?>
	
                    <div id="postrftp" class="postbox">
                    <h3><?php _e('FacePress', 'facepress') ?></h3>
                    <div class="inside">
                    <div id="postrftp">
		
			<a target="__blank" href="http://fullthrottledevelopment.com/facepress"><?php _e('FT-FacePress', 'facepress') ?></a>
			<input value="facepress_edit" type="hidden" name="facepress_edit" />
			<table style="margin-bottom:40px">
                <tr>
                <th style="text-align:left;" colspan="2">
                </th>
                </tr>
                
                <tr>
                	<th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;"><?php _e('Do Not Publish this Post to Facebook:', 'facepress') ?></th>
                	<td><input value="1" type="checkbox" name="facepress_exclude" <?php if ((int)$exclude == 1) echo "checked"; ?> /></td>
				</tr>
				<tr valign="top">
					<th scope="row" style="text-align:right; width:150px; padding-top: 5px; padding-right:10px;"><?php _e('Post Format', 'facepress') ?></th>
					<td><input style="width: 250px;" id="fbformat" name="facepress_format" type="text" value="<?php echo $format ?>" /><br />
					Describe the format you would like FacePress to use to publish this post information on Facebook. If this field is left blank, then FacePress will use the default posting format in your FacePress Options.<strong>NOTE: Do not use double quotation marks in your format.</strong><br />Use the following descriptors: <br />%TITLE% = the title of your post<br />%URL% = the url of your post<br />%EXCERPT% = the excerpt field of your post</td>
				</tr>

			</table>
			
			</div></div></div>
	
			<?php
		}
?>