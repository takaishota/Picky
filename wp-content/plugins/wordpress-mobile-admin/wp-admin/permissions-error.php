<?php
if ( ! defined('ABSPATH') ) die();
// Admin bootstaps
require_once(ABSPATH.'wp-admin'.DIRECTORY_SEPARATOR.'admin.php');
require_once(ABSPATH.'wp-includes'.DIRECTORY_SEPARATOR.'pluggable.php');

// Do this here just to be on the safe side
if(!is_user_logged_in())
{
	header("Location:".get_bloginfo('home').'/wp-login.php');
	exit();
}
require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'functions.php');

$waplString = '';
$pageTitle = 'Permissions';
$pageId = 'permissions';
$subNav = false;
$showHeader = false;

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>You do not have sufficient permissions to access this page.</quick_text></wordsChunk>';
$waplString .= '</layout></wapl>';

// Convert WAPL into markup
process_admin_wapl($waplString);
?>