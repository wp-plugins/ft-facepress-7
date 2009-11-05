<?php
function deleteFbSessionData() {
	global $fbStatusCookieFile;
	// if the file is not writable, the function is not called so there's no need for double check also inside the function
	if ($handle = fopen($fbStatusCookieFile, 'w')) {
		if (fwrite($handle, "") === FALSE) {
			return false;
		}
	} else {
		fclose($handle);
		return false;
	}
	fclose($handle);
	return true;
}

function getPage($url, $postData = null, $username = null, $password = null) {
	global $fbStatusUpdatePath, $fbStatusCookieFile;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, false);
	if(!ini_get('safe_mode')) {
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
	}
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $fbStatusCookieFile);
	curl_setopt($ch, CURLOPT_COOKIEFILE, $fbStatusCookieFile);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_REFERER, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.9.0.6; .NET CLR 3.0; ffco7) Gecko/2009011913 Firefox/3.0.6");

	if ($postData != null) {
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		curl_setopt($ch, CURLOPT_POST, true);
	}

	if ($username != null && $password != null) {
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
	}

	$response = curl_exec($ch);
	curl_close($ch);

	unset($ch);

	return $response;
}
?>