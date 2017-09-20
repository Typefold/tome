(function($) {
    tinymce.create('tinymce.plugins.Tome', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('dropcap', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<span class="dropcap">' + selected_text + '</span>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });


            ed.addCommand('pullquote', function() {
                var node = ed.selection.getNode();

                if ( node.nodeName === "BODY" )
                    return;

                if ( ed.dom.getParent( node, '.pullquote' ) === null ) {
                    $(ed.selection.getNode()).wrap('<div class="pullquote"></div>');
                } else {
                    $(ed.selection.getNode()).unwrap().unwrap();
                }
            });

            ed.addCommand('blockquote', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<blockquote><p>' + selected_text + '</p></blockquote>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

            ed.addCommand('abstract', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '<span class="abstract">' + selected_text + '</span>';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

            ed.addCommand('cite', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '[citation post="INSERT_POST_ID" pgstart="" pgend=""]'+selected_text+'[/citation]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

            ed.addCommand('place', function() {
                var selected_text = ed.selection.getContent();
                var return_text = '';
                return_text = '[tome_place post="INSERT_POST_ID"]';
                ed.execCommand('mceInsertContent', 0, return_text);
            });

            ed.addCommand('showrecent', function() {
                var number = prompt("How many posts you want to show ? "), 
                    shortcode;
                if (number !== null) {
                    number = parseInt(number);
                    if (number > 0 && number <= 20) {
                        shortcode = '[recent-posts numbers="' + number + '"/]';
                        ed.execCommand('mceInsertContent', 0, shortcode);
                    } else {
                        alert("The number value is invalid. It should be from 0 to 20.");
                    }
                }      
            });
            
            ed.addButton('dropcap', {
                title : 'DropCap',
                cmd : 'dropcap',
                image : url + '/dropcap.png'
            });

            ed.addButton('tome_blockquote', {
                title : 'Block Quote',
                cmd : 'blockquote',
                image : url + '/blockquote.png'
            });

            ed.addButton('pullquote', {
                title : 'Pull Quote',
                cmd : 'pullquote',
                image : url + '/pullquote.png'
            });
            ed.addButton('abstract', {
                title : 'Abstract',
                cmd : 'abstract',
                image : url + '/abstract.png'
            });

            // ed.addButton('cite', {
            //     title : 'Insert a citation',
            //     cmd : 'cite',
            //     image : url + '/cite.png'
            // });

            // ed.addButton('place', {
            //     title : 'Insert a map of a place',
            //     cmd : 'place',
            //     image : url + '/map.png'
            // });

            // ed.addButton('showrecent', {
            //     title : 'Add recent posts shortcode',
            //     cmd : 'showrecent',
            //     image : url + '/recent.png'
            // });
        },

        /**
         * Creates control instances based in the incomming name. This method is normally not
         * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
         * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
         * method can be used to create those.
         *
         * @param {String} n Name of the control to create.
         * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
         * @return {tinymce.ui.Control} New control instance or null if no control was created.
         */
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                    longname : 'Tome Buttons',
                    author : 'Agustin Sevilla',
                    authorurl : 'http://typefold.com',
                    infourl : 'http://typefold.com/tome',
                    version : "0.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('typefoldtome', tinymce.plugins.Tome);
})(jQuery);