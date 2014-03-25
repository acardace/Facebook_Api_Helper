<?php
//All this procedure is described in the Facebook PHP getting started, it is used to initialize a base facebook class which will be used for all
//the API calls.
//the 'appId' and 'secret' are parameters taken from the facebook app dashboard and are unique for each application.

require_once('src/facebook.php');

$config = array(
   'appId' => '',
   'secret' => '',
   'fileUpload' => false, // optional
   'allowSignedRequest' => false, // optional, but should be set to false for non-canvas apps
);

global $facebook;
$facebook = new Facebook($config);
?>
