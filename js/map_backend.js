/* * * * * * Leaflet Map * * * * * */

jQuery(document).ready(function ($) {

    //tiles
    var tile_url = 'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png';
    var tile_attrib = '<a href="http://www.mapbox.com/about/maps/" target="_blank">Terms &amp; Feedback</a>'
    var mapboxTiles = L.tileLayer( tile_url, {attribution: tile_attrib});
    //map
    var map = L.map('map', {
        fullscreenControl: {
            pseudoFullscreen: false
            }
        })
        .addLayer(mapboxTiles);
    // marker coords
    var coords = [23.072097, -82.468600];
    var coords_dom = document.getElementById('eventsndocs_icoords');
    if (coords_dom != null)
        var coords = coords_dom.value.split(",");
    var zoom = 5;
    var zoom_dom = document.getElementById('eventsndocs_izoom').value;
    if (zoom_dom != null)
        var zoom = zoom_dom*1;
    map.setView([coords[0], coords[1]], zoom);
    //marker
    var coord_marker = L.marker([coords[0], coords[1]],
                               {draggable:true});
    coord_marker.addTo(map);

    //set marker coords when moved or click on map
    coord_marker.on('dragend', function(e) {
        var latlng = this.getLatLng();
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        setCoords(lat, lng);
    });
    map.on('click', function(e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        var newLatLng = new L.LatLng(lat, lng);
        coord_marker.setLatLng(newLatLng);
        setCoords(lat, lng);
    });

    function setCoords(lat, lng){
        document.getElementById('eventsndocs_icoords').value = lat.toFixed(6) +","+ lng.toFixed(6);
    }

    //zoom
    map.on('zoomend', function(e) {
        var z = this.getZoom();
        document.getElementById('eventsndocs_izoom').value = z;
    });

    // geolocation button
    $("#btn_geoloc").click(function(){
        var addr = document.getElementById('eventsndocs_location').value;
        if (addr.length != 0)
            geo_location(addr);
    })

    // geolocation request
    function geo_location (addr) {
        //prepare to search
        var search_addr = encodeURIComponent(addr);
        // use nominatim
        var url= "http://nominatim.openstreetmap.org/?format=json&addressdetails=0&limit=1&q=" + search_addr;
        $.ajax({
            url: url,
            dataType: "json",
            success:function(obj){
                lat = obj[0]['lat'];
                lon = obj[0]['lon'];
                var newLatLng = new L.LatLng(lat, lon);
                coord_marker.setLatLng(newLatLng);
                map.setView(newLatLng);
                document.getElementById('eventsndocs_icoords').value = lat +"," + lon;
            }
        });
    }
 /*
    map.on('fullscreenchange', function () {
        if (!map.isFullscreen()){
            var newLatLng = coord_marker.getLatLng();
            map.fitBounds(newLatLng);
        }
    });
*/


});
