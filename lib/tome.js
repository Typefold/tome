/*
 * tome
 * http://tome.press/
 *
 * Copyright (c) 2016 Jakub Kohout
 * Licensed under the MIT license.
 */
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
