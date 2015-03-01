<?php
$string = '
<wordsChunk id="subMenu">
	<quick_text>
		[url=edit.php]';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit.php')
{
	$string .= '[color=#333333]';
}
$string .= __('Edit');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit.php')
{
	$string .= '[/color]';
}
$string .= '[/url]|[url=post-new.php]';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php')
{
	$string .= '[color=#333333]';
}
$string .= _x('Add New', 'post');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php')
{
	$string .= '[/color]';
}

$string .= '[/url]|[url=edit-tags.php?taxonomy=post_tag]';
if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit-tags.php' AND $pageId == 'editTags')
{
	$string .= '[color=#333333]';
}
$string .= __('Tags');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit-tags.php' AND $pageId == 'editTags')
{
	$string .= '[/color]';
}

$string .= '[/url]';

if(current_user_can('moderate_comments'))
{
	$string .= '|[url=edit-tags.php?taxonomy=category]';
	if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit-tags.php' AND $pageId == 'editCategories')
	{
		$string .= '[color=#333333]';
	}
	$string .= __('Categories');
	if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit-tags.php' AND $pageId == 'editCategories')
	{
		$string .= '[/color]';
	}
	$string .= '[/url]';
}
$string .= '</quick_text>
</wordsChunk>';

return $string;
?>