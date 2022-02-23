<?php
if(!session_id()) {
    session_start();
}
include_once('php-graph-sdk-5.x/src/Facebook/autoload.php');
$fb = new Facebook\Facebook(array(
	'app_id' => '931389057741742',
	'app_secret' => '98dd19f1a38700ac6990dbe804930fd9',  
	'default_graph_version' => 'v3.2',
));

$helper = $fb->getRedirectLoginHelper();
?>