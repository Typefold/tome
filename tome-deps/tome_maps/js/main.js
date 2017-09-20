
jQuery(document).ready(function($) {


	$('.tome-modal.general').on( 'click', '.map-item', function() {

		mapId = $(this).attr('id');
		window.activeModal.closeModal('tome-maps-modal');
		tinymce.execCommand('mceInsertContent', 0, '[tome_map id="'+mapId+'"]');


	});

	
});
