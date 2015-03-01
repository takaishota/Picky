/**
 * Handle: wpGoogleMapsAdmin
 * Version: 0.0.2
 * Deps: jquery
 * Enqueue: true
 */
/**
 * We have to kill the alerts, so that Google's API won't send alerts to the
 * user.  Setting KillAlerts to false later will restore alert capabilities
 */
var KillAlerts = true;
var realAlert = alert;
var alert = new Function('a', 'if(!KillAlerts){realAlert(a)}');

jQuery(document).ready(wpGoogleMapsLoad);

function wpGoogleMapsLoad()
{
	if (typeof GUnload == 'function') {
    	jQuery(window).unload(GUnload);
	}
    // Re-anable alerts
    KillAlerts = false;

    if (typeof GBrowserIsCompatible == 'function' && GBrowserIsCompatible()) {
        var msgColor = 'CFC';
        var msgText = 'Your Google API Key appears to be valid!';
    } else {
        var msgColor = 'FCC';
        if (typeof G_INCOMPAT != 'undefined' && G_INCOMPAT) {
            var msgText = 'Your Google API Key appears to be invalid! <a href="http://code.google.com/apis/maps/signup.html">Sign up</a> for a new one.';
        } else {
            var msgText = "Google Maps for WordPress can't tell if the Google API Key is valid.<br />This is probably because you have not yet specified a key, or your browser is not compatible with the Google Maps API.";
        }
    }
    jQuery("#wpGoogleMaps_message").replaceWith('<p id="wpGoogleMaps_message" style="padding: .5em; background-color: #' + msgColor + '; font-weight: bold;">' + msgText + '</p>');
}

var wpGoogleMapsAdmin = function () {}

wpGoogleMapsAdmin.prototype = {
    options           : {},
    generateShortCode : function() {
        var address = this['options']['address'];
        delete this['options']['address'];

        var attrs = '';
        jQuery.each(this['options'], function(name, value){
            if (value != '') {
                attrs += ' ' + name + '="' + value + '"';
            }
        });
        return '[googleMap' + attrs + ']' + address + '[/googleMap]'
    },
    sendToEditor      : function(f) {
        var collection = jQuery(f).find("input[id^=wpGoogleMaps]:not(input:checkbox),input[id^=wpGoogleMaps]:checkbox:checked");
        var $this = this;
        collection.each(function () {
            var name = this.name.substring(13, this.name.length-1);
            $this['options'][name] = this.value;
        });
        send_to_editor(this.generateShortCode());
        return false;
    }
}

var wpGMapsAdmin = new wpGoogleMapsAdmin();