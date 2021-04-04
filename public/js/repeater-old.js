var repeatable_field = {
    init: function(){
        this.addRow();
        this.removeRow();
        this.addImageUploader();
        this.removeImage();
         this.dragnDrop();
    },
     dragnDrop: function(){

        jQuery("#tktforms-sortable").sortable();
       
        jQuery("#tktforms-sortable").disableSelection();
    },
    addRow: function(){
        jQuery(document).on('click', '#add-row', function (e) {
            e.preventDefault();
            var row = jQuery('.empty-row.screen-reader-text').clone(true);
            row.removeClass('empty-row screen-reader-text');
            row.insertBefore('#repeatable-fieldset-one tbody>tr:last');
            // return false;
        });
    },
    removeRow: function(){
        jQuery(document).on('click', '.remove-row', function () {
            jQuery(this).parents('tr').remove();
            return false;
        });
    },
    addImageUploader: function(){
        jQuery(document).on('click', '.tktforms-upload_image_button', function (event) {
            event.preventDefault();

            var inputField = jQuery(this).prev('.nts-repeater_instance');

            // Create the media frame.
            var pevent = event,
                button = jQuery(this),
                file_frame = wp.media({
                    title: jQuery( this ).data( 'uploader_title' ),
                    
                    button: {
                        text: jQuery( this ).data( 'uploader_button_text' )
                    },
                    multiple: false
                }).on('select', function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();

					if (attachment.hasOwnProperty('sizes')){
						console.log(attachment);
                    	var attachment_thumbnail = attachment.sizes.thumbnail || attachment.sizes.full;
					}
					else{
						
                    	var attachment_thumbnail = {
						  url: '/wp-content/plugins/tukutoi-forms/assets/generic-file-placeholder.png',
						};
						console.log(attachment_thumbnail);
					}

                    button.closest('.tktforms-repeater-repeater_instance-wrapper').find('.tktforms-repeater_instance').val(attachment.id);
                    button.closest('.tktforms-repeater-repeater_instance-wrapper').find('.tktforms-repeater_instance').before('<div class="row"><div class="col-md-3"><img src="' + attachment_thumbnail.url + '" width="150px" height="150px" /></div><div class="col-md-9"><h3 class="RepeaterFileName">' + attachment.filename + '</h3></div></div>');
                    button.closest('.tktforms-repeater-repeater_instance-wrapper').find('.tktforms-remove_image_button').show();
                    button.hide();

                }).open();
        });
    }, 

    removeImage: function(){
        jQuery(document).on('click', '.tktforms-remove_image_button', function (event) {
            event.preventDefault();
            jQuery(this).closest('.tktforms-repeater-repeater_instance-wrapper').find('.tktforms-repeater_instance').val('');
            jQuery(this).closest('.tktforms-repeater-repeater_instance-wrapper').find('.tktforms-upload_image_button').show();
            jQuery(this).hide();
            jQuery(this).closest('.tktforms-repeater-repeater_instance-wrapper').find('div').remove();

        });
    }

};


jQuery(document).ready(function ($) {
   repeatable_field.init();
});
