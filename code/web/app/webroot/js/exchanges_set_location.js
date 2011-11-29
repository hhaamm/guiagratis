$(document).ready(function() {
    use_circle = false;
    //Google maps
    debug('start_point: '+start_point.latitude+' '+start_point.longitude);
    latitude_field_id = 'ExchangeLat';
    longitude_field_id = 'ExchangeLng';
    youMarkerConfig = {title: 'Punto de encuentro', draggable: true}
    init_gmap(start_point.latitude, start_point.longitude);
    init_geocoder();

    //Editable combos
    $("#go_button").click(function() {
        get_location($('#location').val());
    });
});