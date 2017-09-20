  jQuery(document).ready(function($) {


  // loadIsotope();
  // tagsSelectbox();
  // openTagsClound();
  // selectTag();


  window.lazySizesConfig = {
    addClasses: true
  };




  $('.isotope-wrapper').each(function(){

    $('.tag-filters').select2();

    $container = $('.isotope-box');



    $container.isotope({
      itemSelector: '.isotope-item',
      layoutMode: 'masonry',
    });


      $('.isotope-wrapper').lightGallery({
        selector: '.isotope-item a',
        thumbnail: false
      }); 

    $(window).load(function() {
      $('.isotope-box.loading').removeClass('loading');
    });


    $container.isotope('on', 'layoutComplete', function(event, filteredItems) {

        // var firstLoad = $(event.element).hasClass('loading')

        $('.isotope-wrapper .visible').removeClass('visible');

        $(filteredItems).each(function(index, el) {
          $(filteredItems[index].element).find('a').addClass('visible');
        });
        

        $('.isotope-wrapper').data('lightGallery').destroy( true );

        $('.isotope-wrapper').lightGallery({
          selector: '.isotope-item .visible',
          thumbnail: false
        }); 

        

    });


    /*==========================================
    =            Tags functionality            =
    ==========================================*/
    $('.show-tags').click(function(event) {
      $('.tags-cloud').slideToggle();
    });



    $('.tag-filters').on( 'change', function(event) {


      var selectedTags = $(this).val(),
          filterString = ''
          i = 0;

      $('.tags-cloud .active').removeClass('active');

      // Build Filter String out of selected tags
      $(selectedTags).each(function(index, el) {

        $('.tags-cloud').find('.tag[data-tag="'+el+'"]').addClass('active');

        if ( i !== 0 ) {
          filterString += ', ' + el;
        } else {
          filterString += el;
        }

        i++;
      });


      $container.isotope({
        filter: filterString
      })


    });


    $('.tags-cloud .tag').click(function() {
      $(this).toggleClass('active');

      var selectVals = [];
      $('.tags-cloud').find('.active').each(function(index, el) {
        var tagValue = $(el).attr('data-tag');
        selectVals.push( tagValue );
      });
      $('.tag-filters').val( selectVals ).trigger('change');

      // $('.tags-cloud').find('.active')
    });
  });




});
