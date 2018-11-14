(function() {
    tinymce.create('tinymce.plugins.tn', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addButton('eventsbutton', {
                    title : name_btn_plugin,
                    cmd : 'eventsbutton',
                    image : url + '/tiny_mce_images/calendar-blue.png'
            });

            ed.addCommand('eventsbutton', function() {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'call_get_courses'
                    },
                    success: function ( resp ) {
                        var response = JSON.parse( resp.data );
                        if ( resp.success ) {

                            tinymce.activeEditor.windowManager.open( {
                                title: response.popup_data.text_add_tn_academy_course,
                                width: 440,
                                height: 220,
                                id: 'tn-academy-popup-window',
                                body: [
                                    {
                                        type: 'textbox',
                                        name: 'title',
                                        label: response.popup_data.course_name + ':'
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'text',
                                        label: response.popup_data.text + ':'
                                    },
                                    {
                                        type: 'textbox',
                                        name: 'linkText',
                                        label: response.popup_data.link_text + ':'
                                    },
                                    {
                                        type: 'listbox',
                                        name: 'course',
                                        label: response.popup_data.link + ':',
                                        values : response.data
                                    },
                                    {
                                        type: 'container',
                                        name: 'container',
                                        label: '',
                                        layout: 'grid',
                                        html : response.popup_data.msg_new_course_how_to
                                    }
                                ],
                                onsubmit: function( e ) {
                                    var title;
                                    var text;
                                    var linkText;
                                    var link;
                                    var html;
                                    response.data.forEach( function (element) {
                                        if(element.value === e.data.course){
                                            title = ((e.data.title !== "") ? e.data.title : element.text);
                                            text = ((e.data.text !== "") ? e.data.text : element.content);
                                            linkText = ((e.data.linkText !== "") ? e.data.linkText : element.link);
                                            link = element.link;
                                        }
                                    } );
                                    html = '<div class="events-button"><h3 class="events-button-title">' + title + '</h3>' +
                                        '<p class="events-button-text">' + text + '</p>' +
                                        '<div class="events-button-link"><a href="' + link + '">' + linkText + '</a></div></div>';
                                    ed.execCommand('mceInsertContent', 0, html);
                                }
                            });
                        } else {
                            // No courses found
                            tinymce.activeEditor.windowManager.open({
                                title: response.popup_data.text_add_tn_academy_course,
                                width: 440,
                                height: 220,
                                id: 'tn-academy-popup-window',
                                body: [{
                                    type   : 'container',
                                    name   : 'container',
                                    id : 'tn-academy-no-forms-notice',
                                    label : '',
                                    layout: 'grid',
                                    html   : response.popup_data.msg_no_courses
                                }]
                            });
                        }
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        console.log( 'There was an error during the AJAX call to WordPress Ajax: ' + xhr );
                    },
                }) ;
            });
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
                longname : 'TN Buttons',
                author : 'Sergey Nosenko',
                authorurl : '',
                infourl : '',
                version : "0.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'tn', tinymce.plugins.tn );
})();