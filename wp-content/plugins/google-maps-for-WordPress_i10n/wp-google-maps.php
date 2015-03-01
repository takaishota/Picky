<?php
/**
 * Plugin Name: Google Maps for WordPress
 * Plugin URI: http://xavisys.com/google-maps-for-wordpress/
 * Description: This plugin allows you to easily insert Google maps into your blog, making use of the new shortCode system in WordPress 2.5.  The maps can be configured to offer directions to or from the location, show or hide the zoom/pan controls, show/hide map type, activate zoom using mouse wheel, and more.  Requires PHP5.
 * Version: 1.0.3
 * Author: Aaron D. Campbell
 * Author URI: http://xavisys.com/
 */

/**
 * Changelog:
 * 01/05/2010: 1.0.3
 * 	-  Register setting for WPMU compatability.
 *
 * 04/25/2008: 1.0.2
 * 	-  Now includes json_encode.php for users that don't have json_encode() which was added in PHP 5.2.0
 *
 * 04/25/2008: 1.0.1
 * 	- Upgraded to work with the altered patch for ticket 6444 applied to 2.5.1
 * 	- Javascript adjusted to give better, more accurate messages regarding API Key valididty
 * 	- No longer load google maps js if there is not API Key specified.
 *
 * 04/21/2008: 1.0.0
 * 	- Version number not incremented, but PHP requirement added to documention.
 *
 * 04/19/2008: 1.0.0
 * 	- Added to wordpress.org repository
 *
 * 04/14/2008: 0.0.2
 * 	- Adjusted to use wp_enqueue_script
 * 	- Fixed problem with js being enqueued on every admin page.  It's now only on editing pages and the config page
 *
 * 03/24/2008: 0.0.1
 * 	- Original Version
 */

/*  Copyright 2006  Aaron D. Campbell  (email : wp_plugins@xavisys.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
/**
 * wpGoogleMaps is the class that handles ALL of the plugin functionality.
 * It helps us avoid name collisions
 * http://codex.wordpress.org/Writing_a_Plugin#Avoiding_Function_Name_Collisions
 */

// <-- 20080528 kny Edit
//define('GGLMAPWP_DIR_PATH', dirname(__FILE__));
//load_plugin_textdomain('GoogleMapsWP','wp-content/plugins/' . basename(GGLMAPWP_DIR_PATH) .'/languages');
// <-- 20080528 kny Edit

// <-- 200110602 kny Edit
define('GGLMAPWP_DIR_PATH', dirname(plugin_basename(__FILE__)));
load_plugin_textdomain('GoogleMapsWP', false, GGLMAPWP_DIR_PATH .'/languages');
// <-- 200110602 kny Edit

class wpGoogleMaps
{
    /**
     * We check if there are any maps on the page, and store this as a bool here
     *
     * @var bool
     */
    private $isMap;

    /**
     * Current map being added to the page.  This gives unique ids to each map,
     * allowing multiple maps per page
     *
     * @var int
     */
    private $mapNum = 0;

    /**
     * Full url to the Google Maps JavaScript file (including API key)
     *
     * @var string
     */
    private $mapApiUrl;

    /**
     * Google Maps API Key
     *
     * @var string
     */
    private $googleKey;

    /**
     * Gets Google API Key, and creates the url to the Google Maps JavaScript
     *
     */
    public function __construct()
    {
        $this->googleKey = get_option('wpGoogleMaps_api_key');
        $this->mapApiUrl = "http://maps.google.com/maps?file=api&amp;v=2&amp;key={$this->googleKey}";

// <-- 20110624 kny Edit
//        $jsDir = get_option('siteurl') . '/wp-content/plugins/google-maps-for-wordpress/js/';
        $jsDir = WP_PLUGIN_URL ."/". GGLMAPWP_DIR_PATH . '/js/';
// <-- 20110624 kny Edit

        wp_register_script('googleMaps', $this->mapApiUrl, false, 2);
        wp_register_script('wpGoogleMaps', "{$jsDir}wp-google-maps.js", array('prototype', 'googleMaps'), '0.0.1');
        wp_register_script('wpGoogleMapsAdmin', "{$jsDir}wp-google-maps-admin.js", array('jquery'), '0.0.2');
    }

    /**
     * Add our plugin configuration menu in the admin section
     * Add boxes to the "edit post" and "edit page" pages
     */
    public function adminMenu()
    {
        add_meta_box('wpGoogleMaps', 'Google Maps for WordPress', array($this, 'insertForm'), 'post', 'normal');
        add_meta_box('wpGoogleMaps', 'Google Maps for WordPress', array($this, 'insertForm'), 'page', 'normal');
        if ( function_exists('add_submenu_page') ) {
            $page = add_submenu_page('plugins.php', __('wpGoogleMaps Configuration','GoogleMapsWP'), __('wpGoogleMaps Configuration','GoogleMapsWP'), 'manage_options', 'wpGoogleMaps-config', array($this, 'configPage'));
            add_action( 'admin_print_scripts-'.$page, array($this, 'printScripts') );
        }
    }
	public function registerOptions() {
		register_setting( 'wpGoogleMaps_options', 'wpGoogleMaps_api_key' );
	}

    /**
     * Create the actual plugin configuration page
     */
    public function configPage()
    {
        $title = __('wpGoogleMaps Configuration','GoogleMapsWP');
        if ($message) { ?>
            <div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php } ?>
            <div class="wrap">
                <h2><?php echo $title; ?></h2>
                <form action="options.php" method="post">
                    <?php wp_nonce_field('update-options'); ?>
                    <p><?php _e('Google Maps for WordPress will allow you to easily add maps to your posts or pages.','GoogleMapsWP')?></p>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row"><?php _e('Google API Key:','GoogleMapsWP') ?></th>
                            <td>
                                <input type="text" size="40" style="width:95%;" name="wpGoogleMaps_api_key" id="wpGoogleMaps_api_key" value="<?php echo get_option('wpGoogleMaps_api_key'); ?>" />
                                <p id="wpGoogleMaps_message"></p>
                            </td>
                        </tr>
                   </table>
                    <p class="submit">
                        <input type="submit" name="Submit" value="<?php _e('Update Options &raquo;','GoogleMapsWP'); ?>" />
                    </p>
                    <input type="hidden" name="action" value="update" />
                    <input type="hidden" name="page_options" value="wpGoogleMaps_api_key" />
                </form>
            </div>
<?php
    }

    /**
     * Used to check for maps, and set $this->isMap
     *
     * @param array $posts
     * @return array of posts (unchanged)
     */
    public function findMaps($posts)
    {
        $content = '';
        foreach ($posts as $post) {
            $content .= $post->post_content;
        }
        $this->isMap = (bool)preg_match("/\[googleMap(.*)\]/U", $content);

        return $posts;
    }

    /**
     * If you need to check if there are maps on the current page, use this
     * $wpGoogleMaps->isMap();
     *
     * @return bool
     */
    public function isMap()
    {
        return $this->isMap;
    }

    /**
     * Links to the Google Maps API JavaScript and the wpGoogleMaps JS, but only
     * IF there are maps on this page.
     *
     * @return void
     */
    public function wpHead ()
    {
        if ($this->isMap) {
        	wp_enqueue_script('wpGoogleMaps');
        }
    }

    /**
     * Links to the Google Maps API JavaScript and the wpGoogleMaps admin JS
     *
     * @return void
     */
    public function adminHead ()
    {
    	if ($GLOBALS['editing']) {
    		$this->printScripts();
    	}
    }

    public function printScripts () {
    	wp_enqueue_script('wpGoogleMapsAdmin');
    	if ( get_option('wpGoogleMaps_api_key') ) {
    		wp_enqueue_script('googleMaps', false, array('wpGoogleMapsAdmin'));
    	}
    }

    /**
     * Given the attributes and content from the googleMap shortCode, this will
     * return an object that has all the settings in it.
     *
     * @param array $attr - attributes from the shortCode
     * @param string $address - content of the shortCode
     * @return stdClass
     */
    private function getMapDetails($attr, $address)
    {
        if (isset($attr['width']) && ctype_digit($attr['width'])) {
            $attr['width'] .= 'px';
        }
        if (isset($attr['height']) && ctype_digit($attr['height'])) {
            $attr['height'] .= 'px';
        }

        $mapInfo = (object)shortcode_atts(array('name'              => '',
                                                'mousewheel'        => 'true',
                                                'zoompancontrol'    => 'true',
                                                'typecontrol'       => 'true',
                                                'directions_to'     => 'true',
                                                'directions_from'   => 'false',
                                                'width'             => '100%',
                                                'height'            => '400px',
                                                'description'       => ''), $attr);

        array_walk($mapInfo, array($this, 'fixTrueFalse'));
        $mapInfo->address = $address;
        return $mapInfo;
    }

    /**
     * Replaces "true" and "false" (strings) with true and false (bool)
     * Used with array_walk
     *
     * @param mixed &$value
     * @param string $key
     */
    private function fixTrueFalse(&$value, $key) {
        if ($value == 'false') {
        	$value = false;
        } elseif ($value == 'true') {
            $value = true;
        }
    }

    /**
     * Echo a warning into the admin section, if no Google API has been entered
     */
    public function warning() {
        echo "<div id='wpGoogleMaps_warning' class='updated fade-ff0000'><p><strong>"
            .__('Google Maps for WordPress is almost ready.','GoogleMapsWP')."</strong> "
            .sprintf(__('You must <a href="%1$s">enter your Google API key</a> for it to work.','GoogleMapsWP'), "plugins.php?page=wpGoogleMaps-config")
            ."</p></div>";
    }

    /**
     * Adds the form to generate a googleMaps shortcode and send it to the
     * editor.  Default values are blank on purpose.  It helps the JavaScript
     * generate the shortest possible shortCode for the map in question.
     *
     * @return void
     */
    public function insertForm() {
?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_name"><?php _e('Location Name:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpGoogleMaps[name]" id="wpGoogleMaps_name" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_address"><?php _e('Address:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpGoogleMaps[address]" id="wpGoogleMaps_address" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_description"><?php _e('Location Description:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="text" size="40" style="width:95%;" name="wpGoogleMaps[description]" id="wpGoogleMaps_description" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_width"><?php _e('Map Width:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="text" size="4" name="wpGoogleMaps[width]" id="wpGoogleMaps_width" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_height"><?php _e('Map Height:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="text" size="4" name="wpGoogleMaps[height]" id="wpGoogleMaps_height" />
                </td>
            </tr>
            <tr valign="top">
                <th scope="row"><label for="wpGoogleMaps_description"><?php _e('Options:','GoogleMapsWP')?></label></th>
                <td>
                    <input type="hidden" name="wpGoogleMaps[mousewheel]" id="wpGoogleMaps_mousewheel_" value="false" />
                    <input type="checkbox" name="wpGoogleMaps[mousewheel]" id="wpGoogleMaps_mousewheel" value="" checked="checked" />
                    <label for="wpGoogleMaps_mousewheel"><?php _e('Enable Mouse Wheel Zoom','GoogleMapsWP')?></label><br />
                    <input type="hidden" name="wpGoogleMaps[zoompancontrol]" id="wpGoogleMaps_zoompancontrol_" value="false" />
                    <input type="checkbox" name="wpGoogleMaps[zoompancontrol]" id="wpGoogleMaps_zoompancontrol" value="" checked="checked" />
                    <label for="wpGoogleMaps_zoompancontrol"><?php _e('Enable Zoom/Pan Controls','GoogleMapsWP')?></label><br />
                    <input type="hidden" name="wpGoogleMaps[typecontrol]" id="wpGoogleMaps_typecontrol_" value="false" />
                    <input type="checkbox" name="wpGoogleMaps[typecontrol]" id="wpGoogleMaps_typecontrol" value="" checked="checked" />
                    <label for="wpGoogleMaps_typecontrol"><?php _e('Enable Map Type Controls (Map, Satellite, or Hybrid)','GoogleMapsWP')?></label><br />
                    <input type="hidden" name="wpGoogleMaps[directions_to]" id="wpGoogleMaps_directions_to_" value="false" />
                    <input type="checkbox" name="wpGoogleMaps[directions_to]" id="wpGoogleMaps_directions_to" value="" checked="checked" />
                    <label for="wpGoogleMaps_directions_to"><?php _e('Display option to get directions <em>to</em> this location.','GoogleMapsWP')?></label><br />
                    <input type="hidden" name="wpGoogleMaps[directions_from]" id="wpGoogleMaps_directions_from_" value="" />
                    <input type="checkbox" name="wpGoogleMaps[directions_from]" id="wpGoogleMaps_directions_from" value="true" />
                    <label for="wpGoogleMaps_directions_from"><?php _e('Display option from get directions <em>from</em> this location.','GoogleMapsWP')?></label>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="button" onclick="return wpGMapsAdmin.sendToEditor(this.form);" value="<?php _e('Send Map to Editor &raquo;','GoogleMapsWP'); ?>" />
        </p>
        <p id="wpGoogleMaps_message">&nbsp;</p>
<?php
    }

    /**
     * Replace our shortCode with the necessary divs (one for the map, and one
     * for directions) and some JavaScript to start the map
     *
     * @param array $attr - array of attributes from the shortCode
     * @param string $content - Content of the shortCode
     * @return string - formatted XHTML replacement for the shortCode
     */
    public function handleShortcodes($attr, $content)
    {
        $this->mapNum++;
        $mapInfo = $this->getMapDetails($attr, $content);
        if (function_exists('json_encode')) {
        	$json = json_encode($mapInfo);
        } else {
			require_once('json_encode.php');
        	$json = Zend_Json_Encoder::encode($mapInfo);
		}

        return <<<mapCode
<div id='map_{$this->mapNum}' style='width:{$mapInfo->width}; height:{$mapInfo->height};' class='googleMap'></div>
<div id='dir_{$this->mapNum}'></div>
<script type="text/javascript">
//<![CDATA[
if (GBrowserIsCompatible()) {
//    wpGMaps.wpNewMap({$this->mapNum}, {$json});

// <-- 20080528 kny Edit

	var func_{$this->mapNum} = document.body.onload;

	function init_{$this->mapNum}(){
	    wpGMaps.wpNewMap({$this->mapNum}, {$json});
		if(func_{$this->mapNum}){
			func_{$this->mapNum}();
		}
	}

	if(-1 != navigator.userAgent.indexOf('MSIE')){
		document.body.onload = init_{$this->mapNum};
	}else{
		func_{$this->mapNum} = null;
		init_{$this->mapNum}();
	}

// <-- 20080528 kny Edit

}
//]]>
</script>
mapCode;
    }

// <-- 20100712 kny Edit
    // for Set CSS files in header
    public function setLinkCSS() {
        if ($this->isMap) {
//			$css = get_option('siteurl') . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/googleMap.css';
			$css =  WP_PLUGIN_URL ."/". GGLMAPWP_DIR_PATH . '/googleMap.css';		// <-- 20110606 kny Edit
?>

<link rel="stylesheet" type="text/css" href="<?php echo $css; ?>" />

<?php
        }
	}
// <-- 20100712 kny Edit

}

// Instantiate our class
$wpGoogleMaps = new wpGoogleMaps();

/**
 * Add filters and actions
 */
if ( !get_option('wpGoogleMaps_api_key') && !isset($_POST['submit']) ) {
    // Add the warning notice if the Google API Key isn't set
    add_action('admin_notices', array($wpGoogleMaps, 'warning'));
} else {
    // Process shortCodes and include JavaScript if the Google API Key is set
    add_filter('the_posts', array($wpGoogleMaps, 'findMaps'));
//    add_action('wp_head', array($wpGoogleMaps, 'setLinkCSS'));    // <-- 20100712 kny Edit
    add_action('wp_print_scripts', array($wpGoogleMaps, 'wpHead'));
    add_shortcode('googleMap', array($wpGoogleMaps, 'handleShortcodes'));
}
add_filter('admin_print_scripts', array($wpGoogleMaps, 'adminHead'));
add_action('admin_menu', array($wpGoogleMaps, 'adminMenu'));
add_action('admin_init', array($wpGoogleMaps, 'registerOptions'));
