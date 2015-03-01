<?php
$class = '';
if(isset($subNav) && $subNav == false)
{
	$class = ' class="noSubNav"';
}
$menu = array(
	'Dashboard' => array('url' => 'index.php')
);

// Get current user
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$menuOptions = array(
	'posts' => array('url' => 'edit.php', 'permission' => 'edit_posts'), 
	'pages' => array('url' => 'edit.php?post_type=page', 'permission' => 'edit_pages'),  
	'comments' => array('url' => 'edit-comments.php', 'permission' => 'moderate_comments')
);
foreach($menuOptions as $key => $val)
{
	if(get_option('architect_admin_show_menu_'.$key.'_'.$user_id) OR get_option('architect_admin_show_menu_'.$key.'_'.$user_id) === false)
	{
		$menu[ucwords($key)] = $menuOptions[$key];
	}
}

$links = '';
foreach($menu as $key => $val)
{
	if((isset($val['permission']) AND current_user_can($val['permission'])) OR !isset($val['permission']))
	{
		$links .= '[url='.$val['url'].']';
		
		if((($val['url'] == basename($_SERVER['SCRIPT_FILENAME'])) AND !$pageRedirect) OR (isset($thisMainMenu) AND $thisMainMenu == $key))
		{
			$links .= '[color=#333333]';
		}
		$links .= __($key);
	
		if((($val['url'] == basename($_SERVER['SCRIPT_FILENAME'])) AND !$pageRedirect) OR (isset($thisMainMenu) AND $thisMainMenu == $key))
		{
			$links .= '[/color]';
		}
		
		$links .= '[/url]|';
	}
}
$links .= '[url='.wp_logout_url().']'.__('Log Out').'[/url]';

return '
<wordsChunk id="menu"'.$class.'>
	<quick_text>'.$links.'</quick_text>
</wordsChunk>';
?>