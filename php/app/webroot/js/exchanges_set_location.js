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
$(document).ready(function() {
    use_circle = false;
    //Google maps
    debug('start_point: '+start_point.latitude+' '+start_point.longitude);
    latitude_field_id = 'ExchangeLat';
    longitude_field_id = 'ExchangeLng';

    youMarkerConfig = {title: 'Punto de encuentro', draggable: true};
    //si definimos una variable setLocationIcon
    //esta se setea para el ícono.
    if(typeof(setLocationIcon) !== 'undefined') {
        youMarkerConfig.icon = setLocationIcon;
    }
    init_gmap(start_point.latitude, start_point.longitude);
    init_geocoder();

    //Editable combos
    $("#go_button").click(function() {
        get_location($('#location').val());
    });
});
