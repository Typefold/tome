

( function( $ ) {

    // Wrap the render() function to append controls
    wp.media.view.EmbedLink = wp.media.view.EmbedLink.extend({
        initialize: function() {
            wp.media.view.Settings.prototype.render.apply( this, arguments );

            // this.model.attributes.url = this.model.get('src');

            // this.model.trigger( 'change:url' );

            this.$el.append( wp.media.template( 'custom-embed-media-setting' ) );

            this.$el.find('.media-size').val( this.model.get('size') || 'full-column' )

            this.$el.find('.link-text').css('display', 'none');

            this.listenTo( this.model, 'change:size', this.triggerChange );

        },

        triggerChange: function() {
            var tempUrl = this.model.get('url');
            this.model.set( 'url', '' );
            this.model.trigger( 'change:url' );
            this.model.set( 'url', tempUrl );
        }
    } );


} )( jQuery );