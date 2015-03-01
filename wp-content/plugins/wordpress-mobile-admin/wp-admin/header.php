<?php
if(!isset($pageH1)) $pageH1 = $pageTitle;

$blog_name = get_bloginfo('name', 'display');

$string = '<' . '?xml version="1.0" encoding="utf-8" ?'.'>';
$string .= '
<wapl xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="http://wapl.wapple.net/wapl.xsd">
	<head>
		<title>'.$pageTitle.' - '.$blog_name.' - WordPress</title>';

$css = ARCHITECT_ADMIN_URL.'wp-admin/style.php?architectBypass=true';
if(isset($pageId) && $pageId != '')
{
	$css .= '&amp;page='.$pageId;
}

$string .= '<css><url>'.htmlspecialchars($css).'</url></css>
	<rule type="activation" criteria="iphone" condition="1"><javascript><url>'.ARCHITECT_ADMIN_URL.'wp-admin/admin.js?architectBypass=true</url></javascript></rule>
	</head>
	<settings>
		<iphoneUserScaleable>0</iphoneUserScaleable>
		<iphoneMinScale>1</iphoneMinScale>
		<iphoneMaxScale>1</iphoneMaxScale>
	</settings>
	<layout>';

if(!isset($showHeader) || $showHeader == true)
{
	$string .= '<wordsChunk id="header"><quick_text>[url='.get_bloginfo('home').']'.$blog_name.'[/url]</quick_text></wordsChunk>';
}

return $string;
?>