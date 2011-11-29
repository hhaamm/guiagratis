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

function get_location_callback(answer) {
    debug([answer.Placemark[0]['Point'].coordinates[0], answer.Placemark[0]['Point'].coordinates[1]]);
    var latitude = answer.Placemark[0]['Point'].coordinates[1];
    var longitude = answer.Placemark[0]['Point'].coordinates[0];

    $('#status').val('Dirección encontrada!');
    var point = new GLatLng(latitude, longitude);
    set_user_location(point);
}

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