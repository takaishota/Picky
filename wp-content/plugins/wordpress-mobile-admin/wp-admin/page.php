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

// Save data
if(isset($_POST) && !empty($_POST))
{
	if(edit_post($_POST))
	{
		header("Location:".admin_url().$_POST['_wp_http_referer'].'&message=1');
		exit();
	}
}

$waplString = '';
$pageTitle = __('Edit Page');
$pageId = 'editPage';
$thisMainMenu = 'Pages';

// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');

// Navigation
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'main.php');
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'menu'.DIRECTORY_SEPARATOR.'page.php');
$waplString .= '<wordsChunk id="'.$pageId.'"><quick_text>[p]edit page[/p][h2]'.$pageH1.'[/h2]</quick_text></wordsChunk>';

$post = get_post_to_edit($_REQUEST['post']);

list($permalink, $post_name) = get_sample_permalink($post->ID);

$waplString .= '
<form>
	<action>post.php?post_type=page</action>
	<formItem item_type="hidden"><name>user_ID</name><value>'.(int) $user_ID.'</value></formItem>
	<formItem item_type="hidden"><name>post_ID</name><value>'.$post->ID.'</value></formItem>
	<formItem item_type="hidden"><name>ID</name><value>'.$post->ID.'</value></formItem>
	<formItem item_type="hidden"><name>post_type</name><value>page</value></formItem>
	<formItem item_type="hidden"><name>comment_status</name><value>'.$post->comment_status.'</value></formItem>
	<formItem item_type="hidden"><name>ping_status</name><value>'.$post->ping_status.'</value></formItem>
	<formItem item_type="hidden"><name>action</name><value>save</value></formItem>
	<formItem item_type="hidden"><name>_wp_http_referer</name><value>edit.php?post_type=page</value></formItem>
	<formItem item_type="text"><label>'.__('Title').'</label><name>post_title</name><value>'.$post->post_title.'</value></formItem>
	<formItem item_type="text"><label>'.__('Permalink').'</label><name>post_name</name><value>'.$post_name.'</value></formItem>';

$trans = '';
$trans = (is_array($trans)) ? $trans : get_html_translation_table(HTML_ENTITIES, ENT_QUOTES);
foreach ($trans as $k=>$v)
	$trans[$k]= "&#".ord($k).";";

$waplString .= '
	<formItem item_type="textarea"><label>'.__('Content').'</label><event><name>onkeyup</name><action>grow(this);</action></event><name>content</name><value>'.utf8_encode(htmlentities(strtr($post->post_content, $trans))).'
&#160;
</value></formItem>
	<formItem item_type="select"><name>post_status</name>
	<value>'.$post->post_status.'</value>
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
	<formItem item_type="submit"><label>'.__('Save').'</label><name>edit_post_save</name></formItem>
</form>';

// Footer
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>