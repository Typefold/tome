(function($) {

	jQuery(document).foundation().foundation('abide', {
		patterns: {
			embed_code: /https?:\/\/(www\.)?[-a-zA-Z0-9@:%._\+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_\+.~#?&//=]*)/
		}
	});

	function initActiveGalleryModal() {
		$parent_modal = $(".tome-gallery-modal.active");
		$slide_me = jQuery($parent_modal).find('.slide-me');
		$slide_me.show();

		$slide_me.on('init', function(event, slick, currentSlide) {

			var currentSlide = $slide_me.find('.slick-active'),
			description = $(currentSlide).find('.slide-description').text(),
			caption = $(currentSlide).find('.slide-caption').text();

			$('.tome-gallery-modal').find('.sidebar .description p').text(description);
			$('.tome-gallery-modal').find('.sidebar .caption p').text(caption);

		});


		$parent_modal.find('.slick-initialized').slick('unslick');
		$parent_modal.find('.sidebar .controls button').remove();
		$slide_me.slick({
			infinite: true,
			slidesToShow: 1,
			slidesToScroll: 1,
			arrows: true,
			respondTo: 'min',
			appendArrows: '.tome-gallery-modal.active .sidebar .controls',
			prevArrow: '<button class="ctrl-previous">Previous</button>',
			nextArrow: '<button class="ctrl-next">Next</button>',
			adaptiveHeight: true,
			fade: true,
			responsive: [
				{
					breakpoint: 960,
					settings: {
						adaptiveHeight: true
					}
				}
			]
		});


		$slide_me.off('afterChange init reInit')
			.on('afterChange init reInit', function(event, slick, currentSlide, nextSlide) {
				// 1. set text-based pager. ie, "3 of 6"
				// console.log("CHANGED");

				// grab the info we need.
				$this = $(this);
				slideCount = slick.slideCount;
				slideNum = slick.currentSlide + 1;
				$modal = $this.closest('.tome-gallery-modal');

				// set slide number
				$modal.find('.gallery-pager .slideNumber').text(slideNum);

				// set total slide count
				$modal.find('.gallery-pager .totalSlides').text(slideCount);

				/* 2. set the caption and description for a slide */
				var $currentSlide = $this.find('.slick-current.slick-active');

				// grab title/subtitle text for the current slide
				var $title = $currentSlide.find('.slide-title').html() || "";
				var $caption = $currentSlide.find('.slide-caption').html() || "";
				var $subtitle = $currentSlide.find('.slide-description').html() || "";
				var $tags = $currentSlide.find('.slide-tags li');
				var hasTitle = $($.parseHTML( $title)).text().length > 0;
				var hasCaption = $($.parseHTML( $caption)).text().length > 0;
				var hasSubtitle = $($.parseHTML( $subtitle)).text().length > 0;

				// grab container to which we want to write the title/subtitle/tags, etc
				var $title_container = $modal.find('.sidebar .title');
				var $caption_container = $modal.find('.sidebar .caption');
				var $subtitle_container = $modal.find('.sidebar .description');
				var $tags_container = $modal.find('.sidebar .tags');


				// actually set title for this slide in the sidebar
				if (hasTitle) {
					$title_container.html($title).show();
				}
				else {
					$title_container.hide().html($title);
				}

				// 
				// actually set caption for this slide in the sidebar
				if (hasCaption) {
					$caption_container.show().find('p').html($caption);
				}
				else {
					$caption_container.hide().find('p').html($caption);
				}


				// set description for this slide in the sidebar.
				if (hasSubtitle) {
					$subtitle_container.show().find('p').html($subtitle);
				}
				else {
					$subtitle_container.hide().find('p').html($subtitle);
				}

				// 3. populate tags
				$tags_container.empty().append($tags.clone());


				// 4. set portrait/landscape (default styling is landscape) --- commented out because we are now statically setting these classes on template load.
				//var $viewerInner = $modal.find('.viewer-inner');
				//var $aspectClass =  $currentSlide.find("[data-img-type]").first().attr("data-img-type") || "";
				//$currentSlide.removeClass("portrait landscape").addClass($aspectClass);


				// 98. resize hack for portrait.
				var $sl = $modal.find(".viewer");
				$sl.find(".portrait > img").height($sl.height()).css('width', 'auto');

				// 99. give slick a little kick in the butt to force a resize. particularly helpful when the slide is some type of embedded media.
				$(window).resize();
			}
		);

	};




	// init slick sliders.
	//initSlick();

	// when we click a gallery front-image, open the gallery




	$("a.modal-trigger").click(function(){
		var modalId = $(this).attr("data-modal-id");
		var $modal = $("#" + $(this).attr("data-modal-id"));
		$modal.addClass("active").fadeIn(500);
		$("body").css({ overflow: 'hidden' })
		initActiveGalleryModal(); // re-init slick, otherwise the slide sizing is wrong.
	});

	// when we click the "X" of a gallery, close it.
	$("button.viewer-close").click(function(){
		var $modal = $(this).closest('.tome-gallery-modal');
		$modal.removeClass("active").fadeOut(500);
		$("body").css({ overflow: 'inherit' })
	});

	// when we hit escape, close any open gallery.
	$(document).on('keyup',function(evt) {
		// escape
		if (evt.keyCode == 27) {
			$('.tome-gallery-modal.active').removeClass("active").fadeOut(250);
		}

		// left
		if (evt.keyCode == 37) {
			$('.tome-gallery-modal.active .slick-initialized').slick('slickPrev');
		}

		// right
		if (evt.keyCode == 39) {
			$('.tome-gallery-modal.active .slick-initialized').slick('slickNext');
		}
	});
})(jQuery);
