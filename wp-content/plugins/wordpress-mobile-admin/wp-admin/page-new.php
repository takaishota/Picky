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
	
if(isset($_POST) && !empty($_POST))
{
	if(write_post())
	{
		header("Location:".admin_url().$_POST['_wp_http_referer'].'&message=1');
		exit();
	}
}
$waplString = '';
$pageTitle = __('Add New Page');
$pageId = 'addPage';
$thisMainMenu = 'Pages';

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'page.php');
$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]add new page[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

global $user_ID;
$form_action = 'post';
$post_ID = isset($post_ID) ? (int) $post_ID : 0;
$post = get_default_post_to_edit();
$temp_ID = -1 * time();

$waplString .= '
<form>
	<action>post-new.php</action>
	<formItem item_type="hidden"><name>action</name><value>post-quickpress-save</value></formItem>
	<formItem item_type="hidden"><name>comment_status</name><value>open</value></formItem>
	<formItem item_type="hidden"><name>ping_status</name><value>open</value></formItem>
	<formItem item_type="hidden"><name>quickpress_post_ID</name><value>0</value></formItem>
	<formItem item_type="hidden"><name>_wpnonce</name><value>'.wp_create_nonce('add-post').'</value></formItem>
	<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit.php?post_type=page</value></formItem>
	<formItem item_type="hidden"><name>post_type</name><value>page</value></formItem>
	<formItem item_type="hidden"><name>user_ID</name><value>'.(int) $user_ID.'</value></formItem>
	<formItem item_type="hidden"><name>post_id</name><value>0</value></formItem>
	<formItem item_type="hidden"><name>temp_ID</name><value>'.$temp_ID.'</value></formItem>
	<formItem item_type="text"><label>'.__('Title').'</label><name>post_title</name><value></value></formItem>
	<formItem item_type="text"><label>'.__('Permalink').'</label><name>post_name</name><value></value></formItem>
	<formItem item_type="textarea"><label>'.__('Content').'</label><event><name>onkeyup</name><action>grow(this);</action></event><name>content</name><value></value></formItem>
	<formItem item_type="select"><name>post_status</name>
	<label>'.__('Status').'</label>
		<possibility>
			<label>'.__('Draft').'</label>
			<value>draft</value>
		</possibility>
		<possibility>
			<label>'.__('Pending Review').'</label>
			<value>pending</value>
		</possibility>';
if(current_user_can('publish_posts'))
{
	$waplString .= '<possibility>
			<label>'.__('Published').'</label>
			<value>publish</value>
		</possibility>';
}	
$waplString .= '</formItem>
	<formItem item_type="submit"><label>'.__('Add').'</label><name>add_new_save</name></formItem>
</form>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>