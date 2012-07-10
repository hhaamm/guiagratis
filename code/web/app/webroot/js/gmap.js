/*
 * Guia Gratis, sistema para intercambio de regalos.
 * Copyright (C) 2011  Hugo Alberto Massaroli
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
//Global variables
//
//blocksize in miles
blocksize=1/16;
use_circle = false;

//Gmap
var map;
//Where the user is now
var glocation;
//Geocoder for get address
var geocoder;
//Circle shown where the user is
var circle;

//Default values = 'latitude' and 'longitude'
var latitude_field_id;
var longitude_field_id;

//Defauot config
var youMarkerConfig = {title:'You'}
//Use this when you want to have a custom dragend function.
var youMarkerDragendCallback = null;

//Reverse geocoding
var reverseGeocoding = true;
var location_status_field_id = 'location-status';

//Init functions
function init_gmap(latitude, longitude) {
    map = new GMap(document.getElementById("map"));
    map.addControl(new GLargeMapControl());
    map.addControl(new GMapTypeControl());
    $('#'+get_latitude_field_id()).val(longitude);
    $('#'+get_longitude_field_id()).val(latitude);
    var point = new GLatLng(latitude, longitude);
    //map.centerAndZoom(point, 3);
    set_user_location(point);
}

function init_geocoder() {
    geocoder = new GClientGeocoder();
}

function get_location(address) {
    geocoder.getLocations(address, get_location_callback);
}

function get_location_callback(answer, callback) {
    debug([answer.Placemark[0]['Point'].coordinates[0], answer.Placemark[0]['Point'].coordinates[1]]);
    var latitude = answer.Placemark[0]['Point'].coordinates[1];
    var longitude = answer.Placemark[0]['Point'].coordinates[0];

    $('#status').val('Dirección encontrada!');
    var point = new GLatLng(latitude, longitude);
    set_user_location(point);
    get_exchanges();
}

var marker_has_been_moved = false;
function refreshCenter() {
    map.clearOverlays();
    debug(glocation);
	if (youMarkerConfig != null) {
		var marker = new GMarker(glocation, youMarkerConfig);
		//Adding dragable listener
		GEvent.addListener(marker, 'dragend', function() {
			debug('dragend');
			newPoint = marker.getLatLng();
			set_user_location(newPoint);
                        marker_has_been_moved = true;

		        //If reverse geocoding is enabled, we use it
		        if (reverseGeocoding == true) {
			    $('#'+location_status_field_id).text('Se ha cambiado la ubicación');
			    // evitamos que el usuario
			    marker_has_been_moved = false;
			    $.getJSON('/exchanges/reverse_geocoding', {lat: newPoint.lat(), lng:newPoint.lng()}, function(result) {
				$('#ExchangeCountry').val(result.country);
				$('#ExchangeProvince').val(result.province);
				$('#ExchangeLocality').val(result.locality);

				// hasta que no obtenemos los datos de la geolocalización no dejamos al usuario continuar
				marker_has_been_moved = true;
			    });
			}
		});
		if (youMarkerDragendCallback) {
			GEvent.addListener(marker, 'dragend', youMarkerDragendCallback);
		}
		map.addOverlay(marker);
	}

    debug('use_circle: '+use_circle);
    if (use_circle)
        show_circle(true);
}

function set_user_location(point) {
    //Setting global user location
    glocation = point;

    map.centerAndZoom(point, 3);
    //Showing marker and circle
    refreshCenter();

    debug('latitude_field_id: '+get_latitude_field_id());
    debug('longitude_field_id: '+get_longitude_field_id());
    $('#'+get_latitude_field_id()).val(point.lat());
    $('#'+get_longitude_field_id()).val(point.lng());
}

//new_circle parameter forces creating new circle
function show_circle(new_circle) {
    if (new_circle)
        circle = null;

    //creating circle
    if (circle == undefined) {
        circle = new CircleOverlay(glocation, get_circle_radius(), "#336699", 1, 1, '#336699', 0.25);
        map.addOverlay(circle);
    } else {
        circle.setRadius(get_circle_radius());
    }
    circle.redraw();
}

//TODO: hacer que devuelvan directamente '#latitude'
function get_latitude_field_id() {
    return latitude_field_id == undefined ? 'latitude' : latitude_field_id;
}

function get_longitude_field_id() {
    return latitude_field_id == undefined ? 'longitude' : longitude_field_id;
}

function get_custom_icon(letter, bgcolor, textcolor, star) {
    var icon = new GIcon();
    if (star) {
        icon.image = 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld='+letter+'|'+bgcolor+'|'+textcolor;
    } else {
        icon.image = 'https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld='+letter+'|'+bgcolor+'|'+textcolor;
    }
    icon.shadow = 'https://chart.googleapis.com/chart?chst=d_map_pin_shadow&chld=pin';
    icon.iconAnchor = new GPoint(10, 34);
    icon.shadowSize = new GSize(40,37);
    icon.iconSize = new GSize(21, 34);
    return icon;
}