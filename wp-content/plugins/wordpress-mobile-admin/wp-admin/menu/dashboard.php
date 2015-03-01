<?php
$string = '
<wordsChunk id="subMenu">
	<quick_text>
		[url=profile.php]';

if(basename($_SERVER['SCRIPT_FILENAME']) == 'profile.php')
{
	$string .= '[color=#333333]';
}
$string .= __('Your Profile');
if(basename($_SERVER['SCRIPT_FILENAME']) == 'profile.php')
{
	$string .= '[/color]';
}
$string .= '[/url]
	</quick_text>
</wordsChunk>';

return $string;
?>