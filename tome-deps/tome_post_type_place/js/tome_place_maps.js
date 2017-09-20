




jQuery(document).ready(function($) {

  //Here is the object being used to create maps. 
  var MapObject = function(mapEl) {
    
    var instance = this;
    this.map = mapEl;
    this.bounds = new google.maps.LatLngBounds();
    this.places = [];
    this.infowindow= null;
    this.mapData = $(this.map).data();

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
          zoom: data.zoom,
          scrollwheel: false,
          zoomControlOptions: {
            style: google.maps.ZoomControlStyle.SMALL,
            position: google.maps.ControlPosition.LEFT_CENTER
          },
          mapTypeId: google.maps.MapTypeId.SATELLITE
        }); 

        if ( data.pov !== "" ) {          
          var mapStreetView = instance.map.getStreetView();
          mapStreetView.setPosition( data.latLng );
          mapStreetView.setPov( {"heading":0,"pitch":0,"zoom":1} );
          mapStreetView.setVisible(true)
        }


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

    if( $('.tome-map').length > 0 ) {
      // Now we can actually load up the Google Maps API
      // TODO : This api key, only works on tome.press
          $('.tome-map').each(function(index, el) {
            var placesMap = new MapObject( $(el) );
              placesMap.makeMap();
          });

    }
  
});




















//InfoWindow Markup Template
var createInfoWindowContent = function(title, content, url) {
  return ''+
      '<div id="content" class="tomeInfoWindow">'+
      ' <div id="siteNotice"></div>'+
      ' <h1 class="tomeInfoWindow-title">'+title+'</h1>'+
      ' <hr class="tomeInfoWindow-hr"/>'+
      ' <div class="tomeInfoWindow-body">'+ content +
      '   <div class="tomeInfoWindow-meta">'+
      '     <a href="'+url+'" class="tomeInfoWindow-btn">Visit Place</a>'+
      '   <div>'+
      ' </div>'+
      '</div>';
};


  function isInt( n ) {
    return n % 1 === 0;
  }

  function explodeWidth() {
    var browserWidth = jQuery(document).width();
    var wrapWidth = jQuery('#chapter-content').width();
    var margin = ( ( browserWidth - wrapWidth ) / 2 );
    var rightMargin = margin;

    if ( isInt( margin ) == 0 )
      rightMargin = rightMargin - 2;      

    jQuery('.explode-wrapper').css({
      'margin-left': - margin,
      'margin-right': - rightMargin
    });
  }

  function wrapExplodedElements() {
    jQuery('.explode-width, .size-full-screen').each(function(index, el) {
      var wrapperEl = document.createElement("DIV");
      wrapperEl = jQuery(wrapperEl).addClass("explode-wrapper");
      jQuery(el).wrap( wrapperEl );
    });

  }



  jQuery(document).ready(function(jQuery) {
    wrapExplodedElements();
    explodeWidth();
  });



//This Object will represent a place, and the preview content for it
//Tome Place Class
var TOME_PLACE = function(lat, lng, title, content, url){

  var that = this,
      _coords = new google.maps.LatLng(lat, lng), 
      _title = title ? title : '', 
      _content = content ? content : '',
      _url = url ? url : '#';

  return {
    getLocation: function() {
      return _coords;
    },
    getTitle:function() {
      return _title;
    },
    getContent:function(){
      return _content;
    },
    getInfoWindowContent:function() {
      return createInfoWindowContent(_title, _content, _url);
    }
  };
};

function tomePinSymbol() {
    var host = window.location.host.split('.');
    var imgDir = '//' + host[host.length-2] + '.' + host[host.length-1] + '/wp-content/themes/tome/img/';
    return {
      size: new google.maps.Size(20, 28),
      scaledSize: new google.maps.Size(20, 28),
      url : imgDir + 'droppin.png',
      // url: 'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|000000|ffffff|ffffff'
    };
}

// This handles the rendering of the maps on various posts. 
// Dependencies: jQuery, Google Maps API

// Sections: 
// 0) Variables Declaration
// 1) Maps Helper Functions Declaration
// 1a) Admin Area Maps Helper Functions Declaration
// 2) onLoad Code to make maps where needed.

//Section 0 : Variables
var mapsInitialized = false;
var geocoder;
var maps = [];
var panoramas = [];
var marker = []; //Used for single marker places (admin, single template)
var markers = []; //Used for archive. This needs refactoring, obviously.
var infowindow = new google.maps.InfoWindow({
    content: '<h1>empty - nothing yet...</h1>',
    disableAutoPan: false,
    maxWidth:440
});

/*--------

Section 1 : Maps Helpers
  • To init map

---------*/
function initialize(_id) {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(0, 0);
    var mapOptions = {
      scrollwheel: false,
      zoom: 1,
      zoomControlOptions: {
          style: google.maps.ZoomControlStyle.SMALL,
          position: google.maps.ControlPosition.LEFT_CENTER
      },
      center: latlng,
      mapTypeId: jQuery('input[name="tome_place_map_type"]').val()
    }
    maps[_id] = new google.maps.Map(document.getElementById('map-canvas-' + _id), mapOptions);
    mapsInitialized = true;

    zoomValue( maps[_id] );

    google.maps.event.addListener(maps[_id], 'maptypeid_changed', function(e) {
      jQuery('input[name="tome_place_map_type"]').val( maps[_id].getMapTypeId() );
    });

      var input = document.getElementById('pac-input');
      var searchBox = new google.maps.places.SearchBox(input);

      //  // Bias the SearchBox results towards current map's viewport.
      //  map.addListener('bounds_changed', function() {
      //   searchBox.setBounds(map.getBounds());
      // });

    return maps[_id];
}

function tomeGetZoom() {
  var zoom = jQuery("input[name='tome_place_zoom']").val();


  if ( zoom === "" ) {
    jQuery("input[name='tome_place_zoom']").val(14);
    zoom = 14;
  }

  return parseInt(zoom);
}

/*--------

  • Geocoding Addresses.

---------*/
function codeAddress(_id) {

  var address = jQuery('.address').val();
  var zoom = tomeGetZoom();

  if ( address.trim() == "" ) {
    
    var lat = jQuery("input[name='tome_place_loc_lat']").val();
    var long = jQuery("input[name='tome_place_loc_long']").val();

    if ( lat !== "" && long !== "" ) {
      var lat = 50.0755381;
      var long = 14.43780049999998;
      maps[_id].setCenter(new google .maps.LatLng(lat,long), 0);
      var type = "satellite";
      tomeSetLocation(lat, long, zoom, type, _id);
    }

  } else {

    geocoder.geocode( { 'address': address}, function(results, status) {
      var type = "satellite";

      if (status == google.maps.GeocoderStatus.OK) {
        maps[_id].setCenter(results[0].geometry.location);
        tomeSetLocation(results[0].geometry.location.lat(),results[0].geometry.location.lng(), zoom, type, _id);
        jQuery("input[name='tome_place_loc_lat']").val(results[0].geometry.location.lat());
        jQuery("input[name='tome_place_loc_long']").val(results[0].geometry.location.lng());

        jQuery('.custom-title-field').val(address);
        jQuery('#title-prompt-text').addClass('screen-reader-text');
      } else {
        alert("Tome coulnd't find any place for your search.");
      }

    });


  }





}

/*--------

  • Center map and place marker.

---------*/
function tomeSetLocation(lat, lng, zoom, type, _id, editable) {
  
  editable = typeof editable !== 'undefined' ? editable : true;

  var latlng = new google.maps.LatLng(lat, lng)
  
  maps[_id].setMapTypeId(type);

  maps[_id].setCenter(latlng);

  if ( zoom !== '' ) {
    maps[_id].setZoom(zoom);
  }

  if(!marker[_id]) {
    marker[_id] = new google.maps.Marker({
        map: maps[_id],
        position: latlng,
        zoom: zoom,
        draggable: editable,
        animation: google.maps.Animation.DROP,
        icon: tomePinSymbol()
        //icon:'http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|000000|ffffff|ffffff'
    });

    google.maps.event.addListener(marker[_id], 'dragend', function(e) {
      tomePlaceUpdateFields(marker[_id], _id);
    });

    google.maps.event.addListener(maps[_id], 'bounds_changed', function(e) { 
      // jQuery("input[name='tome_place_zoom']").val(maps[_id].getZoom()); 
    });

    // setup panoramas if this is Add New Place in admin
    tomeSetupPanorama("", "", _id);
    tomePanoramaListeners(_id);
  } else {
    marker[_id].setPosition(latlng);
  }   

  return marker[_id];
}


/*--------

  • setup street view and display it

---------*/
function tomeSetupPanorama(pos, pov, _id) {

  panoramas[_id] = maps[_id].getStreetView();

  if(pos !== "")
    panoramas[_id].setPosition(pos);

  if(pov !== "") {
    //console.log(pov);
    panoramas[_id].setPov(pov);
    panoramas[_id].setVisible(true);
  }

}

/*--------

  • setup listeners for changes to panorama in admin

---------*/
function tomePanoramaListeners(_id) {
  google.maps.event.addListener(panoramas[_id], 'visible_changed', function() {
      tomePanoramaEventHandler(_id)
  });
  google.maps.event.addListener(panoramas[_id], 'pov_changed', function() {
      tomePanoramaEventHandler(_id)
  });
  google.maps.event.addListener(panoramas[_id], 'position_changed', function() {
      tomePanoramaEventHandler(_id)
  });
}

/*--------

  • put values for admin panorama into field

---------*/
function tomePanoramaEventHandler(_id) {
    if(panoramas[_id].getVisible()) {
      pos = panoramas[_id].getPosition();
      jQuery("input[name='tome_place_loc_lat']").val(pos.lat());
      jQuery("input[name='tome_place_loc_long']").val(pos.lng()); 
      jQuery("input[name='tome_place_pov']").val(JSON.stringify(panoramas[_id].getPov())); 
    } else {
      if(marker[_id])
        // tomePlaceUpdateFields(marker[_id], _id);
      jQuery("input[name='tome_place_pov']").val(""); 
    }
}

/*--------

  • for those who don't notice the little X in the upper right corner of map

---------*/
function toggleStreetView(_id) {
  var toggle = panoramas[_id].getVisible();
  if (toggle == false) {
    panoramas[_id].setVisible(true);
  } else {
    panoramas[_id].setVisible(false);
  }
}

/*--------

  • Creating Multiple Markers for display - accepts JSON encoded posts

---------*/
function tomeRenderAllPlaces(data, _id) {
  var bounds = new google.maps.LatLngBounds (); // Used to fit all markers on map
  jQuery.each(data, function() {  
    bounds.extend (addMarker(this, _id).position);
  });
  maps[_id].fitBounds (bounds);
}

function addMarker(wp_post, _id) {
  var coords = new google.maps.LatLng(wp_post.latitude, wp_post.longitude);
  var m = new google.maps.Marker({
    position:   coords,
    title:      wp_post.post_title,
    map:        maps[_id],
    draggable:  false,
    animation:  google.maps.Animation.DROP,
    content:    wp_post.post_content,
    permalink:  wp_post.permalink,
    tags:       wp_post.tags,
    featuredIn: wp_post.featured_in,
    icon: tomePinSymbol(),
  })

  google.maps.event.addListener(m, 'click', function() {
    //infowindow.open(map,m);
    //infowindow.setContent('<div id="info-window-content"><h1>'+m.title+'</h1>'+'<p>'+m.content+'<br /><a class="button" href="'+m.permalink+'">View Place</a></p></div>');
    jQuery(".map-location-unit").show();
    jQuery(".map-location-unit #text").html(m.title + m.featuredIn + m.tags);
    jQuery(".map-location-unit .button").attr("href", m.permalink);
  });

  markers.push(m);
  return m; // Return Marker so we can access it's props
}

function zoomTo(n, _id) {
  maps[_id].setZoom(Number(n));
}

/*--------

  • Creating Multiple Markers for a Tome Map - accepts JSON encoded posts

---------*/
function tomeRenderMap(data, _id) {
  var infowindow;
  var bounds = new google.maps.LatLngBounds (); // Used to fit all markers on map

  jQuery.each(data, function() {

    var tp = new TOME_PLACE(this.latitude, this.longitude, this.post_title, this.post_content,this.permalink);
    var marker = new google.maps.Marker({
      icon: tomePinSymbol(),
      position: tp.getLocation(),
      draggable: false,
      map: maps[_id],
      infoWindowContent:tp.getInfoWindowContent()
    });
    google.maps.event.addListener(marker, 'click', function() {
      if(infowindow) {
        infowindow.setContent(this.infoWindowContent);
      } else {
        infowindow = new google.maps.InfoWindow({
          content: this.infoWindowContent
        });
      }
      infowindow.open(maps[_id],this);
    });
    bounds.extend ( tp.getLocation() );
  });
  maps[_id].fitBounds (bounds);

  jQuery('body').on('click', '[data-place]', function(e){
    window.location = jQuery(this).data('place');
  });
}

/*--------

  • Add zoom value to the zoom input (which is hidden)

---------*/
function zoomValue(map) {
  
  map.addListener('zoom_changed', function() {
    jQuery('.place-input[name="tome_place_zoom"]').val( map.getZoom() );
  });

}

/*--------

  Section 1a) Functions for Admin Interface only…

---------*/
function tomePlaceUpdateFields(m,_id) {
  var pos = m.getPosition();
  jQuery("input[name='tome_place_loc_lat']").val(pos.lat());
  jQuery("input[name='tome_place_loc_long']").val(pos.lng());  
  jQuery("input[name='tome_place_zoom']").val(maps[_id].getZoom());   
}

/*--------

  Section 2) jQuery to create maps when needed.

---------*/
jQuery(window).load(function () {
  if(jQuery('div.map-canvas')) {
    jQuery('div.map-canvas').each(function() {  

        //Creates a map
        var id = jQuery(this).data("id");
        initialize(id);

        //Map is in Admin Area on the places post type edit screen');
        if(jQuery('div#map-canvas-'+id+'.tome_place_admin-map.haveCoords').length) {

          var map = jQuery('div#map-canvas-'+id+'.tome_place_admin-map.haveCoords');
          var mrk = tomeSetLocation(map.data('latitude'), map.data('longitude'), map.data('zoom'), map.data('type'), id);
          tomeSetupPanorama(mrk.getPosition(), map.data('pov'), id);
          tomePanoramaListeners(id);
        }
        //If we see a front end map receptacle, fill it ;)
        //This is on a single post…
        else if(jQuery('div#map-canvas-'+id+'.place-map-container').length) {

          var map = jQuery('div#map-canvas-'+id+'.place-map-container');    

          if( map.data('latitude') && map.data('longitude')) {

            var mrk = tomeSetLocation(map.data('latitude'), map.data('longitude'), map.data('zoom'), map.data('type'), id, false);

            //If the map has content to display, make the marker launch infowindow.
            if( map.data().placeContent ) {

              google.maps.event.addListener(mrk, 'click', function(mrk) {
                if(infowindow) {
                  infowindow.setContent(createInfoWindowContent( map.data().placeContent.title, map.data().placeContent.content, map.data().placeContent.url ));
                } else {
                  infowindow = new google.maps.InfoWindow({
                    content: createInfoWindowContent( map.data().placeContent.title, map.data().placeContent.content, map.data().placeContent.url )
                  });
                }
                infowindow.open(maps[id],this);
              });
            }
            //If the map has a permalink, make the marker clickable
            else if ( map.data('permalink')) {
              google.maps.event.addListener(mrk, 'click', function() {
                window.location = map.data('permalink');
              });
            }
            //If a panorama is declared…
            tomeSetupPanorama(mrk.getPosition(), map.data('pov'), id);
          }

        }

    });
    
  }

});