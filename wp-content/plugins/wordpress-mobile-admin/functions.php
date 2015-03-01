<?php

// Set debug level
if(ARCHITECT_ADMIN_DEBUG)
{
	error_reporting(E_ALL);
	ini_set('display_errors', true);
}

/**
 * WordPress Mobile Admin Control Panel
 * 
 * @access public
 * @return void
 */
if(!function_exists('architect_admin_options_page'))
{
	function architect_admin_options_page()
	{
		if (isset($_POST['info_update'])) 
		{
			$updateOption = false;
			
			// Save Dev Key
			if(architect_admin_save_option('architect_admin_devkey')) $updateOption = true;
			// Save "posted from.."
			if(architect_admin_save_option('architect_admin_show_postedfrom')) $updateOption = true;
			// Save "posted from.." text
			if(architect_admin_save_option('architect_admin_postedfrom')) $updateOption = true;
		}
		
		if(isset($updateOption) && $updateOption == true)
		{
			echo "<div class='updated fade'><p><strong>Settings saved</strong></p></div>";
		}
			
		echo '<div class="wrap">';
		
		if(get_option('architect_admin_devkey') == '' OR !get_option('architect_admin_devkey'))
		{
			echo '<div class="updated" style="background:#F24318;color:#000;border-color:#B23112;"><p><strong>You haven\'t saved your free Wapple Architect Dev Key, get one today from <a href="http://wapple.net/signup/wordpress?trk=wpma" style="color:#000;">Wapple</a></strong>. Once you have it, enter it below</p></div>';
		}
		if(!function_exists('simplexml_load_string'))
		{
			echo '<div class="updated" style="background:#F24318;color:#000;border-color:#B23112;"><p>You do not have simpleXML installed, this plugin needs simpleXML in order to parse XML returned from Wapple\'s web services</p></div>';
		}

		echo '<form method="post" action="options-general.php?page=wordpress-mobile-admin.php" enctype="multipart/form-data">';
		echo architect_admin_header_mobile('2', 'WordPress Mobile Administration Settings');	
		
		echo architect_table_start();
		// Dev Key
		echo architect_admin_option('input', array('label' => 'Wapple Architect Dev Key', 'name' => 'architect_admin_devkey', 'value' => get_option('architect_admin_devkey'), 'description' => '<br />Enter your newly acquired Dev Key from Wapple (You can use the same one for the Wapple Architect Mobile Plugin)'));
		
		// Add a "posted from.." text
		echo architect_admin_option('select', array('label' => 'Show "Posted from Mobile"', 'name' => 'architect_admin_show_postedfrom', 'options' => array('1' => 'Yes', '0' => 'No'), 'value' => get_option('architect_admin_show_postedfrom'), 'description' => '<br />Add text at the bottom of your post to say "Posted from Mobile"'));
	
		// "Posted from.." text
		echo architect_admin_option('input', array('label' => '"Posted from Mobile" text', 'name' => 'architect_admin_postedfrom', 'value' => stripslashes(htmlspecialchars(get_option('architect_admin_postedfrom'))), 'description' => '<br />Defined the "Posted from Mobile" text you want added at the bottom of a post. Use {manufacturer} for handset manufacturer and {model} for handset model'));
		
		echo '</table>';
		
		$twitterLink = 'http://twitter.com/wapplemobileweb';
		echo '
		<br />
		<div style="width:45%;float:left;border:solid 1px #e3e3e3;height:250px;padding:0 10px 15px 10px;margin-right:10px;">
			<p>We\'re on twitter, so don\'t forget to <a href="'.$twitterLink.'">follow us</a> on there!</p>
			<a title="Follow us on Twitter" href="'.$twitterLink.'">
				<img style="margin:0 25px;" src="'.ARCHITECT_ADMIN_URL.'img/follow.png" alt="Follow Us on Twitter" />
			</a>
		</div>
		<div style="width:45%;float:left;border:solid 1px #e3e3e3;height:250px;padding:0 10px 15px 10px;margin-right:10px;">
			<h3>Wapple Architect Mobile Plugin</h3>
			<a href="plugin-install.php?tab=search&type=term&s=wapple"><img style="float:right;margin:0 0 10px 10px;" src="'.ARCHITECT_ADMIN_URL.'img/WAMP.png" alt="Wapple Architect Mobile Plugin" title="Wapple Architect Mobile Plugin" /></a>
			
			<p>Did you know that you can use your Wapple dev key with another great plugin?</p>
			<p>The Wapple Architect Mobile Plugin for WordPress mobilizes your blog so your visitors can read your posts whilst they are on their mobile phone!</p>
			<p>Head over to <a href="http://wordpress.org/extend/plugins/wapple-architect/">http://wordpress.org/extend/plugins/wapple-architect/</a> and install it now
			or jump straight to the <a href="plugin-install.php?tab=search&type=term&s=wapple">Plugin Install Page</a></p>
		</div>';
		echo '<table class="form-table" cellspacing="2" cellpadding="5" width="100%"><tr><td><p class="submit"><input class="button-primary" type="submit" name="info_update" value="Save Changes" /></p></td></tr></table>';
		echo '</form></div>';
	}
}

/**
 * Do device detection
 * 
 * @access public
 * @param array $options
 * @return void
 */
if(!function_exists('adminDeviceDetection'))
{
	function adminDeviceDetection($options = array())
	{
		if(isset($_REQUEST['architectBypass']) AND $_REQUEST['architectBypass'] == true)
		{
			return false;
		}
		
		if(get_bloginfo('version') < '2.6')
		{
			return false;
		}
		
		// Setup a global SOAP client so we can use the same one in the theme
		global $adminWaplSoapClient;
		// Setup global headers so we don't have to get them twice
		global $adminWaplHeaders;
		
		// Which communication method do we want to use?
		if(function_exists('curl_init'))
		{
			// Communicating via REST
			define('ARCHITECT_ADMIN_DO_SOAP', false);
			define('ARCHITECT_ADMIN_DO_REST', true);
		} else if(class_exists('SoapClient'))
		{
			// Check if the SOAP client is up
			if(ini_get('allow_url_fopen') AND !@file_get_contents('http://webservices.wapple.net/info.txt'))
			{
				define('ARCHITECT_ADMIN_DO_SOAP', false);
				define('ARCHITECT_ADMIN_DO_REST', false);
				return false;
			} else
			{
				// Communicating via SOAP
				define('ARCHITECT_ADMIN_DO_SOAP', true);
				define('ARCHITECT_ADMIN_DO_REST', false);
			}
		} else
		{
			return false;
		}
		if(!function_exists('simplexml_load_string'))
		{
			return false;
		}
		
		// Create the SOAP client
		if(ARCHITECT_ADMIN_DO_SOAP)
		{
			$adminWaplSoapClient = __getAdminSoapClient();
		} else
		{
			$adminWaplSoapClient = null;
		}
		// Get device / browser header information 
		$adminWaplHeaders = __getAdminHeaders();
	
		// If __testForMobile() comes back true, amend template path
		if(__adminTestForMobile() AND (defined('ARCHITECT_ADMIN_DO_SOAP') OR defined('ARCHITECT_ADMIN_DO_REST')))
		{
			$file = ARCHITECT_ADMIN_DIR.'wp-admin'.DIRECTORY_SEPARATOR.basename($_SERVER['SCRIPT_NAME']);
			
			global $architectFile;
			$architectFile = basename($file);
			
			if(file_exists($file))
			{
				include($file);
				exit(0);
			}
		}
	}
}

/**
 * Get a SOAP client
 * @return mixed
 */
if(!function_exists('__getAdminSoapClient'))
{
	function __getAdminSoapClient()
	{
		if(ARCHITECT_ADMIN_DEBUG)
		{
			return false;
		}
		if(ARCHITECT_ADMIN_DO_SOAP)
		{
			return new SoapClient('http://webservices.wapple.net/wapl.wsdl', array('exceptions' => 0));
		} else
		{
			return false;
		}	
	}
}

/**
 * Build device header array that SOAP can use
 * @return array
 */
if(!function_exists('__getAdminHeaders'))
{
	function __getAdminHeaders()
	{
		$_SERVER['ARCHITECT_SIGNATURE'] = 'WordPressMobileAdmin';
		if(ARCHITECT_ADMIN_DO_SOAP)
		{
			$headers = array();
			foreach($_SERVER as $key => $val)
			{
				$headers[] = array('name' => $key, 'value' => $val);
			}
			return $headers;
		} else
		{
			$headers = '';
			foreach($_SERVER as $key => $val)
			{
				$headers .= $key.':'.$val.'|';
			}
			return $headers;
		}
	}
}

/**
 * Perform a device detection check
 * @return boolean
 */
if(!function_exists('__adminTestForMobile'))
{
	function __adminTestForMobile()
	{
		global $adminWaplSoapClient;
		global $adminWaplHeaders;
		
		if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] == 0)
		{
			setcookie('isMobile', "", time()-3600, '/');
			setcookie('isMobile', "0", time()+3600, '/');
			return false;
		} else if(isset($_REQUEST['mobile']) && $_REQUEST['mobile'] == 1)
		{
			setcookie('isMobile', "", time()-3600, '/');
			setcookie('isMobile', "1", time()+3600, '/');
			return true;
		}
		
		if(!get_option('architect_admin_devkey'))
		{
			return false;
		}
		
		// Check for existence of a cookie
		if(isset($_COOKIE['isMobile']) AND $_COOKIE['isMobile'] == true)
		{
			return true;
		} else if(isset($_COOKIE['isMobile']) AND $_COOKIE['isMobile'] == false)
		{
			return false;
		} else
		{
			if(ARCHITECT_ADMIN_DEBUG)
			{
				return true;
			}
			if(ARCHITECT_ADMIN_DO_SOAP)
			{
				// Test for mobile
				$params = array(
					'devKey' => get_option('architect_admin_devkey'),
					'deviceHeaders' => $adminWaplHeaders
				);
			
				$result = @$adminWaplSoapClient->getMobileDevice($params);
				if(is_soap_fault($result))
				{
					// There is a SOAP error! probably a dev key error
					return false;
				}  else
				{
					$xml = simplexml_load_string($result);
					
					if(isset($xml->mobile_device) AND $xml->mobile_device == 1)
					{
						setcookie('architectMobileInformation', 'manufacturer='.trim($xml->manufacturer).'|model='.trim($xml->model), time()+3600, '/');
												
						// Set a cookie to remember the outcome!
						setcookie('isMobile', true, time()+3600, '/');
						return true;
					} else
					{
						setcookie('isMobile', false, time()+3600, '/');
						return false;
					}
				}
			} else if(ARCHITECT_ADMIN_DO_REST)
			{
				$postfields = array(
					'devKey' => get_option('architect_admin_devkey'),
					'headers' => $adminWaplHeaders
				);
				
				$c = curl_init();
				curl_setopt($c, CURLOPT_URL, 'http://webservices.wapple.net/getMobileDevice.php');
				curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($c, CURLOPT_POST, 1);
				curl_setopt($c, CURLOPT_POSTFIELDS, $postfields);
				
				$result = curl_exec($c);
				$xml = simplexml_load_string($result);
				
				if(isset($xml->mobile_device) AND $xml->mobile_device == 1)
				{
					setcookie('architectMobileInformation', 'manufacturer='.trim($xml->manufacturer).'|model='.trim($xml->model), time()+3600, '/');
					
					setcookie('isMobile', true, time()+3600, '/');
					return true;
				} else
				{
					setcookie('architectMobileInformation', '', time()-3600, '/');
					setcookie('isMobile', "0", time()+3600, '/');
					return false;
				}
			}
		}
	}
}

/**
 * Convert any other chars
 * 
 * @param string $string
 * @access public
 * @return string
 */
if(!function_exists('architectAdminCharsOther'))
{
	function architectAdminCharsOther($string)
	{
		$chars = array(
			'’' => '&#39;', '—' => '&#45;', '«' => '&#171;', '»' => '&#187;', '&laquo;' => '&#171;',
			'&raquo;' => '&#187;', '–' => '&#45;', '&hellip;' => '&#8230;', '&nbsp;' => '&#160;', '&amp;nbsp;' => '&#160;'
		);
		return str_replace(array_keys($chars), array_values($chars), $string);
	}
}

if(!function_exists('esc_url'))
{
	function esc_url( $url, $protocols = null ) {
		return clean_url( $url, $protocols, 'display' );
	}
}

if(!function_exists('architectAdminError'))
{
	function architectAdminError()
	{
		$_SERVER['SCRIPT_NAME'] = 'permissions-error.php';
		adminDeviceDetection();
	}
}

if(!function_exists('architect_admin_save_option'))
{
	function architect_admin_save_option($option, $options = array())
	{
		// Strip tags out
		if(isset($options['stripTags']) && $options['stripTags'] == true)
		{
			$tagAllow = '';
			if(!empty($options['tagAllow']))
			{
				$tagAllow .= '<';
				$tagAllow .= implode('><', $options['tagAllow']);
				$tagAllow .= '>';
			}
			$_POST[$option] = strip_tags($_POST[$option], $tagAllow);
		} 
		
		if($_POST[$option] != get_option($option))
		{
			update_option($option, $_POST[$option]);
			return true;
		}
	}
}

if(!function_exists('architect_admin_header_mobile'))
{
	function architect_admin_header_mobile($h, $value)
	{
		return '<tr><td colspan="2" class="architectHeader"><h'.$h.'><img src="'.WP_PLUGIN_URL.'/'.get_wpma_plugin_base().'/img/architect32.png" alt="Wapple Architect" class="architectHeaderImage" />'.$value.'</h'.$h.'></td></tr>';
	}
}

if(!function_exists('architect_table_start'))
{
	function architect_table_start()
	{
		return '<table class="form-table architectTable" cellspacing="2" cellpadding="5">';
	}
}

if(!function_exists('architect_admin_option'))
{
	function architect_admin_option($type, $options = array())
	{
		$string  = '<tr>';
		if($type != 'text')
		{
			$string .= '<th width="30%" valign="top" class="architectCell">';
			
			if(isset($options['name']))
			{
				$string .= '<label for="'.$options['name'].'">'.$options['label'].': </label>';
			}
			$string .= '</th>';
			$string .= '<td>';
		}  else
		{
			$string .= '<td colspan="2">';
		}
		
		switch($type)
		{
			case 'input' : 
				if(!isset($options['size']))
				{
					$options['size'] = 40;
				}
				if(isset($options['before']) AND $options['before'] != '')
				{
					$string .= $options['before'];
				}
				$string .= '<input';
				
				if($options['size'] == 40)
				{
					$string .= ' class="regular-text architectInput"';
				}
				$string .= ' size="'.$options['size'].'" type="text" name="'.$options['name'].'" id="'.$options['name'].'" value="'.$options['value'].'" />';
				
				if(isset($options['after']) AND $options['after'] != '')
				{
					$string .= $options['after'];
				}
				break;
			case 'select' :
				$string .= '<select name="'.$options['name'].'" class="architectSelect';
				
				if(isset($options['multiple']) && $options['multiple'] == true)
				{
					$string .= ' architectMultiple" multiple size="5"';
				} else
				{
					$string .= '"';
				}
				
				$string .= '>';
				if(isset($options['multiple']) && $options['multiple'] == true)
				{
					$values = explode('|', $options['value']);
					
					foreach($values as $key => $val)
					{
						$values[$key] = stripslashes(architectCharsOther($val));
					}

					foreach($options['options'] as $val)
					{
						$val = stripslashes($val);
						$string .= '<option value="'.$val.'"';
						
						if(in_array(architectCharsOther($val), $values))
						{
							$string .= ' selected="selected"';
						}
						$string .= '>'.$val.'</option>';
					}
				} else
				{
					foreach($options['options'] as $key => $val)
					{
						$string .= '<option value="'.$key.'"';
					
						if($key == $options['value'])
						{
							$string .= ' selected="selected"';
						}
						
						$string .= '>'.$val.'</option>';
					}
				}
				$string .= '</select>';
				break;
			case 'file' :
				$string .= '<input type="file" name="'.$options['name'].'" id="'.$options['name'].'" class="architectInput" />';
				break;
			case 'image' :
				$string .= '<img src="'.$options['src'].'" alt="'.$options['alt'].'" class="architectImage" />';
				break;
			case 'textarea' :
				if(!isset($options['rows']))
				{
					$options['rows'] = 25;
				}
				if(!isset($options['cols']))
				{
					$options['cols'] = 70;
				}
				$string .= '<textarea class="architectTextarea" id="'.$options['name'].'" name="'.$options['name'].'" rows="'.$options['rows'].'" cols="'.$options['cols'].'">'.$options['value'].'</textarea>';
				break;
			case 'text' :
				$string .= '<p>';
				if(isset($options['bold'])) $string .= '<strong>';
				if(isset($options['italic'])) $string .= '<em>';
				
				$string .= $options['value'];
				
				if(isset($options['italic'])) $string .= '</em>';
				if(isset($options['bold'])) $string .= '</strong>';
				$string .= '</p>';
				break;
			
		}
		
		if(isset($options['description']) && $type != 'text')
		{
			$string .= '<span class="description">'.$options['description'].'</span>';
		}
		
		$string .= '</td></tr>';
		return $string;
	}
}

if(!function_exists('wp_logout_url'))
{
	function wp_logout_url($redirect = '') {
		$args = array( 'action' => 'logout' );
		if ( !empty($redirect) ) {
			$args['redirect_to'] = $redirect;
		}
	
		$logout_url = add_query_arg($args, site_url('wp-login.php', 'login'));
		$logout_url = wp_nonce_url( $logout_url, 'log-out' );
	
		return apply_filters('logout_url', $logout_url, $redirect);
	}
}

if(!function_exists('_n'))
{
	function _n($single, $plural, $number, $domain = 'default') {
		$translations = &get_translations_for_domain( $domain );
		$translation = $translations->translate_plural( $single, $plural, $number );
		return apply_filters( 'ngettext', $translation, $single, $plural, $number, $domain );
	}
}

if(!function_exists('get_translations_for_domain'))
{
	function &get_translations_for_domain( $domain ) {
		global $l10n;
		$empty = &new Translations;
		if ( isset($l10n[$domain]) )
			return $l10n[$domain];
		else
			return $empty;
	}
}

if(!class_exists('Translations'))
{
	class Translations {
		var $entries = array();
		var $headers = array();
	
		/**
		 * Add entry to the PO structure
		 *
		 * @param object &$entry
		 * @return bool true on success, false if the entry doesn't have a key
		 */
		function add_entry($entry) {
			if (is_array($entry)) {
				$entry = new Translation_Entry($entry);
			}
			$key = $entry->key();
			if (false === $key) return false;
			$this->entries[$key] = $entry;
			return true;
		}
	
		/**
		 * Sets $header PO header to $value
		 *
		 * If the header already exists, it will be overwritten
		 *
		 * TODO: this should be out of this class, it is gettext specific
		 *
		 * @param string $header header name, without trailing :
		 * @param string $value header value, without trailing \n
		 */
		function set_header($header, $value) {
			$this->headers[$header] = $value;
		}
	
		function set_headers(&$headers) {
			foreach($headers as $header => $value) {
				$this->set_header($header, $value);
			}
		}
	
		function get_header($header) {
			return isset($this->headers[$header])? $this->headers[$header] : false;
		}
	
		function translate_entry(&$entry) {
			$key = $entry->key();
			return isset($this->entries[$key])? $this->entries[$key] : false;
		}
	
		function translate($singular, $context=null) {
			$entry = new Translation_Entry(array('singular' => $singular, 'context' => $context));
			$translated = $this->translate_entry($entry);
			return ($translated && !empty($translated->translations))? $translated->translations[0] : $singular;
		}
	
		/**
		 * Given the number of items, returns the 0-based index of the plural form to use
		 *
		 * Here, in the base Translations class, the commong logic for English is implmented:
		 * 	0 if there is one element, 1 otherwise
		 *
		 * This function should be overrided by the sub-classes. For example MO/PO can derive the logic
		 * from their headers.
		 *
		 * @param integer $count number of items
		 */
		function select_plural_form($count) {
			return 1 == $count? 0 : 1;
		}
	
		function get_plural_forms_count() {
			return 2;
		}
	
		function translate_plural($singular, $plural, $count, $context = null) {
			$entry = new Translation_Entry(array('singular' => $singular, 'plural' => $plural, 'context' => $context));
			$translated = $this->translate_entry($entry);
			$index = $this->select_plural_form($count);
			$total_plural_forms = $this->get_plural_forms_count();
			if ($translated && 0 <= $index && $index < $total_plural_forms &&
					is_array($translated->translations) &&
					isset($translated->translations[$index]))
				return $translated->translations[$index];
			else
				return 1 == $count? $singular : $plural;
		}
	
		/**
		 * Merge $other in the current object.
		 *
		 * @param Object &$other Another Translation object, whose translations will be merged in this one
		 * @return void
		 **/
		function merge_with(&$other) {
			$this->entries = array_merge($this->entries, $other->entries);
		}
	}
}

if(!class_exists('Translation_Entry'))
{
	class Translation_Entry {
	
		/**
		 * Whether the entry contains a string and its plural form, default is false
		 *
		 * @var boolean
		 */
		var $is_plural = false;
	
		var $context = null;
		var $singular = null;
		var $plural = null;
		var $translations = array();
		var $translator_comments = '';
		var $extracted_comments = '';
		var $references = array();
		var $flags = array();
	
		/**
		 * @param array $args associative array, support following keys:
		 * 	- singular (string) -- the string to translate, if omitted and empty entry will be created
		 * 	- plural (string) -- the plural form of the string, setting this will set {@link $is_plural} to true
		 * 	- translations (array) -- translations of the string and possibly -- its plural forms
		 * 	- context (string) -- a string differentiating two equal strings used in different contexts
		 * 	- translator_comments (string) -- comments left by translators
		 * 	- extracted_comments (string) -- comments left by developers
		 * 	- references (array) -- places in the code this strings is used, in relative_to_root_path/file.php:linenum form
		 * 	- flags (array) -- flags like php-format
		 */
		function Translation_Entry($args=array()) {
			// if no singular -- empty object
			if (!isset($args['singular'])) {
				return;
			}
			// get member variable values from args hash
			$object_varnames = array_keys(get_object_vars($this));
			foreach ($args as $varname => $value) {
				$this->$varname = $value;
			}
			if (isset($args['plural'])) $this->is_plural = true;
			if (!is_array($this->translations)) $this->translations = array();
			if (!is_array($this->references)) $this->references = array();
			if (!is_array($this->flags)) $this->flags = array();
		}
	
		/**
		 * Generates a unique key for this entry
		 *
		 * @return string|bool the key or false if the entry is empty
		 */
		function key() {
			if (is_null($this->singular)) return false;
			// prepend context and EOT, like in MO files
			return is_null($this->context)? $this->singular : $this->context.chr(4).$this->singular;
		}
	}
}

if(!function_exists('simplexml_load_string'))
{
	function simplexml_load_string($string)
	{
		require_once('wp-admin'.DIRECTORY_SEPARATOR.'simplexml.class.php');
		$sx = new simplexml;
		return $sx->xml_load_string($string);
	}
}

if(!function_exists('architect_admin_web_footer'))
{
	function architect_admin_web_footer()
	{
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$color = get_user_option('admin_color', $user_id);
		
		switch($color)
		{
			case 'classic':
				echo '<div id="switchToDesktop" style="background:#1D507D;padding:5px 5px 5px 15px;color:#B6D1E4;font-style:italic;font-size:0.9em;font-family:Georgia,\'Times New Roman\',\'Bitstream Charter\',Times,serif;">View in: <a href="index.php?mobile=1" style="color:#ffffff;text-decoration:none;">Mobile</a> | Standard</div>';
				break;
			case 'fresh':
			default:
				if(get_bloginfo('version') >= '3.0')
				{
					echo '<div id="switchToDesktop" style="background:#d7d7d7;padding:5px 5px 5px 15px;color:#777777;font-style:italic;font-size:0.9em;font-family:Georgia,\'Times New Roman\',\'Bitstream Charter\',Times,serif;">View in: <a href="index.php?mobile=1" style="color:#222222;text-decoration:none;">Mobile</a> | Standard</div>';
				} else
				{
					echo '<div id="switchToDesktop" style="background:#464646;padding:5px 5px 5px 15px;color:#999999;font-style:italic;font-size:0.9em;font-family:Georgia,\'Times New Roman\',\'Bitstream Charter\',Times,serif;">View in: <a href="index.php?mobile=1" style="color:#CCCCCC;text-decoration:none;">Mobile</a> | Standard</div>';
				}
				
				break;
		}
	}
}

if(!function_exists('_wp_get_comment_list'))
{
	function _wp_get_comment_list( $status = '', $s = false, $start, $num, $post = 0, $type = '' )
	{
		global $wpdb;
	
		$start = abs( (int) $start );
		$num = (int) $num;
		$post = (int) $post;
		$count = wp_count_comments();
		$index = '';
	
		if ( 'moderated' == $status ) {
			$approved = "c.comment_approved = '0'";
			$total = $count->moderated;
		} elseif ( 'approved' == $status ) {
			$approved = "c.comment_approved = '1'";
			$total = $count->approved;
		} elseif ( 'spam' == $status ) {
			$approved = "c.comment_approved = 'spam'";
			$total = $count->spam;
		} elseif ( 'trash' == $status ) {
			$approved = "c.comment_approved = 'trash'";
			$total = $count->trash;
		} else {
			$approved = "( c.comment_approved = '0' OR c.comment_approved = '1' )";
			$total = $count->moderated + $count->approved;
			$index = 'USE INDEX (c.comment_date_gmt)';
		}
	
		if ( $post ) {
			$total = '';
			$post = " AND c.comment_post_ID = '$post'";
		} else {
			$post = '';
		}
	
		$orderby = "ORDER BY c.comment_date_gmt DESC LIMIT $start, $num";
	
		if ( 'comment' == $type )
			$typesql = "AND c.comment_type = ''";
		elseif ( 'pings' == $type )
			$typesql = "AND ( c.comment_type = 'pingback' OR c.comment_type = 'trackback' )";
		elseif ( 'all' == $type )
			$typesql = '';
		elseif ( !empty($type) )
			$typesql = $wpdb->prepare("AND c.comment_type = %s", $type);
		else
			$typesql = '';
	
		if ( !empty($type) )
			$total = '';
	
		$query = "FROM $wpdb->comments c LEFT JOIN $wpdb->posts p ON c.comment_post_ID = p.ID WHERE p.post_status != 'trash' ";
		if ( $s ) {
			$total = '';
			$s = $wpdb->escape($s);
			$query .= "AND
				(c.comment_author LIKE '%$s%' OR
				c.comment_author_email LIKE '%$s%' OR
				c.comment_author_url LIKE ('%$s%') OR
				c.comment_author_IP LIKE ('%$s%') OR
				c.comment_content LIKE ('%$s%') ) AND
				$approved
				$typesql";
		} else {
			$query .= "AND $approved $post $typesql";
		}
	
		$comments = $wpdb->get_results("SELECT * $query $orderby");
		if ( '' === $total )
			$total = $wpdb->get_var("SELECT COUNT(c.comment_ID) $query");
	
		update_comment_cache($comments);
	
		return array($comments, $total);
	}
}
?>