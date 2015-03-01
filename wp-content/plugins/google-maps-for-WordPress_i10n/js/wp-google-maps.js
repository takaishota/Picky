/**
 * Handle: wpGoogleMaps
 * Version: 0.0.1
 * Deps: prototype,googleMaps
 * Enqueue: true
 */
var wpGoogleMaps = Class.create();

wpGoogleMaps.prototype = {
    directions        : '',
    map               : {},
    initialize        : function() {
        this.geocoder = new GClientGeocoder();
        this.directions = new Template(
            '<form action="" id="directions_#{mapNum}" onsubmit="return wpGMaps.getDirections(this);" style="margin-bottom:2em;">' +
            '<label for="new_addr_#{mapNum}">Directions: <b>#{toFrom} Here</b>#{dirLink}:</label>' +
            '<input type="text" style="width:90%;" size="40" maxlength="40" name="new_addr" id="new_addr_#{mapNum}" /><br />' +
            '<input value="Get Directions" style="width:90%;" type="submit" />' +
            '<input type="hidden" name="toFrom" value="#{toFrom}" />' +
            '<input type="hidden" name="mapNum" value="#{mapNum}" />' +
            '<input type="hidden" name="cur_addr" id="cur_addr_#{mapNum}" value="#{name}@#{lat},#{lng}"/></form>'
        );
    },
    getDirections     : function (f)
    {
        var cur_addr = f.cur_addr.value;
        var new_addr = f.new_addr.value;
        if (f.toFrom.value == 'To') {
            var mapString = "from: " + new_addr + " to: " + cur_addr;
        } else {
            var mapString = "from: " + cur_addr + " to: " + new_addr;
        }
        this.map[f.mapNum.value]['gdir'].load(mapString);
        this.map[f.mapNum.value]['map'].closeInfoWindow();
        return false;
    },
    showDirections    : function(toFrom, mapNum) {
        var info = {'toFrom':toFrom, 'mapNum':mapNum};
        info.name = this.map[mapNum]['mapInfo'].get('name');
        info.lat = this.map[mapNum]['mapInfo'].get('point').lat();
        info.lng = this.map[mapNum]['mapInfo'].get('point').lng();
        if (toFrom == 'To' && this.map[mapNum]['mapInfo'].get('directions_from')) {
            info.dirLink = ' - <a href="#" onclick="wpGMaps.showDirections(\'From\', \'' + mapNum + '\'); return false;">From here</a>'
        }
        if (toFrom == 'From' && this.map[mapNum]['mapInfo'].get('directions_to')) {
            info.dirLink = ' - <a href="#" onclick="wpGMaps.showDirections(\'To\', \'' + mapNum + '\'); return false;">To here</a>'
        }
        
        this.updateInfoWindow(mapNum, this.directions.evaluate(info));
    },
    updateInfoWindow  : function(mapNum, html) {
        wpGMaps.map[mapNum]['html'] = html;
        if (!this.map[mapNum]['mapInfo'].get('name')) {
            this.map[mapNum]['mapInfo'].set('name', '');
            this.map[mapNum]['nameTag'] = '';
        } else {
            this.map[mapNum]['nameTag'] = '<h4>' + this.map[mapNum]['mapInfo'].get('name') + '</h4>';
        }
        html = this.map[mapNum]['nameTag'] + this.map[mapNum]['mapInfo'].get('description') + '<br />' + html;
        //this.map[mapNum]['marker'].openInfoWindowHtml(html);
    },
    wpNewMap          : function(mapNum, mapInfo)
    {
        if (this.geocoder) {
            if (!this.map[mapNum]) {
                this.map[mapNum] = {};
            }
            this.map[mapNum]['mapInfo'] = $H(mapInfo);
            var address = this.map[mapNum]['mapInfo'].get('address');
            var name = this.map[mapNum]['mapInfo'].get('name');

            if (!this.map[mapNum]['mapInfo'].get('description')) {
                this.map[mapNum]['mapInfo'].set('description', address);
            }
            var wpGMaps = this;
            this.map[mapNum]['map'] = new GMap2($('map_' + mapNum));
            if (this.map[mapNum]['mapInfo'].get('zoompancontrol')) {
                this.map[mapNum]['map'].addControl(new GLargeMapControl());
            }
            if (this.map[mapNum]['mapInfo'].get('typecontrol')) {
                this.map[mapNum]['map'].addControl(new GMapTypeControl());
            }
            if (this.map[mapNum]['mapInfo'].get('mousewheel')) {
                this.map[mapNum]['map'].enableScrollWheelZoom();
            }

            this.map[mapNum]['gdir'] = new GDirections(this.map[mapNum]['map'], $('dir_' + mapNum));
            this.geocoder.getLatLng(
                address,
                function(point) {
                    if (!point) {
                        alert("There was an error polling the Google Servers for " + address + ".\nPlease try again.")
                    } else {
                        wpGMaps.map[mapNum]['mapInfo'].set('point', point);
                        wpGMaps.map[mapNum]['map'].setCenter(point, 13);
                        wpGMaps.map[mapNum]['marker'] = new GMarker(point);
                        GEvent.addListener(wpGMaps.map[mapNum]['marker'], "click", function() {
                            if (wpGMaps.map[mapNum]['map'].getInfoWindow().isHidden()) {
                                wpGMaps.updateInfoWindow(mapNum, wpGMaps.map[mapNum]['html']);
                            }
                        });
                        wpGMaps.map[mapNum]['map'].addOverlay(wpGMaps.map[mapNum]['marker']);
                        if (wpGMaps.map[mapNum]['mapInfo'].get('directions_to') && wpGMaps.map[mapNum]['mapInfo'].get('directions_from')) {
                            var html = '<p id="directions_' + mapNum + '">Directions: ' +
                                '<a href="#" onclick="wpGMaps.showDirections(\'To\', \'' + mapNum + '\'); return false;">To here</a> - ' +
                                '<a href="#" onclick="wpGMaps.showDirections(\'From\', \'' + mapNum + '\'); return false;">From here</a>' +
                                '</p>';
                            wpGMaps.updateInfoWindow(mapNum, html);
                        } else if (wpGMaps.map[mapNum]['mapInfo'].get('directions_to')){
                            wpGMaps.showDirections('To', mapNum);
                        } else if (wpGMaps.map[mapNum]['mapInfo'].get('directions_from')){
                            wpGMaps.showDirections('From', mapNum);
                        } else {
                            wpGMaps.updateInfoWindow(mapNum, '');
                        }
                    }
                }
            );
        }
    }
}
var wpGMaps = new wpGoogleMaps();

new Event.observe(window, 'unload', GUnload);
