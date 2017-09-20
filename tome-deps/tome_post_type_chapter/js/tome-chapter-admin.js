jQuery(function(){

	var s = jQuery("fieldset.chapter-header-options input:radio[name=tome_chapter_header_option]:checked");

	var an = jQuery("fieldset.chapter-header-options input:checkbox[name=tome-chapter-header-author]:checked");

	var onHeaderOptionChange = function(a) {

		a = typeof a !== 'undefined' ? a : jQuery(s).val();

		jQuery(".tome-chapter-head-option").removeClass('active');
		
		if(a == "embed") {
			jQuery("fieldset#media-selector").addClass('active');
		}
		
		if(a == "place") {
			jQuery("fieldset#place-selector").addClass('active');
		}
		
		if(a == "allplaces") {
			jQuery("div#multiplace-warning").addClass('active');
		}
	};

	var onAuthorChange = function(aa) {
		aa = typeof aa !== 'undefined' ? aa : jQuery(an).attr("checked");

		jQuery(".tome-chapter-head-author-name").removeClass('active');
		
		if(aa == "checked")
			jQuery("fieldset#author-name").addClass('active');

	}



	onHeaderOptionChange();
	onAuthorChange();
	
	jQuery("fieldset.chapter-header-options input:radio[name=tome_chapter_header_option]").change(function(e) {
		jQuery("fieldset.chapter-header-options input:radio[name=tome_chapter_header_option]").removeAttr("checked");
		jQuery(e.currentTarget).attr("checked", "checked");

		var newVal = jQuery(e.currentTarget).attr("value");
		onHeaderOptionChange(newVal);
		// if(newVal == "embed") {
		// 	jQuery("fieldset#media-selector").addClass('active');
		// }
		// if(newVal == "place") {
		// 	jQuery("fieldset#place-selector").addClass('active');
		// }

	});

	jQuery("fieldset.chapter-header-options input:checkbox[name=tome-chapter-header-author]").change(function(e) {

		var newVal = jQuery(e.currentTarget).attr("checked");
		onAuthorChange(newVal);


	});
});