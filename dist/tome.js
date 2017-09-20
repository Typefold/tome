/*! tome - v0.1.3 - 2017-05-28
* http://tome.press/
* Copyright (c) 2017 Jakub Kohout; Licensed MIT */
(function(exports, $) {

  'use strict';

  exports.cover_embed_video = function() {

	$('#embed-cover iframe').attr({
		width: window.width,
		height: window.height
	});
  };


  // Gallery lightbox
  if ( typeof lightbox != 'undefined' ) {

	lightbox.option({
		'resizeDuration': 200,
		disableScrolling: true
	});

  }

  $('.toggle-topbar').click(function() {
  	$('.top-bar').toggleClass('expanded');
  });

  $('.has-dropdown').click(function() {
  	$(this).addClass('opened').siblings().removeClass('opened');
  });


}(typeof exports === 'object' && exports || this, jQuery));
