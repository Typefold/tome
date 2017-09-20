(function () {

	var TomeVideoCover,
		bind = function (fn, me) {
			return function () {
				return fn.apply(me, arguments);
			};
		};

	TomeVideoCover = function () {

		function TomeVideoCover(videoEl) {
			this.videoEl = videoEl;
			this.loadedmetadata = bind(this.loadedmetadata, this);
			this.fit = bind(this.fit, this);
			this.videoEl.addEventListener('loadedmetadata', this.loadedmetadata)
		}

		TomeVideoCover.prototype.loadedmetadata = function () {
			this.fit();
		};

		TomeVideoCover.prototype.fit = function () {

			var inner = { width : this.videoEl.videoWidth, height : this.videoEl.videoHeight },
				outer = { width : this.videoEl.parentNode.parentNode.clientWidth, height : this.videoEl.parentNode.parentNode.clientHeight }, //{ width : window.innerWidth, height : window.innerHeight },
				innerAspectRatio = inner.width / inner.height,
				outerAspectRatio = outer.width / outer.height,
				resizeFactor = (innerAspectRatio <= outerAspectRatio) ? outer.width / inner.width : outer.height / inner.height,
				newWidth = Math.round(inner.width * resizeFactor),
				newHeight = Math.round(inner.height * resizeFactor);
        };

        return TomeVideoCover;
    }();

	(function($){
		var $tomeVideoCoverEl = $('[data-videocover]');
		//var $coverVideos = $('video.tome-cover-video');
		var TomeVideoCovers = [];

		function initTomeCoverVideos() {
			if(window.innerWidth <= 700) {

				//Too small, just set the fallback image to fill the background.

				$tomeVideoCoverEl.each(function(){

					var videoData = $(this).data();

					$(this).parent().css({
						backgroundImage : 'url('+videoData.fallback+')',
						backgroundSize : 'cover',
						backgroundPosition : 'center center'
					});
				});

			} else {

				//create a video player per div found
				$tomeVideoCoverEl.each(function(){

					//Store the data from the placeholder element
					var videoData = $(this).data();
					
					//Build up the <video> markup
					var newVideoMarkup = '<video autoplay loop controls="false" class="tome-cover-video">';
					if(videoData.fallback) {
						newVideoMarkup = newVideoMarkup + " poster=\""+videoData.fallback+"\""
					}
					newVideoMarkup = newVideoMarkup;

					if(videoData.webm) {
						newVideoMarkup = newVideoMarkup + "<source src=\""+videoData.webm+"\" type=\"video/webm\"/>"
					}
					if(videoData.ogv) {
						newVideoMarkup = newVideoMarkup + "<source src=\""+videoData.ogv+"\" type=\"video/ogg\"/>"
					}
					if(videoData.mp4) {
						newVideoMarkup = newVideoMarkup + "<source src=\""+videoData.mp4+"\" type=\"video/mp4\"/>"
					}


					newVideoMarkup = newVideoMarkup + "<h1>Your browser does not support the video tag.</h1>";
					
					newVideoMarkup = newVideoMarkup + "</video>";

					$('body').prepend( $( newVideoMarkup ) );

					//Create the new element using jquery
					var $newVideo = $(newVideoMarkup);

					//Append it to the DOM
					// $(this).append( newVideoMarkup );

					//Create a TomeVideoCover with it
					TomeVideoCovers.push( new TomeVideoCover($newVideo[0]) );
				});

				$(window).on('resize', function(){
					for (var i = 0; i < TomeVideoCovers.length; i++) {
						TomeVideoCovers[i].fit();
					}
				});
			}
		}

		initTomeCoverVideos();


	})(jQuery);

}.call(this));