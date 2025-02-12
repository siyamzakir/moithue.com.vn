( function( $ ) {
    'use strict';

    var ajaxurl = Houzez_admin_vars.ajax_url;
    var processing_text = Houzez_admin_vars.processing_text;
    var delete_btn_text = Houzez_admin_vars.delete_btn_text;
    var confirm_btn_text = Houzez_admin_vars.confirm_btn_text;
    var cancel_btn_text = Houzez_admin_vars.cancel_btn_text;
    var map_fields_text = Houzez_admin_vars.map_fields_text;
    var error_import_text = Houzez_admin_vars.error_import;
    var select_text = Houzez_admin_vars.select_text;
    var import_text = Houzez_admin_vars.import_text;

    $( function() {

        $('.houzez-clone').cloneya();

        $( '.houzez-fbuilder-js-on-change' ).change( function() {
            var field_type = $( this ).val();
            $('.houzez-clone').cloneya();

            if(field_type == 'select' || field_type == 'multiselect' ) {
                $.post( ajaxurl, { action: 'houzez_load_select_options', type: field_type }, function( response ) {
                    $( '.houzez_select_options_loader_js' ).html( response );
                    $('.houzez-clone').cloneya();
                } );

            } else if(field_type == 'checkbox_list' || field_type == 'radio' ) {
                $( '.houzez_multi_line_js' ).show();
                $( '.houzez_select_options_loader_js' ).html('');

            } else {
                $( '.houzez_select_options_loader_js' ).html('');
                $( '.houzez_multi_line_js' ).hide();
            }
        } );

        $(window).on('load', function() {
            var current_option = $('.houzez-fbuilder-js-on-change').attr('value');

            if( current_option == 'checkbox_list' || current_option == 'radio' ) {
                $( '.houzez_multi_line_js' ).show();
            }
        });


    } );

    function HouzezStringToSlug( str ) {
        // Trim the string
        str = str.replace( /^\s+|\s+$/g, '' );
        str = str.toLowerCase();

        // Remove accents
        var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;",
            to = "aaaaeeeeiiiioooouuuunc------",
            i, l;

        for ( i = 0, l = from.length; i < l; i ++ ) {
            str = str.replace( new RegExp( from.charAt( i ), 'g' ), to.charAt( i ) );
        }

        str = str.replace( /[^a-z0-9 -]/g, '' ) // remove invalid chars
                  .replace( /\s+/g, '-' ) // collapse whitespace and replace by -
                  .replace( /-+/g, '-' ); // collapse dashes

        return str;
    }


    $( document ).ready( function() {

        $('#fetch-locations-csv').on('click', function(e) {
            e.preventDefault();
            var selectedFile = $('#locations-csv-file').val();
            var $this = $(this);

            var $success = $('#locations-locations-success');
            var $error = $('#locations-locations-error');

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'get_locations_csv_headers',
                    file_name: selectedFile
                },
                beforeSend: function( ) {
                    $this.addClass('updating-message');
                    $success.html('');
                    $error.html('');
                },
                complete: function(){
                    $this.removeClass('updating-message');
                },
                success: function(response) {
                    if (response.success) {
                        // Call a function to display the field mapping interface
                        displayMappingInterface(response.data);
                    } else {
                        $error.html(response.data);
                    }
                },
                error: function() {
                    // Handle error
                }
            });
        });

        function displayMappingInterface(headers) {
            var mappingHtml = '<form id="locations-csv-form" class="fwl-form">';
            
            // Predefined database column names
            var dbColumns = ['country', 'state', 'city', 'area'];
            
            dbColumns.forEach(function(dbColumn) {
                mappingHtml += '<div class="field-wrap form-group"><label for="' + dbColumn + '">' + dbColumn.replace('_', ' ').charAt(0).toUpperCase() + dbColumn.replace('_', ' ').slice(1) + ' </label>';
                mappingHtml += '<select name="field_mapping[' + dbColumn + ']" id="' + dbColumn + '" class="form-field form-control">';
                
                mappingHtml += '<option value="">' + select_text + '</option>';

                // Add options for each CSV header
                headers.forEach(function(header) {
                    mappingHtml += '<option value="' + header + '">' + header + '</option>';
                });
                
                mappingHtml += '</select></div>';
            });
            
            mappingHtml += '<button id="run-locations-import" type="button" class="button button-primary field-wrap">'+import_text+'</button></form>';

            // Append the form to a container in your HTML
            $('#locations-mapping-container').html(mappingHtml);

            import_locations_csv();
        }

        function import_locations_csv() {
            $('#run-locations-import').on('click', function(e) {
                e.preventDefault();

                var $success = $('#locations-locations-success');
                var $error = $('#locations-locations-error');

                $success.html('');
                $error.html('');

                var $this = $(this);
                var allFieldsEmpty = true;
                $('#locations-csv-form select').each(function() {
                    if (this.value.trim() !== '') {
                        allFieldsEmpty = false;
                        return false; // Break the loop
                    }
                });

                if (allFieldsEmpty) {
                    $error.html(map_fields_text);
                    return; // Prevent form submission
                }

                // Assuming your form has an ID 'locations-csv-form'
                var formData = new FormData($('#locations-csv-form')[0]);
                formData.append('action', 'locations_process_field_mapping');
                formData.append('selected_csv_file', $('#locations-csv-file').val());

                $.ajax({
                    url: ajaxurl, // ajaxurl is already defined in WordPress admin
                    type: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        // Handle success
                        if( response.success ) {
                            $('#locations-mapping-container').html('');
                        }
                        $success.html(response.data);
                        console.log(response.data);
                    },
                    beforeSend: function( ) {
                        $this.addClass('updating-message');
                        $success.html('');
                        $error.html('');
                    },
                    complete: function(){
                        $this.removeClass('updating-message');
                    },
                    error: function(response) {
                        // Handle error
                        $error.html(response.data);
                    }
                });

            });
        }


        $('#upload-locations-csv').click(function(e) {
            e.preventDefault();

            var mediaUploader;

            if (mediaUploader) {
                mediaUploader.open();
                return;
            }

            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose a CSV File',
                button: {
                    text: 'Choose CSV File'
                },
                library: {
                    type: 'text/csv'
                },
                multiple: false
            });

            mediaUploader.on('select', function() {
                var attachment = mediaUploader.state().get('selection').first().toJSON();
                $('#locations-csv-file').val(attachment.url);
                $('#locations-mapping-container').html('');
            });

            mediaUploader.open();
        });

    });



} )( jQuery );