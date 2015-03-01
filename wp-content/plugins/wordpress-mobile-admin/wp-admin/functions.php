<?php
/**
 * @package Wapple_Architect
 * @subpackage WAPL Theme
 */

/**
 * Debug function
 * @param $debug mixed
 * @access public
 * @return void
 */
if(!function_exists('debug'))
{
	function debug($debug)
	{
		echo '<pre>'.print_r($debug,true).'</pre>';
	}
}

/**
 * Process WAPL into meaningful markup
 * @param $waplString string
 * @access public
 * @return void
 */
if(!function_exists('process_admin_wapl'))
{
	function process_admin_wapl($waplString)
	{
		if(ARCHITECT_ADMIN_DEBUG)
		{
			header('Content-type: application/xml');
			echo $waplString;
			die();
		}
		
		if(ARCHITECT_ADMIN_DO_SOAP)
		{
			global $adminWaplHeaders;
			global $adminWaplSoapClient;
			
			$params = array(
				'devKey' => get_option('architect_admin_devkey'), 
				'wapl' => $waplString, 
				'deviceHeaders' => $adminWaplHeaders
			);
			
			// Send markup to API and parse through simplexml
			$result = @$adminWaplSoapClient->getMarkupFromWapl($params);
			
			
			if(is_soap_fault($result))
			{
				if(strpos($result->faultstring, 'Account API request limit reached') === 0)
				{
					setcookie('architectError', $result->faultstring, time()+1800, '/');
				}
				setcookie('isMobile', "0", time()+1800, '/');
				header('Location:'.get_permalink());
				exit();
			} else
			{
				setcookie('architectError', "", time()-3600, '/');
			}
			
			$xml = simplexml_load_string($result);
			
			foreach($xml->header->item as $val)
			{
				header($val);
			}
			
			// Flush output buffer - to clean up any other plugin mess!
			ob_end_clean();
			
			$markup = trim($xml->markup);
			$markup = str_replace('""http://www.wapforum.org', '" "http://www.wapforum.org', $markup);
			
			// Echo correct markup
			echo trim($markup);
			die();
		} else if(ARCHITECT_ADMIN_DO_REST)
		{
			global $adminWaplHeaders;

			$postfields = array(
				'devKey' => get_option('architect_admin_devkey'),
				'wapl' => $waplString,
				'headers' => $adminWaplHeaders
			);
			
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, 'http://webservices.wapple.net/getMarkupFromWapl.php');
			curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($c, CURLOPT_POST, 1);
			curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
			
			$result = curl_exec($c);
			if(strpos($result, 'Account API request limit reached')!== false)
			{
				setcookie('architectError', 'Account API request limit reached', time()+1800, '/');
				setcookie('isMobile', "0", time()+1800, '/');
				header('Location:'.get_permalink());
				exit();
			} else if(strpos($result, 'Developer key authentication error') !== false)
			{
				setcookie('architectError', 'Developer key authentication error', time()+1800, '/');
				setcookie('isMobile', "0", time()+1800, '/');
				header('Location:'.get_permalink());
				exit();
			} else
			{
				setcookie('architectError', "", time()-3600, '/');
			}
			curl_close($c);
			
			$xml = simplexml_load_string($result);
			foreach($xml->header->item as $val)
			{
				header($val);
			}
			
			// Flush output buffer - to clean up any other plugin mess!
			ob_end_clean();
			
			$markup = trim($xml->markup);
			$markup = str_replace('""http://www.wapforum.org', '" "http://www.wapforum.org', $markup);
			
			// Echo correct markup
			echo trim($xml->markup);
			die();
			
		} else
		{
			header('Content-type: application/xml');
			echo $waplString;
		}
	}
}

require_once('wapl_builder.php');
/**
 * WordPress WAPL parser
 * 
 * Extends the waplBuilder class to build perfect markup
 * @author Rich Gubby
 */
class WordPressAdminWapl extends waplAdminBuilder
{
/**
	 * Format content into WAPL readable format
	 * @param string $content
	 * @param integer $imagescale
	 * @param integer $imagequality
	 * @param string $class
	 * @param integer $length
	 * @param string $transcol
	 * @access public
	 * @return string
	 */
	function format_text($content, $imagescale = 95, $imagequality = 90, $class = 'entry', $length = null, $transcol = '')
	{
		// Remove comments
		$content = str_replace(
			array('&#8211;', '<strong><strong>', '&copy;', '&nbsp;'), 
			array('--', '<strong>', '&#169;', '&#160;'), 
			$content
		);

		$content = $this->foreignChars($content);

		// Ampersand cleanup
		$content = preg_replace('/(?!&#)&/', '&amp;', $content);
		preg_match_all('/&#[0-9]{1,}(;)?/', $content, $matches, PREG_OFFSET_CAPTURE);
		foreach($matches[0] as $key => $val)
		{
			if(substr($val[0], -1) != ';')
			{
				$content = substr_replace($content, str_replace('&#', '&amp;#', $val[0]), $val[1], strlen($val[0]));
			}
		}
		
		$replacements = array();
		require_once('simple_html_dom.php');
		
		$html = wpma_str_get_html($content);
		
		// Remove comments
		foreach($html->find('comment') as $element)
		{
			$content = str_ireplace($element->outertext, '', $content);
		}
		
		// Remove script tags
		foreach($html->find('script') as $element)
		{
			$replacements[trim($element->outertext())] = '';
		}
		
		// Replace [caption] with <caption>
		$content = preg_replace('/(\[caption(.*?)\])(.*?)(\[\/caption\])/i', '<caption ${2}>${3}</caption>', $content);
		
		foreach($replacements as $key => $val)
		{
			$content = str_ireplace(trim($key), $val, $content);
		}
		
		// Use parent format text
		$content = parent::format_text($content, $imagescale, $imagequality, $class, $length, $transcol);
		
		return utf8_encode(str_replace('& ', '&amp; ', $content));
	}
	
	/**
	 * Clean up ampersands in text
	 * 
	 * @param string $content
	 * @access public
	 * @return string
	 */
	function ampersand_cleanup($content)
	{
		$content = preg_replace('/(?!&#)&/', '&amp;', $content);
		preg_match_all('/&#[0-9]{1,}(;)?/', $content, $matches, PREG_OFFSET_CAPTURE);
		foreach($matches[0] as $key => $val)
		{
			if(substr($val[0], -1) != ';')
			{
				$content = substr_replace($content, str_replace('&#', '&amp;#', $val[0]), $val[1], strlen($val[0]));
			}
		}
		return $content;
	}
	
	/**
	 * Check if a string is UTF8 or not
	 * 
	 * @param string $string
	 * @access public
	 * @return string
	 */
	function is_utf8($string) 
	{
	    return (preg_match('/^([\x00-\x7f]|[\xc0-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xf7][\x80-\xbf]{3}|[\xf8-\xfb][\x80-\xbf]{4}|[\xfc-\xfd][\x80-\xbf]{5})*$/', $string) === 1);
	}
	
	/**
	 * Convert foreign characters to ascii alternatives
	 * 
	 * @param string $string
	 * @access public
	 * @return string
	 */
	function foreignChars($string)
	{
		// Add support for Japanese, Chinese, Korean, etc characters
		if(function_exists('mb_detect_encoding'))
		{
			$newString = '';
			for ($i=0; $i < mb_strlen($string, 'UTF-8'); $i++)
			{
				$ch = mb_substr($string, $i, 1, 'UTF-8');
				
				if($ch && trim($ch) != '')
				{
					$returnVal = $this->uniord($ch);
					if($returnVal['encode'] == true)
					{
						$newString .= '&#'.$returnVal['ud'].';';
					} else if(!$returnVal['die'])
					{
						$newString .= mb_substr($string, $i, 1, 'UTF-8');
					}
				} else
				{
					$newString .= mb_substr($string, $i, 1, 'UTF-8');
				}
			}
			
			$string = $newString;

			require_once('language.php');
			$chars = architectAdminGetTranslation('chars');
			
			// Convert any other characters
			$string = str_replace(
				array_keys($chars), 
				array_values($chars), 
			$string);
		} else
		{
			// Check for dodgy chars
			for($i = 0; $i < strlen($string); $i++)
			{
				if(ord($string[$i]) == 26)
				{
					$string[$i] = '';
				}
			}

			require_once('language.php');
			$chars = architectAdminGetTranslation('chars');
			$charsOriginal = architectAdminGetTranslation('charsOriginal');
			
			// Convert characters manually
			$string = str_replace(
				array_keys(array_merge($chars, $charsOriginal)), 
				array_values(array_merge($chars, $charsOriginal)), 
				$string
			);
		}
		
		// Any other characters
		$string = architectAdminCharsOther($string);

		return utf8_decode($string);
	}
	
	function uniord($c)
	{
		$ud = 0;
		$encode = true;
		$die = false;
		if (ord($c{0})>=0 && ord($c{0})<=127)
		{
			if(ord($c{0}) == 26)
			{
				$die = true;
			}
			$encode = false;
			$ud = $c{0};
		}
		if (ord($c{0})>=192 && ord($c{0})<=223)
		{
			$ud = (ord($c{0})-192)*64 + (ord($c{1})-128);
		}
		if (ord($c{0})>=224 && ord($c{0})<=239)
		{
			$ud = (ord($c{0})-224)*4096 + (ord($c{1})-128)*64 + (ord($c{2})-128);
		}
		if (ord($c{0})>=240 && ord($c{0})<=247)
		{
			$ud = (ord($c{0})-240)*262144 + (ord($c{1})-128)*4096 + (ord($c{2})-128)*64 + (ord($c{3})-128);
		}
		if (ord($c{0})>=248 && ord($c{0})<=251)
		{
			$ud = (ord($c{0})-248)*16777216 + (ord($c{1})-128)*262144 + (ord($c{2})-128)*4096 + (ord($c{3})-128)*64 + (ord($c{4})-128);
		}
		if (ord($c{0})>=252 && ord($c{0})<=253)
		{
			$ud = (ord($c{0})-252)*1073741824 + (ord($c{1})-128)*16777216 + (ord($c{2})-128)*262144 + (ord($c{3})-128)*4096 + (ord($c{4})-128)*64 + (ord($c{5})-128);
		}
		if (ord($c{0})>=254 && ord($c{0})<=255)
		{
			//error
			$ud = false;
		}
		return array('ud' => $ud, 'encode' => $encode, 'die' => $die);
	}
}

if(!function_exists('architect_post_rows'))
{
	function architect_post_rows()
	{
		global $wp_query, $post, $mode;
	
		add_filter('the_title','esc_html');
	
		// Create array of post IDs.
		$post_ids = array();
	
		if ( empty($posts) )
			$posts = &$wp_query->posts;
	
		return $posts;
	}
}

if(!function_exists('architect_return_posts_per_page'))
{
	function architect_return_posts_per_page()
	{
		return 10;
	}
}

if(!function_exists('architect_mobile_info'))
{
	function architect_mobile_info()
	{
		if(isset($_COOKIE['architectMobileInformation']))
		{
			$return = array();

			foreach(explode('|', $_COOKIE['architectMobileInformation']) as $infoVal)
			{
				list($key,$val) = explode('=', $infoVal);
				$return[$key] = $val;
			}
			return $return;
		} else
		{
			return false;
		}
	}
}

if(!function_exists('architectAdminSplit'))
{
	function architectAdminSplit($string)
	{
		return str_replace(
			array('http://'),
			array(' http://'),
			$string
		); 
	}
}
?>