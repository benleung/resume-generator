<?php

// Display this code source if asked.
if (isset($_GET['source'])) exit('<!DOCTYPE HTML><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><title>OpenTBS plug-in for TinyButStrong - demo source</title></head><body>'.highlight_file(__FILE__,true).'</body></html>');

// Read user choices
if (!isset($_POST['btn_go']) && !isset($_POST['save_as'])){
	//header("Location: ../cv_submit.php"); 
	exit('Your Session Expires! You might try to use the browser\'s back button and go back to the submission form. (http://webhost1.ust.hk/~ddp2014/cgi-bin/intranet/cv_submit.php)');
}

// Retrieve the template to open
$template = (isset($_POST['tpl'])) ? $_POST['tpl'] : '';
$template = basename($template); // for security
$info= pathinfo($template);

// Checks
if (substr($template,0,5)!=='demo_') exit("Wrong file.");
if (!file_exists($template)) exit("The asked template ($template) does not exist.");

// Start the demo
$script = $info['filename'].'.php';
include($script);
?>