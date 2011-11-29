//Put this on true when you want to avoid the location fields going somewhere when the user press ENTER
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
    
    //Editable combos
    //See why this is not working (options)    
    $("#location").keyup(function(event) {
    	change_location();
	});

	$('#exchange_type_id').change(function() {
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
	get_exchanges();
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
	debug(map.getCenter());
	var sw = bounds.getSouthWest();
	var ne = bounds.getNorthEast();
    searchOnEvents = true;
    refreshCenter();
    debug('get exchanges');
    var exchange_type_id = $('#exchange_type_id').val();
    $.getJSON('/exchanges/get', {
			south:sw.y,
			west:sw.x,
			east:ne.x,
			north:ne.y,
			exchange_type_id:exchange_type_id
	}, get_exchanges_callback);
}

function get_exchanges_callback(data) {
	if (data.exchanges == null) {
		return;
	}
	
	debug('exchanges_got: '+data.exchanges.length);

	map.clearOverlays();
    
    $.each(data.exchanges, function() {
        debug('inside for');
		
        var exchange = this.Exchange;
		
		debug('exchange: '+exchange.title);
		debug(exchange.lat+" "+exchange.lng);
		var point = new GLatLng(exchange.lat, exchange.lng);
        
		var markerOptions = {title:exchange.title};
		
		if (exchange.custom_icon == 1) {
			var icon = new GIcon();
			icon.image = exchange.custom_icon_pic;
			icon.shadow = exchange.custom_icon_shadow;
			//icon.iconSize = new GSize(59, 62);
			//icon.shadowSize = new GSize(91, 62);
			icon.iconAnchor = new GPoint(37, 59);
			icon.infoWindowAnchor = new GPoint(31, 8);
			markerOptions.icon = icon;
		}
            
		var marker = new GMarker(point, markerOptions);
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
