jQuery(document).ready(function() {	
	jQuery("div.widget_style").jCarouselLite({
		btnPrev: ".previous",
		btnNext: ".next",
		visible: 1,
		afterEnd: function(a) {
			jQuery("a.zoom").attr("href", a[0].firstElementChild);
		}
	});
	jQuery("a[rel^='prettyPhoto']").prettyPhoto({ 
		show_title: false, 
		deeplinking: false,
		social_tools: false,		
		theme: 'light_rounded', 
		counter_separator_label: ' of ',
		allow_resize: true
		});	
	
	var latitude = jQuery('#latitude').text();
	var longitude = jQuery('#longitude').text();
	var zoomLevel = jQuery('#zoomLevel').text();
	var zoom = +zoomLevel;
	var publicProfileName = jQuery('#publicProfileName').text();	
	var map = getGoogleMap(latitude, longitude, zoom, publicProfileName);
});

function getGoogleMap(latitude , longitude , zoomLevel, publicProfileName) {
	var lat = latitude * 1;
	var lng = longitude * 1;
	var geocoder;
	var map;
	var infowindow = new google.maps.InfoWindow();
		
	if (lat != null && lng != null) {
		
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(lat, lng);
		var myOptions = {
			zoom: zoomLevel,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		var gm = document.getElementById("googleMap");
		map = new google.maps.Map(gm, myOptions);
		var marker = new google.maps.Marker({
			map: map, 
			position: latlng
	            //draggable: true
		});
		if (publicProfileName != null) {
			geocoder.geocode({'latLng': latlng}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						var contentString = '<div>'+
						'<p><b>' + publicProfileName + '</b></p>'+
						'<div id="mapAddress">'+
						'<p><b>Address: </b>' + results[0].formatted_address + '</p>'+
						'</div>'+
						'</div>';
						infowindow.setContent(contentString);
					} 
				} else {
					var contentString = '<div id="mapLocation">'+
					'<p>' + latitude + ',' + longitude + '</p>'+
					'</div>';
					infowindow.setContent(contentString);
				}
			});
		}
		google.maps.event.addListener(marker, 'click', function() { infowindow.open(map,marker);});
		return true;		 		
	} 
}