/* globals hrmpf */

var boundingBox = new L.bounds();
//container for markers in order to access manual actions
var markersContainer = {};
//layer to group markers on map
var markersLayer = L.markerClusterGroup();
//rig line container, only one can be displayed at a time
var wellLine = '';
var map;
document.addEventListener('DOMContentLoaded', function ()
{
	$('#sl-wellid').change(function()
	{
		clickMarker($(this).val());
	});

    //get coords from map container
    var neBounds = UTMtoLL({
        easting: parseFloat($('#sl-map').data('neBounds1')),
        northing: parseFloat($('#sl-map').data('neBounds2')),
        zoneLetter: 'U',
        zoneNumber: 12
    });
    var swBounds = UTMtoLL({
        easting: parseFloat($('#sl-map').data('swBounds1')),
        northing: parseFloat($('#sl-map').data('swBounds2')),
        zoneLetter: 'U',
        zoneNumber: 12
    });

    var bounds = [
        [swBounds.lat, swBounds.lng],
        [neBounds.lat, neBounds.lng]
        // [57.306663, -111.180877],  //489102.696 6351537.554 SW
        // [57.441692, -111.008655]   //499480.53 6366554.882, NE
    ];

	if ($('#sl-map').length) {
		map = startMap('sl-map', bounds, $('#mapMarkers').data('mapId'));
		drawMarkers();
	}

	$('.editRigWellRelation').click(function(e) {
		e.preventDefault();
		$(this).siblings('.wellSuggest').val('').attr('type', 'text');
		$(this).siblings('span').remove();
		$(this).remove();
		// console.log($(this).siblings('.wellSuggest').attr('type', 'text'));
	});

	$( ".wellSuggest" ).autocomplete({
		source: function (request, response) {
			$.ajax({
				url: "/well/search/" + request.term,
				success: function (data) {
					response(data);
				},
				error: function () {
					response([]);
				},
				dataType: 'JSON'
			});
		},
		minLength: 2,
		select: function( e, ui ) {
			$(e.target).val(ui.item.value);
			$(e.target).siblings('input').val(ui.item.id);
		}
	});


    //add markers to map on click; debug
    var counter = 0;
    map.on('click', function(e) {
        var marker = L.marker([e.latlng.lat, e.latlng.lng]);
        var popup = L.popup({
            minWidth:250
        });
        var utm = LLtoUTM(e.latlng);

        popup.setContent('<p>test.ing</p> <p>'+ utm.easting +', '+ utm.northing + '</p>');
        marker.bindPopup(popup);
        marker.addTo(map);

        if (counter == 0 ) {
            $('#SWbounds1').val(utm.easting);
            $('#SWbounds2').val(utm.northing);
        } else {
            $('#NEbounds1').val(utm.easting);
            $('#NEbounds2').val(utm.northing);
        }
        counter++;

        console.log(e.latlng.lat + ", " + e.latlng.lng);
        console.log(utm.easting + ", " + utm.northing);
    });


});

/**
 * Creates a leaflet marker from UTM 12N coordinates.
 *
 * @param easting
 * @param northing
 * @param label
 * @param debug
 *
 * @return L.Marker
 */
function createMarker(easting, northing, label, debug) {

	easting = parseFloat(easting);
	northing = parseFloat(northing);

	//@TODO debug, try to setup offset
	// easting = parseFloat(easting) - parseFloat(546.53);
	// northing = parseFloat(northing) - parseFloat(305.882);


    var latLng = UTMtoLL({
        easting: easting,
        northing: northing,
        zoneLetter: 'U',
        zoneNumber: 12
    });

    var marker = L.marker([latLng.lat, latLng.lng], { title: label});
    var popup = L.popup({
        minWidth:250
    });

    // @TODO use html from wrapper within twig ?
    popup.setContent('<p>' + label + '</p> <p>'+ easting +', '+ northing + '<br /> WellId:'+debug.wellId +'; RigId:' + debug.rigId +
        ' <br /> Status: undefined</p>');
    marker.bindTooltip(label, { permanent: true});
    marker.bindPopup(popup);

    marker.on('click', function(e) {
        // marker.openPopup();
    });

    return marker;
}

/**
 * Start up a leaflet map, limiting it to given bounds, with custom image background as imageOverlay layer.
 * @TODO how to find center coords ? // new boundingbox; getCenter ?
 *
 * @param identifier
 * @param bounds
 * @param mapId
 *
 * @returns L.Map
 */
function startMap(identifier, bounds, mapId) {
    var map = new L.Map(identifier, {
        continuousWorld: true,
        worldCopyJump: false,
        maxBounds: bounds,
        center: [57.393924, -111.095123], // @TODO getCenter !?
        zoom: 13
    });

    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        minZoom: 13
    }).addTo(map);

    // add the image overlay, load mapId from container
    var url = '/resources/maps/' + mapId + '.jpg';
    L.imageOverlay(url, bounds).addTo(map);

    return map;
}

/**
 * load markers from container and add them to map
 */
function drawMarkers() {
    //reset container
    markersContainer = {};
    $('#mapMarkers span').each(function(k, v) {
        if ($(v).data('northing') != '') {
            var debug = {
                wellId: $(v).data('wellId'),
                rigId: $(v).data('rigId')
            };
            // console.log($(v).data('easting'));
            // console.log($(v).data('northing'));
            var marker = createMarker($(v).data('easting'), $(v).data('northing'), $(v).data('label'), debug);
            markersLayer.addLayer(marker);

            //create array first
            if (typeof (markersContainer[$(v).data('rigId')]) === "undefined") {
                markersContainer[$(v).data('rigId')] = {};
            }

            markersContainer[$(v).data('rigId')][$(v).data('wellId')] = marker;
        }
    });
    markersLayer.addTo(map);
}

function clickMarker(wellId) {
	for (var data in markersContainer) {
		if (typeof markersContainer[data][wellId] !== "undefined") {
			var marker = markersContainer[data][wellId];
			map.setView(marker._latlng, 16);
			marker.openPopup();
		}
	}
}

/**
 * Draws a basic line, connecting wells for given rigId.
 *
 * @param rigId
 */
function drawRigPath(rigId) {
    var lineDots = [];
    var count = Object.keys(markersContainer[rigId]).length;
    var i = 0;
    for (var wellId in markersContainer[rigId]) {
        var marker = markersContainer[rigId][wellId];

        //center map on middle marker
        if (Math.ceil(count / 2) == i) {
            map.setView(marker._latlng, 16);
        }

        lineDots.push([marker._latlng.lat, marker._latlng.lng]);
        i++;
    }

    //reset line
    if (wellLine !== '') {
        map.removeLayer(wellLine);
    }
    //create basic line
    wellLine = new L.polyline(lineDots, {
        color: 'red',
        weight: 10,
        opacity: .9,
        // dashArray: '20,15',
        lineJoin: 'round'
    }).addTo(map);
}



/* latlon to UTM conversion magick */


/**
 * Converts a set of Longitude and Latitude co-ordinates to UTM
 * using the WGS84 ellipsoid.
 *
 * @private
 * @param {object} ll Object literal with lat and lon properties
 *     representing the WGS84 coordinate to be converted.
 * @return {object} Object literal containing the UTM value with easting,
 *     northing, zoneNumber and zoneLetter properties, and an optional
 *     accuracy property in digits. Returns null if the conversion failed.
 */
function LLtoUTM(ll) {
	var Lat = ll.lat;
	var Long = ll.lng;
	var a = 6378137.0; //ellip.radius;
	var eccSquared = 0.00669438; //ellip.eccsq;
	var k0 = 0.9996;
	var LongOrigin;
	var eccPrimeSquared;
	var N, T, C, A, M;
	var LatRad = degToRad(Lat);
	var LongRad = degToRad(Long);
	var LongOriginRad;
	var ZoneNumber;
	// (int)
	ZoneNumber = Math.floor((Long + 180) / 6) + 1;

	//Make sure the longitude 180.00 is in Zone 60
	if (Long === 180) {
		ZoneNumber = 60;
	}

	// Special zone for Norway
	if (Lat >= 56.0 && Lat < 64.0 && Long >= 3.0 && Long < 12.0) {
		ZoneNumber = 32;
	}

	// Special zones for Svalbard
	if (Lat >= 72.0 && Lat < 84.0) {
		if (Long >= 0.0 && Long < 9.0) {
			ZoneNumber = 31;
		}
		else if (Long >= 9.0 && Long < 21.0) {
			ZoneNumber = 33;
		}
		else if (Long >= 21.0 && Long < 33.0) {
			ZoneNumber = 35;
		}
		else if (Long >= 33.0 && Long < 42.0) {
			ZoneNumber = 37;
		}
	}

	LongOrigin = (ZoneNumber - 1) * 6 - 180 + 3; //+3 puts origin
	// in middle of
	// zone
	LongOriginRad = degToRad(LongOrigin);

	eccPrimeSquared = (eccSquared) / (1 - eccSquared);

	N = a / Math.sqrt(1 - eccSquared * Math.sin(LatRad) * Math.sin(LatRad));
	T = Math.tan(LatRad) * Math.tan(LatRad);
	C = eccPrimeSquared * Math.cos(LatRad) * Math.cos(LatRad);
	A = Math.cos(LatRad) * (LongRad - LongOriginRad);

	M = a * ((1 - eccSquared / 4 - 3 * eccSquared * eccSquared / 64 - 5 * eccSquared * eccSquared * eccSquared / 256) * LatRad - (3 * eccSquared / 8 + 3 * eccSquared * eccSquared / 32 + 45 * eccSquared * eccSquared * eccSquared / 1024) * Math.sin(2 * LatRad) + (15 * eccSquared * eccSquared / 256 + 45 * eccSquared * eccSquared * eccSquared / 1024) * Math.sin(4 * LatRad) - (35 * eccSquared * eccSquared * eccSquared / 3072) * Math.sin(6 * LatRad));

	var UTMEasting = (k0 * N * (A + (1 - T + C) * A * A * A / 6.0 + (5 - 18 * T + T * T + 72 * C - 58 * eccPrimeSquared) * A * A * A * A * A / 120.0) + 500000.0);

	var UTMNorthing = (k0 * (M + N * Math.tan(LatRad) * (A * A / 2 + (5 - T + 9 * C + 4 * C * C) * A * A * A * A / 24.0 + (61 - 58 * T + T * T + 600 * C - 330 * eccPrimeSquared) * A * A * A * A * A * A / 720.0)));
	if (Lat < 0.0) {
		UTMNorthing += 10000000.0; //10000000 meter offset for
		// southern hemisphere
	}

	return {
		northing: Math.round(UTMNorthing),
		easting: Math.round(UTMEasting),
		zoneNumber: ZoneNumber,
		zoneLetter: getLetterDesignator(Lat)
	};
}

/**
 * Conversion from degrees to radians.
 *
 * @private
 * @param {number} deg the angle in degrees.
 * @return {number} the angle in radians.
 */
function degToRad(deg) {
	return (deg * (Math.PI / 180.0));
}

/**
 * Conversion from radians to degrees.
 *
 * @private
 * @param {number} rad the angle in radians.
 * @return {number} the angle in degrees.
 */
function radToDeg(rad) {
	return (180.0 * (rad / Math.PI));
}

/**
 * Calculates the MGRS letter designator for the given latitude.
 *
 * @private
 * @param {number} lat The latitude in WGS84 to get the letter designator
 *     for.
 * @return {char} The letter designator.
 */
function getLetterDesignator(lat) {
	//This is here as an error flag to show that the Latitude is
	//outside MGRS limits
	var LetterDesignator = 'Z';

	if ((84 >= lat) && (lat >= 72)) {
		LetterDesignator = 'X';
	}
	else if ((72 > lat) && (lat >= 64)) {
		LetterDesignator = 'W';
	}
	else if ((64 > lat) && (lat >= 56)) {
		LetterDesignator = 'V';
	}
	else if ((56 > lat) && (lat >= 48)) {
		LetterDesignator = 'U';
	}
	else if ((48 > lat) && (lat >= 40)) {
		LetterDesignator = 'T';
	}
	else if ((40 > lat) && (lat >= 32)) {
		LetterDesignator = 'S';
	}
	else if ((32 > lat) && (lat >= 24)) {
		LetterDesignator = 'R';
	}
	else if ((24 > lat) && (lat >= 16)) {
		LetterDesignator = 'Q';
	}
	else if ((16 > lat) && (lat >= 8)) {
		LetterDesignator = 'P';
	}
	else if ((8 > lat) && (lat >= 0)) {
		LetterDesignator = 'N';
	}
	else if ((0 > lat) && (lat >= -8)) {
		LetterDesignator = 'M';
	}
	else if ((-8 > lat) && (lat >= -16)) {
		LetterDesignator = 'L';
	}
	else if ((-16 > lat) && (lat >= -24)) {
		LetterDesignator = 'K';
	}
	else if ((-24 > lat) && (lat >= -32)) {
		LetterDesignator = 'J';
	}
	else if ((-32 > lat) && (lat >= -40)) {
		LetterDesignator = 'H';
	}
	else if ((-40 > lat) && (lat >= -48)) {
		LetterDesignator = 'G';
	}
	else if ((-48 > lat) && (lat >= -56)) {
		LetterDesignator = 'F';
	}
	else if ((-56 > lat) && (lat >= -64)) {
		LetterDesignator = 'E';
	}
	else if ((-64 > lat) && (lat >= -72)) {
		LetterDesignator = 'D';
	}
	else if ((-72 > lat) && (lat >= -80)) {
		LetterDesignator = 'C';
	}
	return LetterDesignator;
}

/**
 * Converts UTM coords to lat/long, using the WGS84 ellipsoid. This is a convenience
 * class where the Zone can be specified as a single string eg."60N" which
 * is then broken down into the ZoneNumber and ZoneLetter.
 *
 * @private
 * @param {object} utm An object literal with northing, easting, zoneNumber
 *     and zoneLetter properties. If an optional accuracy property is
 *     provided (in meters), a bounding box will be returned instead of
 *     latitude and longitude.
 * @return {object} An object literal containing either lat and lon values
 *     (if no accuracy was provided), or top, right, bottom and left values
 *     for the bounding box calculated according to the provided accuracy.
 *     Returns null if the conversion failed.
 */
function UTMtoLL(utm) {
	var UTMNorthing = utm.northing;
	var UTMEasting = utm.easting;
	var zoneLetter = utm.zoneLetter;
	var zoneNumber = utm.zoneNumber;
	// check the ZoneNummber is valid
	if (zoneNumber < 0 || zoneNumber > 60) {
		return null;
	}

	var k0 = 0.9996;
	var a = 6378137.0; //ellip.radius;
	var eccSquared = 0.00669438; //ellip.eccsq;
	var eccPrimeSquared;
	var e1 = (1 - Math.sqrt(1 - eccSquared)) / (1 + Math.sqrt(1 - eccSquared));
	var N1, T1, C1, R1, D, M;
	var LongOrigin;
	var mu, phi1Rad;

	// remove 500,000 meter offset for longitude
	var x = UTMEasting - 500000.0;
	var y = UTMNorthing;

	// We must know somehow if we are in the Northern or Southern
	// hemisphere, this is the only time we use the letter So even
	// if the Zone letter isn't exactly correct it should indicate
	// the hemisphere correctly
	if (zoneLetter < 'N') {
		y -= 10000000.0; // remove 10,000,000 meter offset used
		// for southern hemisphere
	}

	// There are 60 zones with zone 1 being at West -180 to -174
	LongOrigin = (zoneNumber - 1) * 6 - 180 + 3; // +3 puts origin
	// in middle of
	// zone

	eccPrimeSquared = (eccSquared) / (1 - eccSquared);

	M = y / k0;
	mu = M / (a * (1 - eccSquared / 4 - 3 * eccSquared * eccSquared / 64 - 5 * eccSquared * eccSquared * eccSquared / 256));

	phi1Rad = mu + (3 * e1 / 2 - 27 * e1 * e1 * e1 / 32) * Math.sin(2 * mu) + (21 * e1 * e1 / 16 - 55 * e1 * e1 * e1 * e1 / 32) * Math.sin(4 * mu) + (151 * e1 * e1 * e1 / 96) * Math.sin(6 * mu);
	// double phi1 = ProjMath.radToDeg(phi1Rad);

	N1 = a / Math.sqrt(1 - eccSquared * Math.sin(phi1Rad) * Math.sin(phi1Rad));
	T1 = Math.tan(phi1Rad) * Math.tan(phi1Rad);
	C1 = eccPrimeSquared * Math.cos(phi1Rad) * Math.cos(phi1Rad);
	R1 = a * (1 - eccSquared) / Math.pow(1 - eccSquared * Math.sin(phi1Rad) * Math.sin(phi1Rad), 1.5);
	D = x / (N1 * k0);

	var lat = phi1Rad - (N1 * Math.tan(phi1Rad) / R1) * (D * D / 2 - (5 + 3 * T1 + 10 * C1 - 4 * C1 * C1 - 9 * eccPrimeSquared) * D * D * D * D / 24 + (61 + 90 * T1 + 298 * C1 + 45 * T1 * T1 - 252 * eccPrimeSquared - 3 * C1 * C1) * D * D * D * D * D * D / 720);
	lat = radToDeg(lat);

	var lon = (D - (1 + 2 * T1 + C1) * D * D * D / 6 + (5 - 2 * C1 + 28 * T1 - 3 * C1 * C1 + 8 * eccPrimeSquared + 24 * T1 * T1) * D * D * D * D * D / 120) / Math.cos(phi1Rad);
	lon = LongOrigin + radToDeg(lon);

	var result;
	if (utm.accuracy) {
		var topRight = UTMtoLL({
			northing: utm.northing + utm.accuracy,
			easting: utm.easting + utm.accuracy,
			zoneLetter: utm.zoneLetter,
			zoneNumber: utm.zoneNumber
		});
		result = {
			top: topRight.lat,
			right: topRight.lon,
			bottom: lat,
			left: lon
		};
	}
	else {
		result = {
			lat: lat,
			lng: lon
		};
	}
	return result;
}