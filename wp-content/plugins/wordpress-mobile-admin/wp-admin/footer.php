<?php
$string = '<spacemakerChunk><scale>2</scale></spacemakerChunk>';

$showWappleAd = false;

if($showWappleAd == true)
{
	$string .= '
		<admobChunk class="wappleAd">
			<mobile_site_id>a144c8e289ebcca</mobile_site_id>
		</admobChunk>';
}

$fp = fopen(WP_PLUGIN_DIR.DIRECTORY_SEPARATOR.get_wpma_plugin_base().DIRECTORY_SEPARATOR.'wordpress-mobile-admin.php', 'r');
// Pull only the first 8kiB of the file in.
$plugin_data = fread($fp, 400);
// PHP will close file handle, but we are good citizens.
fclose($fp);
preg_match('|Version:(.*)|i', $plugin_data, $version);

if(!isset($version[1]))
{
	$version[1] = 'Unknown';
}

$string .= '
		<spacemakerChunk><scale>2</scale></spacemakerChunk>';

if(isset($showVersion) AND $showVersion == true)
{
	$string .= '<row id="version"><cell><chars><value><![CDATA[<!-- WordPress Mobile Admin Version: '.trim($version[1]).' -->]]></value></chars></cell></row>';
}

$string .= '
		<wordsChunk id="footer">
			<quick_text>'.__('Mobilized with').' [url=http://wapple.net]Wapple Architect[/url]</quick_text>
		</wordsChunk>
';

// Switch to desktop if we're an admin user
if(!isset($showVersion) OR $showVersion == false)
{
	global $architectFile;
	$string .= '
		<wordsChunk id="switchToDesktop">
			<quick_text>'.__('Switch to Mobile').' | [url='.$architectFile.'?mobile=0]'.__('Desktop').'[/url]</quick_text>
		</wordsChunk>';
}

$string .= '</layout></wapl>';

return $string;
?>