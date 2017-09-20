(function($) {

	function isInt( n ) {
		return n % 1 === 0;
	}

	function explodeWidth() {
		var browserWidth = $(document).width();
		var wrapWidth = $('#chapter-content').width();
		var margin = ( ( browserWidth - wrapWidth ) / 2 );
		var rightMargin = margin;

		if ( isInt( margin ) == 0 )
			rightMargin = rightMargin - 2;			

		$('.explode-wrapper').css({
			'margin-left': - margin,
			'margin-right': - rightMargin
		});
	}


	function wrapExplodedElements() {
		var wrapperEl = document.createElement("DIV");
		wrapperEl = $(wrapperEl).addClass("explode-wrapper");
		$('.explode-width').wrap( wrapperEl );
	}

	wrapExplodedElements();

	explodeWidth();
	$(window).resize(explodeWidth);

	var tomePinSymbol = function() {
		var host = window.location.host.split('.');
		var imgDir = '//' + host[host.length-2] + '.' + host[host.length-1] + '/wp-content/themes/tome/img/droppin.png';
		return imgDir;

	}
	//Here is the object being used to create maps. 
	var MapObject = function(mapEl) {
		
		var instance = this;
		this.map = mapEl;
		this.bounds = new google.maps.LatLngBounds();
		this.places = [];
		this.infowindow= null;

		//Infowindow markup
		this.createInfoWindowContent = function(data) {
			return ''+
			'<div id="content" class="tomeInfoWindow">'+
			' <div id="siteNotice"></div>'+
			' <h1 class="tomeInfoWindow-title">'+data.placeTitle+'</h1>'+
			' <hr class="tomeInfoWindow-hr"/>'+
			' <div class="tomeInfoWindow-body">'+ data.placeContent +
			'   <div class="tomeInfoWindow-meta">'+
			'     <a href="'+data.placeUrl+'" class="tomeInfoWindow-btn button">Visit Place</a>'+
			'   <div>'+
			' </div>'+
			'</div>';
		};
		//This function makes the map.
		this.makeMap = function() {

			var placesCount = 0;
			var position = "";

			//First we are going to store data for all the places we find.
			instance.map.find('[data-lat-lng]').each(function(index, el){
				placesCount++;
				position  = new google.maps.LatLng($(this).data().latLng);

				instance.places.push( {
					latLng : position,
					placeData : $(this).data() //This field is used for infowindow
				});

				//extend the bounds to include each marker's position
				instance.bounds.extend( position );
			});

			// That means we are printing map not a place (in Tome speech)
			if ( placesCount > 1 ) {
				// Create a map object and specify the DOM element for display.
				instance.map = new google.maps.Map(instance.map[0], {
					scrollwheel: false,
					zoom: 4,
					zoomControlOptions: {
						style: google.maps.ZoomControlStyle.SMALL,
						position: google.maps.ControlPosition.LEFT_CENTER
					},
					mapTypeId: google.maps.MapTypeId.SATELLITE
				});
				
				// Fit to the map markers
				instance.map.fitBounds(instance.bounds);

			} else {
				var data = instance.places[0].placeData;
				
				instance.map = new google.maps.Map(instance.map[0], {
					center: position,
					zoom: data.placeZoom,
					scrollwheel: false,
					zoomControlOptions: {
						style: google.maps.ZoomControlStyle.SMALL,
						position: google.maps.ControlPosition.LEFT_CENTER
					},
					mapTypeId: data.mapType
				});

				var panorama = new google.maps.StreetViewPanorama(
					mapEl[0], {
						position: position,
						pov: data.pov
					});

				instance.map.setStreetView(panorama);

			}

			
			// Create infoWindow
			instance.infowindow = new google.maps.InfoWindow({maxWidth:440});
			
			// Add the places markers to the map.
			// Create a marker and set its position.
			for (var i = 0; i < instance.places.length; i++) {
				// Create a marker
				var marker = new google.maps.Marker({
					map: instance.map,
					position: instance.places[i].latLng,
					placeData: instance.places[i].placeData,
					animation: google.maps.Animation.DROP,
					icon: tomePinSymbol(),
				});
				// Register a listener
				google.maps.event.addListener(marker, 'click', (function(marker) {
					return function() {
						instance.infowindow.setContent( instance.createInfoWindowContent(marker.placeData) );
						instance.infowindow.open(instance.map, marker);
					}
				})(marker, i));
			}	
		}
	}


	$(function(){
		if( $('.tome-map').length > 0 ) {
			// Now we can actually load up the Google Maps API
			// TODO : This api key, only works on tome.press

			google.load("maps", "3", {"key" : "AIzaSyDZKRUlQtlabNgBDFyCLRhCXVE7IrMRKao", "callback" : function() {
					$('.tome-map').each(function(index, el) {
						var placesMap = new MapObject( $(el) );
							placesMap.makeMap();
					});
				}
			});

		}
	});
})(jQuery);