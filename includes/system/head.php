<?php
/**
 * Displays the site navigation.
 *
 * @package PACMEC
 * @subpackage Tiny
 * @since 0.0.1
var authUrl = '<?=siteinfo('siteurl');?>/includes/auth.php'; // url of 'auth.php' from php-api-auth
var clientId = 'default'; // client id as defined in php-api-auth
var audience = '/includes/index.php'; // api audience as defined in php-api-auth

window.onload = function () {
	var match = RegExp('[#&]access_token=([^&]*)').exec(window.location.hash);
	var accessToken = match && decodeURIComponent(match[1].replace(/\+/g, ' '));
	if (!accessToken) {
		document.location = authUrl+'?audience='+audience+'&response_type=token&client_id='+clientId+'&redirect_uri='+document.location.href;
	} else {
		document.location.hash = '';
		var req = new XMLHttpRequest();
		req.onreadystatechange = function () {
			if (req.readyState==4) {
				console.log(req.responseText);
				console.log(req.responseText);
			}
		}
		url = '<?= siteinfo("siteurl"); ?>/includes/index.php/me';
		req.open("GET", url, true);
		req.setRequestHeader('X-Authorization', 'Bearer '+accessToken);
		req.send();
	}
};
 */
foreach($GLOBALS['PACMEC']['website']['styles']['head'] as $file){
	echo "<link href=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\">";
}
do_action( "head" );
foreach($GLOBALS['PACMEC']['website']['scripts']['head'] as $file){
	echo "<script src=\"{$file['src']}\" rel=\"{$file['rel']}\" type=\"{$file['type']}\"></script>";
}
if(isAdmin()){
	echo "<script>\n";
		echo "const route = ".json_encode($GLOBALS['PACMEC']['route'], JSON_PRETTY_PRINT)."; \n";
		echo "const styles_list = ".json_encode($GLOBALS['PACMEC']['website']['styles']['list'], JSON_PRETTY_PRINT)."; \n";
		echo "const scripts_list = ".json_encode($GLOBALS['PACMEC']['website']['scripts']['list'], JSON_PRETTY_PRINT)."; \n";
	echo "</script>\n";
}
?>