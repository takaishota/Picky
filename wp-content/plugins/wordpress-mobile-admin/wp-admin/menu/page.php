<?php
$string = '
<wordsChunk id="subMenu">
	<quick_text>
		[url=edit.php?post_type=page]';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit.php')
{
	$string .= '[color=#333333]';
}
$string .= __('Edit');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit.php')
{
	$string .= '[/color]';
}
$string .= '[/url]|[url=post-new.php?post_type=page]';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php')
{
	$string .= '[color=#333333]';
}
$string .= _x('Add New', 'page');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'post-new.php')
{
	$string .= '[/color]';
}

$string .= '[/url] 
	</quick_text>
</wordsChunk>';

return $string;
?>