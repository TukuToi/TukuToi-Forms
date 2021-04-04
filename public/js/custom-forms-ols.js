(function($) {

$(document).ready( function() {
	var file_frame; // variable for the wp.media file_frame
	
	// attach a click event (or whatever you want) to some element on your page
	$( '#add_fr_media' ).on( 'click', function( event ) {
		
		event.preventDefault();

        // if the file_frame has already been created, just reuse it
		if ( file_frame ) {
			file_frame.open();
			return;
		} 

		file_frame = wp.media.frames.file_frame = wp.media({
			title: $( this ).data( 'uploader_title' ),
			button: {
				text: $( this ).data( 'uploader_button_text' ),
			},
			library: {
              type: 'image',
            },
			multiple: false // set this to true for multiple file selection
		});

		file_frame.on( 'select', function() {
			attachment = file_frame.state().get('selection').first().toJSON();
			if($("#postFeaturedImage").length > 0) {
				$( 'input#postFeaturedImage' ).attr('value', attachment.url);
				$( 'img#postFeaturedImage' ).attr('src', attachment.url);
				var text = document.getElementById('add_fr_media').firstChild;
   				text.data = "Changer l'image";
			}
			if($("#termAvatar").length > 0) {
				$( '#termAvatar' ).attr('value', attachment.url);
				$( '#termAvatar' ).attr('src', attachment.url);
			}
			if($("#termAvatarImg").length > 0) {
				$( '#termAvatarImg' ).attr('src', attachment.url);
			}
			if($("#repeating_input").length > 0) {
				$( '#repeating_input' ).attr('value', attachment.url);
				$( '#repeating_input' ).attr('src', attachment.url);
			}
		});

		file_frame.open();
	});
	select2_init_edit_forms();
	datepicker_init();
	hide_show_parent();
	change_label();
	var postTypeSelector = document.getElementById("postType");
	if(typeof postTypeSelector === 'object' && postTypeSelector !== null){
		var postTypeSelected = postTypeSelector.value;
		if(postTypeSelected == 'ressource'){
				jQuery('#categorySelect').val(['1530']);
				jQuery('#categorySelect').trigger('change');	
			}
		postTypeSelector.onchange = function(){
			postTypeSelected = postTypeSelector.value;
			if(postTypeSelected == 'ressource'){
				jQuery('#categorySelect').val(['1530']);
				jQuery('#categorySelect').trigger('change');	
			} else if(postTypeSelected == 'evenement'){
				jQuery('#categorySelect').val(['7']);
				jQuery('#categorySelect').trigger('change');	
			} else{
				jQuery('#categorySelect').val(null).trigger('change');

			}
		};
	}
	jQuery(".postform:not([name=parent])").on('select2:select', function (e) {
 		change_select2_title();
	});

	init_optgroups_select2();

	change_select2_title();
	
	validate_inputs();
});

})(jQuery);

function change_label(){
	
	if (jQuery('#postFeaturedImage').length > 0) {
		var elem = document.getElementById('postFeaturedImage');

		  if(elem.getAttribute('value') == "")
		  {
			//alert("empty");
		  }
		  else
		  {
			var text = document.getElementById('add_fr_media').firstChild;
			text.data = "Changer l'image";
		  }
	}
}

function select2_init_edit_forms() {
	jQuery(".postform:not([name=parent])").select2({
		placeholder : "Rechercher...",
		allowClear: true,
		width: '100%',
		templateResult: function (data) {    
			// We only really care if there is an element to pull classes from
			if (!data.element) {
			  return data.text;
			}

			var $element = jQuery(data.element);

			var $wrapper = jQuery('<span></span>');
			$wrapper.addClass($element[0].className);
			$wrapper.text(data.text);
			//jQuery( "<span class='text-right'>+</span>" ).appendTo( ".level-0" );

			return $wrapper;
		},
		tags: true,
		dropdownParent: jQuery('#front_end_add_edit_posts_container'),
		errorLoading:function(){return"Les résultats ne peuvent pas être chargés."},inputTooLong:function(e){var n=e.input.length-e.maximum;return"Supprimez "+n+" caractère"+(n>1?"s":"")},inputTooShort:function(e){var n=e.minimum-e.input.length;return"Saisissez au moins "+n+" caractère"+(n>1?"s":"")},loadingMore:function(){return"Chargement de résultats supplémentaires…"},maximumSelected:function(e){return"Vous pouvez seulement sélectionner "+e.maximum+" élément"+(e.maximum>1?"s":"")},noResults:function(){return"Aucun résultat trouvé"},searching:function(){return"Recherche en cours…"},removeAllItems:function(){return"Supprimer tous les éléments"},removeItem:function(){return"Supprimer l'élément"}
	});
	
}

function datepicker_init() {
	if (jQuery('#date').length > 0) {
		jQuery( "#date" ).datepicker({
			monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			prevText: '&lt;Préc',
    		nextText: 'Suiv&gt;',
			dateFormat: 'dd/mm/yy',
		});
		jQuery( "#dateDebut" ).datepicker({
			monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			prevText: '&lt;Préc',
    		nextText: 'Suiv&gt;',
			dateFormat: 'dd/mm/yy',
		});
		jQuery( "#dateFin" ).datepicker({
			monthNames: ['Janvier','Février','Mars','Avril','Mai','Juin',
    'Juillet','Août','Septembre','Octobre','Novembre','Décembre'],
			dayNamesMin: ['Di','Lu','Ma','Me','Je','Ve','Sa'],
			prevText: '&lt;Préc',
    		nextText: 'Suiv&gt;',
			dateFormat: 'dd/mm/yy',
		});
	}
}

function hide_show_parent() {
	jQuery( "#postType" ).change(function () {
		var sel = document.getElementById('postType');
		if (sel.value === 'publication'){
			jQuery("#post_parent_section").show(1000);
			jQuery(".toggle-col").removeClass('col-4');
			jQuery(".toggle-col").addClass('col-3');
		}
		else {
			jQuery("#post_parent_section").hide(1000);
			jQuery(".toggle-col").removeClass('col-3');
			jQuery(".toggle-col").addClass('col-4');
		}
  	}).change();
	jQuery( "#postType" ).change(function () {
		var sel = document.getElementById('postType');
		if (sel.value === 'evenement'){
			jQuery("#evenement_data").show(1000);
		}
		else {
			jQuery("#evenement_data").hide(1000);
		}
  	}).change();
}
function change_select2_title(){
	jQuery('.select2-selection__choice__remove').each(function() {
		jQuery(this).attr({
		  "title" : "Supprimer"
		});
	});
}
function validate_inputs(){
	  jQuery("#front_end_add_edit_posts_form").validate({
		// Specify validation rules
		rules: {
		  // The key name on the left side is the name attribute
		  // of an input field. Validation rules are defined
		  // on the right side
		  "postTitle": "required",
		  "langue[]": "required",
		  "date": "required",
	  	  "dateDebut": "required",
		  "category[]": "required",
		},
		// Specify validation error messages
		messages: {
		  "postTitle": "Ce champ est requis",
		  "langue[]": "Ce champ est requis",
		  "date": "Ce champ est requis",
	  	  "dateDebut": "Ce champ est requis",
		  "category[]": "Ce champ est requis",
		},
		// Make sure the form is submitted to the destination defined
		// in the "action" attribute of the form when valid
		submitHandler: function(form) {
		  form.submit();
		}
	  });
  }

function init_optgroups_select2(){
	let optgroupState = {};

	jQuery("body").on('click', '.select2-container--open .select2-results__group', function() {
	  jQuery(this).siblings().toggle();
	  let id = jQuery(this).closest('.select2-results__options').attr('id');
	  let index = jQuery('.select2-results__group').index(this);
	  optgroupState[id][index] = !optgroupState[id][index];
	});

	jQuery('.postform:not([name=parent])').on('select2:open', function() {
	  jQuery('.select2-dropdown--below').css('opacity', 0);
	  setTimeout(() => {
		let groups = jQuery('.select2-container--open .select2-results__group');
		let id = jQuery('.select2-results__options').attr('id');
		if (!optgroupState[id]) {
		  optgroupState[id] = {};
		}
		jQuery.each(groups, (index, v) => {
		  optgroupState[id][index] = optgroupState[id][index] || false;
		  optgroupState[id][index] ? jQuery(v).siblings().show() : jQuery(v).siblings().hide();
		})
		jQuery('.select2-dropdown--below').css('opacity', 1);
	  }, 0);
	});
}