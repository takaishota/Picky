<?php
if ( ! defined('ABSPATH') ) die();
// Admin bootstaps
require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'functions.php');
require_once(ABSPATH.'wp-includes'.DIRECTORY_SEPARATOR.'pluggable.php');

remove_all_filters('login_head');
remove_all_filters('wp_authenticate');

if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'logout')
{
	wp_clear_auth_cookie();
	wp_logout();
}

// Process login (copied from /wp-login.php)
if(isset($_POST['wp-login']) && $_POST['wp-login'] == 1)
{
	$secure_cookie = '';

	// If the user wants ssl but the session is not ssl, force a secure cookie.
	if ( !empty($_POST['log']) && !force_ssl_admin() ) {
		$user_name = sanitize_user($_POST['log']);
		if ( $user = get_userdatabylogin($user_name) ) {
			if ( get_user_option('use_ssl', $user->ID) ) {
				$secure_cookie = true;
				force_ssl_admin(true);
			}
		}
	}

	if ( isset( $_REQUEST['redirect_to'] ) ) {
		$redirect_to = $_REQUEST['redirect_to'];
		// Redirect to https if user wants ssl
		if ( $secure_cookie && false !== strpos($redirect_to, 'wp-admin') )
			$redirect_to = preg_replace('|^http://|', 'https://', $redirect_to);
	} else {
		$redirect_to = admin_url();
	}


	if ( !$secure_cookie && is_ssl() && force_ssl_login() && !force_ssl_admin() && ( 0 !== strpos($redirect_to, 'https') ) && ( 0 === strpos($redirect_to, 'http') ) )
		$secure_cookie = false;

	$user = wp_signon('', $secure_cookie);

	$redirect_to = apply_filters('login_redirect', $redirect_to, isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : '', $user);

	if ( !is_wp_error($user) ) {
		// If the user can't edit posts, send them to their profile.
		if ( !$user->has_cap('edit_posts') && ( empty( $redirect_to ) || $redirect_to == 'wp-admin/' || $redirect_to == admin_url() ) )
			$redirect_to = admin_url('profile.php');
		wp_safe_redirect($redirect_to);
		exit();
	}

	$errors = $user;
	// Clear errors if loggedout is set.
	if ( !empty($_GET['loggedout']) )
		$errors = new WP_Error();

	// If cookies are disabled we can't log in even with a valid user+pass
	if ( isset($_POST['testcookie']) && empty($_COOKIE[TEST_COOKIE]) )
		$errors->add('test_cookie', __("<strong>ERROR</strong>: Cookies are blocked or not supported by your browser. You must <a href='http://www.google.com/cookies.html'>enable cookies</a> to use WordPress."));

	// Some parts of this script use the main login form to display a message
	if		( isset($_GET['loggedout']) && TRUE == $_GET['loggedout'] )			$errors->add('loggedout', __('You are now logged out.'), 'message');
	elseif	( isset($_GET['registration']) && 'disabled' == $_GET['registration'] )	$errors->add('registerdisabled', __('User registration is currently not allowed.'));
	elseif	( isset($_GET['checkemail']) && 'confirm' == $_GET['checkemail'] )	$errors->add('confirm', __('Check your e-mail for the confirmation link.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'newpass' == $_GET['checkemail'] )	$errors->add('newpass', __('Check your e-mail for your new password.'), 'message');
	elseif	( isset($_GET['checkemail']) && 'registered' == $_GET['checkemail'] )	$errors->add('registered', __('Registration complete. Please check your e-mail.'), 'message');

	login_header(__('Log In'), '', $errors);

	if ( isset($_POST['log']) )
		$user_login = ( 'incorrect_password' == $errors->get_error_code() || 'empty_password' == $errors->get_error_code() ) ? esc_attr(stripslashes($_POST['log'])) : '';
}

$waplString = '';
$pageTitle = __('Log In');
$pageId = 'login';
$showHeader = false;

$wapl = new WordPressAdminWapl;
// Header
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'header.php');
$waplString .= '<row class="" id="header"><cell><externalLink><label>'.$wapl->format_text(sprintf(__('&larr; Back to %s'), get_bloginfo('title', 'display' ))).'</label><url>'.htmlspecialchars(get_option('home')).'</url></externalLink></cell></row>';
$waplString .= '<row class="" id="wordpress"><cell><externalImage filetype="png" scale="75" quality="100"><url>'.ARCHITECT_ADMIN_URL.'img/wordpress-logo.png</url><transcol>F9F9F9</transcol></externalImage></cell></row>';

// Show logged out message
if(isset($_REQUEST['loggedout']) && $_REQUEST['loggedout'] == "true")
{
	$waplString .= '<wordsChunk class="updated"><quick_text>'.__('You are now logged out.').'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';
}
if(isset($errors) AND !empty($errors))
{
	$waplString .= '<wordsChunk id="login_error"><quick_text>'.str_replace(': ', '', __('<strong>ERROR</strong>: Invalid username or e-mail.')).'</quick_text></wordsChunk>';
	$waplString .= '<spacemakerChunk><scale>2</scale></spacemakerChunk>';	
}

// Login form
$waplString .= '<form><action>'. htmlentities(site_url('wp-login.php', 'login_post')).'</action><name>loginform</name><formItem item_type="text"><label>'.__('Username').'</label><name>log</name></formItem><formItem item_type="password"><label>'.__('Password').'</label><name>pwd</name></formItem><formItem item_type="checkbox"><name>rememberme</name><value>0</value><label>'.__('Remember Me').'</label></formItem><formItem item_type="submit"><name>wp-submit</name><label>'.__('Log In').'</label></formItem><formItem item_type="hidden"><name>redirect_to</name><value>'.admin_url().'</value></formItem><formItem item_type="hidden"><name>wp-login</name><value>1</value></formItem></form>';

// Footer
$showVersion = true;
$waplString .= require_once(ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.'footer.php');

// Convert WAPL into markup
process_admin_wapl($waplString);
?>