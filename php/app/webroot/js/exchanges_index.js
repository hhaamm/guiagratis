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
////Put this on true when you want to avoid the location fields going somewhere when the user press ENTER
//We don't want to search the same thing twice.
var locationEnterBlocks = 0;
//Once this is put on true, when user moves the center, changes location or moves the radio a
//search will be called.
var searchOnEvents = false;

$(document).ready(function() {

    //Google maps
    debug('start_point: '+start_point.latitude+' '+start_point.longitude);

	//We don't want a Marker!'
	youMarkerConfig = null;
    init_gmap(start_point.latitude, start_point.longitude);
    init_geocoder();

	$('#exchange_type_id').change(function() {
		get_exchanges();
	});
    
    $('#text_tags').focusout(function() {
        get_exchanges();
    });

	//Adding map events
	GEvent.addListener(map, 'zoomend', function() {
		get_exchanges();
	});

	GEvent.addListener(map, 'dragend', function() {
		get_exchanges();
	});


    get_exchanges();
});

function change_location() {
	var address = $('#location').val();
	get_location(address);
}

function showAdvancedSearch() {
    $('.adv-search').show();
    $('#show-adv-search').hide();
}

function hideAdvancedSearch() {
    $('#show-adv-search').show();
    $('.adv-search').hide();
}

function blocks_changed_callback(event) {
    show_circle();
}

function get_exchanges() {
	var bounds = map.getBounds();
    console.log(bounds);
	debug(map.getCenter());
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
    searchOnEvents = true;
    refreshCenter();
    debug('get exchanges');
    var exchange_type_id = $('#exchange_type_id').val();
    var text_tags = $('#text_tags').val();
    $.getJSON('/exchanges/get', {
			south:sw.y,
			west:sw.x,
			east:ne.x,
			north:ne.y,
			exchange_type_id:exchange_type_id,
            query:text_tags
	}, get_exchanges_callback);
}

function get_exchanges_callback(data) {
	if (data.exchanges == null) {
		return;
	}
	
	debug('exchanges_got: '+data.exchanges.length);

	map.clearOverlays();
    
    $.each(data.exchanges, function() {

        var exchange = this.Exchange;

		var point = new GLatLng(exchange.lat, exchange.lng);
        
        var icon;
        //esta variable va a venir en el exchange. va a usarse para exchanges destacados
        //principalmente para entidades existentes y/o eventos patrocinados / amigos de la página.
        //por ahora está deshabilitado su uso.
        var destacado = false;
        //según el tipo de exchange, generamos diferentes íconos.
        var textcolor, bgcolor, letter;
        textcolor = 'FFFFFF';
        switch(exchange.exchange_type_id) {
        case exchange_type_id['offer']:
            letter = 'O';            
            bgcolor = 'FF9305';
            break;
        case exchange_type_id['request']:
            letter = 'P';            
            bgcolor = 'FF9305';
            break;
        case exchange_type_id['event']:
            letter = 'E';
            textcolor = '000000';
            bgcolor = 'FF9305';
            break;
        case exchange_type_id['service']:
            bgcolor = 'FF9305';
            letter = 'S';
            break;
        }

        var marker = new google.maps.Marker({
            position: point,
            map: map,
            icon: 'https://chart.googleapis.com/chart?chst=d_map_xpin_letter&chld='+letter+'|'+bgcolor+'|'+textcolor,
            title: exchange.title            
        });
        
		GEvent.addListener(marker, "click", function() {
			window.open('/exchanges/view/'+exchange._id);
		});
		map.addOverlay(marker);
	});
}

function get_circle_radius() {
    return $("#blocks").val()*blocksize;
}

//Returns a exchange by ajax and show the exchange info in the exchange-info div.
function get_exchange(exchange_id) {
    var url = "/exchanges/get_one?exchange_id="+exchange_id;
    $.getJSON(url, null, get_exchanges_callback);
}

//Gets a single exchange for filling an info page.
function get_exchange_callback(data) {
    debug('exchange_got');
    debut('data: '+data);

    //TODO: implement
}

function show_exchange_info(marker, did) {
    $.ajax(
        {url:'/exchanges/get_one',
        data: {did: did},
        success: function(data) {
            marker.openInfoWindowHtml(data);
        }
    })
}

//exchange javascript functions

//Refrigerate!
function addexchange(did) {
    debug('Adding exchange '+did);
    $.ajax({
        url: '/refrigerator/add?did='+did,
        success: function(data) {
            debug('bla');
            $('#show-refrigerate'+did).html('refrigerate');
        }
    });
}

function showexchangeInfo(did) {
    this.location = '/exchanges/show?did='+did;
}

function editexchange(did) {
    this.location = '/exchanges/edit/'+did;
}

function reportexchange(did) {
    /*debug('Reporting exchange '+did);
    $.ajax({
        url: '/reports/add',
        success: function(data) {
            $('#show-report'+did).html('report');
        },
        data: {did:did, detail:''}
    });*/
     showReport(did);
}

//This function will parse a exchange into a well formated html div that can easy be put on bla.
function renderexchangeHtml(info) {
    
}

function showAddBookmark() {
    var opts = {
        title: "Add boookmark",
        modal: false,
        autoOpen: true,
        height: 260,
        width: 400,
        resizable: false,
        buttons: {
            'Create' : function() {
                debug('Created!');
                var data = {
                    detail: $('#bookmark-detail').val(),
                    latitude:$('#latitude').val(),
                    longitude:$('#longitude').val()
                }
                addBookmark(data);
                $(this).dialog('close');
            },
            'Cancel' : function() {
                $(this).dialog('close');
            }
        }
    }
    $('#add-bookmark').dialog(opts);
}

function addBookmark(data) {
    debug(data);
    $.getJSON('/bookmarks/add', data, function(data, textSuccess) {

    });
}

function saveLocationCookie(address) {
    $.ajax({
        url: '/bookmarks/save_cookie',
        data: {location: address}
    });
}
