<?php 
/*
Plugin Name: WordPress Mobile Admin
Plugin URI: http://blog.wapple.net/wordpress-mobile-admin-plugin/
Description: Manage your blog from your mobile with this plugin. After activating this plugin visit <a href="options-general.php?page=wordpress-mobile-admin.php">the settings page</a> and enter your Wapple Architect Dev Key.
Author: Rich Gubby
Version: 4.0.8
Author URI: http://blog.wapple.net/
*/

if(!defined('ARCHITECT_ADMIN_DEBUG'))
	define('ARCHITECT_ADMIN_DEBUG', false);

ob_start();

if(!function_exists('get_wpma_plugin_base'))
{
	/**
	 * Get the name of the plugin folder - different on whichever way you install
	 * Must be in this file so it returns the right value
	 * @access public
	 * @return string
	 */
	function get_wpma_plugin_base()
	{
		return $base = substr(dirname(__FILE__), (strrpos(dirname(__FILE__), DIRECTORY_SEPARATOR)+1), strlen(dirname(__FILE__)));
	}
}

if(!defined('ARCHITECT_ADMIN_URL'))
	define('ARCHITECT_ADMIN_URL', WP_PLUGIN_URL.'/'.get_wpma_plugin_base().'/');
if(!defined('ARCHITECT_ADMIN_DIR'))
	define('ARCHITECT_ADMIN_DIR', WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.get_wpma_plugin_base().DIRECTORY_SEPARATOR);

require_once(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.get_wpma_plugin_base().DIRECTORY_SEPARATOR.'functions.php');

$errors = false;
if(get_option('architect_admin_devkey') == '' OR !get_option('architect_admin_devkey'))
{
	$errors = true;
}
if(!function_exists('simplexml_load_string'))
{
	$errors = true;
}

if($errors)
{
	if(!function_exists('ArchitectAdminInitError'))
	{
		function ArchitectAdminInitError()
		{
			if(!isset($_REQUEST['page']) OR (isset($_REQUEST['page']) AND $_REQUEST['page'] != 'wordpress-mobile-admin.php'))
			{
				echo '<div class="updated architectInitError"><p>There was a problem initializing the WordPress Mobile Admin plugin, please check <a href="options-general.php?page=wordpress-mobile-admin.php">the settings page</a> for more information</p></div>';
			}
		}
	}
}

// Add architect options to admin menu
add_action('admin_menu', 'add_architect_admin_options_page');

if(is_admin())
{
	add_action('admin_init', 'adminDeviceDetection');
	
	if(function_exists('ArchitectAdminInitError'))
	{
		add_action('admin_head','ArchitectAdminInitError');
	}
	
} else if(basename($_SERVER['SCRIPT_NAME']) == 'wp-login.php')
{
	add_action('init', 'adminDeviceDetection');
}

if(!function_exists('add_architect_admin_options_page'))
{
	function add_architect_admin_options_page()
	{
		add_options_page('WordPress Mobile Administration Settings', 'Mobile Admin', 8, basename(__FILE__), 'architect_admin_options_page');
	}
}

add_action('admin_footer', 'architect_admin_web_footer');

?>
