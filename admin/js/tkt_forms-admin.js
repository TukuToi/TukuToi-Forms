(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).on.(load, function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	/**
	 * Make all variables available to scope
	 *
	 * @var tkt_forms_add 					The main "Add" Button
	 * @var tkt_forms_edit 					The main "Edit" Button
	 * @var tkt_forms_notifications 		The main "Add/Edit Notifications" Button
	 * @var tkt_forms_add_modal				The main "Add" Modal 
	 * @var tkt_forms_edit_modal 			The main "Edit" Modal 
	 * @var tkt_forms_notifications_modal 	The main "Add/Edit Notifications" Modal
	 * @var tkt_forms_add_new_notification 	The single "Add Notification" Trigger in the main "Add/Edit Notifications" Modal
	 * @var tkt_edit_forms_triggers			The single "Edit Form" Triggers in the main "Edit" Modal
	 * @var tkt_forms_notification_triggers The single "Add/Edit/Delete" Triggeres in the main "Add/Edit Notifications" Modal
	 * @var tkt_forms_codeditor_post 		The single "Add Post Form" Trigger in the main "Add" Modal
	 * @var tkt_forms_codeditor_term 		The single "Add Term Form" Trigger in the main "Add" Modal
	 * @var tkt_forms_codeditor_user 		The single "Add User Form" Trigger in the main "Add" Modal
	 * @var tkt_forms_codeditor_post_modal	The postform "Code Editor" Modal
	 * @var tkt_forms_notification_modal 	The notifications "Code Editor" Modal
	 * @var close_tkt_forms_add_modal 		The Close Button for "Add" Modal
	 * @var close_tkt_forms_edit_modal 		The Close Button for "Edit" Modal
	 * @var close_tkt_forms_codeditor_modal	The Close Button for the "Post" Code Editor Modal
	 * @var close_tkt_forms_notif_modal 	The Close Button for the "Notifications" Code Editor Modal
	 */
	var tkt_forms_add;
	var tkt_forms_edit;
	var tkt_forms_notifications;
	var tkt_forms_add_modal;
	var tkt_forms_edit_modal;
	var tkt_forms_notifications_modal;
	var tkt_forms_add_new_notification;
	var tkt_edit_forms_triggers;
	var tkt_forms_notification_triggers;
	var tkt_forms_codeditor_post;
	var tkt_forms_codeditor_post_modal;
	var tkt_forms_notification_modal;
	var close_tkt_forms_notif_code_modl;
	var close_tkt_forms_add_modal;
	var close_tkt_forms_edit_modal;
	var close_tkt_forms_codeditor_modal;
	var close_tkt_forms_notif_modal;
	var tkt_delete_forms_triggers;
    
	/**
	 *The document ready event fired when the HTML document is loaded and the DOM is ready, 
	 *even if all the graphics haven’t loaded yet. 
	 *If you want to hook up your events for certain elements before the window loads, 
	 *then $(document).ready is the right place.
	 */
	$(document).on('ready', function() {
	    // document is loaded and DOM is ready
	    //alert("Document is ready");
	});
	/**
	*The window load event fired a bit later, when the complete page is fully loaded, 
	*including all frames, objects and images. 
	*Therefore functions which concern images or other page contents 
	*should be placed in the load event for the window or the content tag itself.
	*/
	$(window).on('load', function() {
	    // page is fully loaded, including all files, objects and images
	    //alert("Window is loaded");

	    /**
		 * Populate the necessary variables
		 *
		 * @var tkt_forms_add 					Element 	The main "Add" Button
		 * @var tkt_forms_edit 					Element 	The main "Edit" Button
		 * @var tkt_forms_add_modal				Element 	The main "Add" Modal 
		 * @var tkt_forms_edit_modal 			Element 	The main "Edit" Modal 
		 * @var tkt_edit_forms_triggers 		Array 		The single "Edit Form" Triggers in the main "Edit" Modal
		 * @var tkt_forms_codeditor_post 		Element 	The single "Add Post Form" Trigger in the main "Add" Modal
		 * @var tkt_forms_codeditor_post_modal	Element 	The postform "Code Editor" Modal
		 * @var close_tkt_forms_add_modal 		Element 	The Close Button for "Add" Modal
		 * @var close_tkt_forms_edit_modal 		Element 	The Close Button for "Edit" Modal
		 * @var close_tkt_forms_codeditor_modal	Element 	The Close Button for the "Post" Code Editor Modal
		 */
	    tkt_forms_add					= document.getElementById("tkt_forms_add");
	    tkt_forms_edit					= document.getElementById("tkt_forms_edit");
	    tkt_forms_notification 			= document.getElementById("tkt_forms_notification");
	    tkt_forms_add_modal 			= document.getElementById("tkt_forms_add_modal");
	    tkt_forms_edit_modal 			= document.getElementById("tkt_forms_edit_modal");
	    tkt_forms_notifications_modal   = document.getElementById("tkt_forms_notifications_modal");
	    tkt_forms_add_new_notification 	= document.getElementById("tkt_forms_add_new_notification");
	    tkt_edit_forms_triggers 		= document.getElementsByClassName("tkt_forms_edit_form");
	    tkt_forms_notification_triggers = document.getElementsByClassName("tkt_forms_notifications");
	    tkt_forms_codeditor_post		= document.getElementById("tkt_forms_codeditor_post");
	    tkt_forms_codeditor_post_modal 	= document.getElementById("tkt_forms_codeditor_post_modal");
	    tkt_forms_notification_modal 	= document.getElementById("tkt_forms_notification_code_modal");
		close_tkt_forms_add_modal 		= document.getElementById("tkt_forms_add_modal_close");
		close_tkt_forms_edit_modal 		= document.getElementById("tkt_forms_edit_modal_close");
		close_tkt_forms_codeditor_modal = document.getElementById("tkt_forms_codeditor_modal_close");
		close_tkt_forms_notif_modal 	= document.getElementById("tkt_forms_notifications_modal_close");
		close_tkt_forms_notif_code_modl = document.getElementById("tkt_forms_notification_codeditor_modal_close");
		tkt_delete_forms_triggers 		= document.getElementsByClassName("tkt_forms_delete_triggers");

		/**
		 * Open main "Add" modal
		 */
		tkt_forms_add.onclick = function(){
			tkt_forms_add_modal.style.display = "flex";
		}
		/**
		 * Open postform "Code Editor" modal when from main "Add" modal
		 */
		tkt_forms_codeditor_post.onclick = function(){
			
			render_codeditor_post_modal();
			tkt_forms_add_modal.style.display = "none";

		}
		/**
		 * Open notifications "Code Editor" modal when from main "Add/Edit Notifications" modal
		 */
		tkt_forms_add_new_notification.onclick = function(){
			
			render_codeditor_notifications_modal();
			tkt_forms_notifications_modal.style.display = "none";

		}
		/**
		 * Open main "Edit" modal
		 * Once opened, add clickevent to The single "Edit Form" Triggers in the main "Edit" Modal
		 * For each of The single "Edit Form" Triggers in the main "Edit" Modal, once clicked call Form data with AJAX
		 */
		tkt_forms_edit.onclick = function(){
			tkt_forms_edit_modal.style.display = "flex";			
			for( var i=0; i < tkt_edit_forms_triggers.length; i++ ){
				tkt_edit_forms_triggers.item(i).onclick = function(event){

					// add the spinner is-active class before the Ajax posting only for the clicked item
         			$("#" + this.getAttribute("id") + ".spinner").addClass("is-active"); 
         			//Get the form data to be edited 
				    get_form_ajax(this);
					
				}
				
			}
			for( var i=0; i < tkt_delete_forms_triggers.length; i++ ){
				tkt_delete_forms_triggers.item(i).onclick = function(event){

					// add the spinner is-active class before the Ajax posting only for the clicked item
         			//$("#" + this.getAttribute("id") + ".spinner").addClass("is-active"); 
         			//Get the form data to be edited 
				    delete_form_ajax(this);
					
				}
				
			}
		}
		/**
		 * Open main "Add/Edit Notifications" modal
		 * Once opened, add clickevent to The single "Edit Notification" Triggers in the main "Add/Edit Notifications" modal
		 * For each of The single "Edit Notification" Triggers in the main "Add/Edit Notifications" modal, 
		 * once clicked call Form data with AJAX
		 */
		tkt_forms_notification.onclick = function(){
			tkt_forms_notifications_modal.style.display = "flex";			
			for( var i=0; i < tkt_forms_notification_triggers.length; i++ ){
				tkt_forms_notification_triggers.item(i).getElementsByClassName('notification_edit_trigger')[0].onclick = function(event){

					// add the spinner is-active class before the Ajax posting only for the clicked item
					//var single_id = this.getAttribute("id").substring(this.getAttribute("id").indexOf("_") + 1);
         			$("#" + this.getAttribute("id") + ".spinner").addClass("is-active"); 
         			//Get the form data to be edited 
				    get_form_ajax(this);
					
				}
				
			}
		}
		/**
		 * Close postform "Code Editor" modal
		 */
		close_tkt_forms_codeditor_modal.onclick = function(){
			tkt_forms_codeditor_post_modal.style.display = "none";
		}
		/**
		 * Close notifications "Code Editor" modal
		 */
		close_tkt_forms_notif_code_modl.onclick = function(){
			tkt_forms_notification_modal.style.display = "none";
		}
		/**
		 * Close main "Add" modal
		 */
		close_tkt_forms_add_modal.onclick = function() {
		  	tkt_forms_add_modal.style.display = "none";
		}
		/**
		 * Close main "Edit" modal
		 */
		close_tkt_forms_edit_modal.onclick = function() {
		  	tkt_forms_edit_modal.style.display = "none";
		}
		/**
		 * Close main "Add/Edit Notifications" modal
		 */
		close_tkt_forms_notif_modal.onclick = function() {
		  	tkt_forms_notifications_modal.style.display = "none";
		}
		/**
		 * Open user clicks outside of any modal, close it
		 */
		window.onclick = function(event) {
		  if (event.target == tkt_forms_add_modal || event.target == tkt_forms_edit_modal || event.target == tkt_forms_notifications_modal) {
		    tkt_forms_add_modal.style.display = "none";
		    tkt_forms_edit_modal.style.display = "none";
		    tkt_forms_notifications_modal.style.display = "none";
		  }
		}	
		/**
		 * Prevent the Submit KeyBoard button to actually submit the "Code Editor" forms
		 */
		$('form input').keydown(function (event) {
		    if (event.keyCode == 13) {
		        event.preventDefault();
		        return false;
		    }
		});
		/**
		 * Save formdata when submitted
		 */
		$( 'form[name="tkt_forms_add_form"]' ).on( 'submit', function() {
			// add the spinner is-active class before the Ajax posting
         	$(".spinner").addClass("is-active"); 
			update_form_ajax(this);
			return false;
		});
		/**
		 * Save notificationdata when submitted
		 */
		$( 'form[name="tkt_forms_add_notification"]' ).on( 'submit', function() {
			// add the spinner is-active class before the Ajax posting
         	$(".spinner").addClass("is-active"); 
			update_form_ajax(this);
			return false;
		});

		/**
		 * Function to render postform "Code Editor" modal to fullscreen
		 */
		function render_codeditor_post_modal(){
			tkt_forms_codeditor_post_modal.style.display = "flex";
			tkt_forms_codeditor_post_modal.style.zIndex = '99999';
			tkt_forms_codeditor_post_modal.getElementsByClassName('tkt_modal_wrap')[0].style.width = "100vw";
			tkt_forms_codeditor_post_modal.getElementsByClassName('tkt_modal_wrap')[0].style.height = "100vh";
			tkt_forms_codeditor_post_modal.getElementsByClassName('tkt_modal_wrap')[0].getElementsByClassName('tkt_modal_content')[0].style.height = '93%';
			tkt_forms_codeditor_post_modal.getElementsByClassName('tkt_modal_wrap')[0].getElementsByClassName('tkt_modal_content')[0].style.padding = '32px 25vw 32px 25vw';
		}
		/**
		 * Function to render notifications "Code Editor" modal to fullscreen
		 */
		function render_codeditor_notifications_modal(){
			tkt_forms_notification_modal.style.display = "flex";
			tkt_forms_notification_modal.style.zIndex = '99999';
			tkt_forms_notification_modal.getElementsByClassName('tkt_modal_wrap')[0].style.width = "100vw";
			tkt_forms_notification_modal.getElementsByClassName('tkt_modal_wrap')[0].style.height = "100vh";
			tkt_forms_notification_modal.getElementsByClassName('tkt_modal_wrap')[0].getElementsByClassName('tkt_modal_content')[0].style.height = '93%';
			tkt_forms_notification_modal.getElementsByClassName('tkt_modal_wrap')[0].getElementsByClassName('tkt_modal_content')[0].style.padding = '32px 25vw 32px 25vw';
		}

		/**
		 * Ajax method to get formdata by ID and populate "Code Editor" areas.
		 */
		function get_form_ajax($event_target){

			$.ajax({
				url : tkt_forms_ajax_data.ajax_url,
				type : 'get',
				data : {
					'action':'db_get_form_ajax',
					'form_id': $event_target.getAttribute("id"),
					'tkt_forms_admin_ajax_secure' : tkt_forms_ajax_data.ajax_nonce
				},
				success : function( response ) {
					//Parse JSON String
					response = JSON.parse(response);
					//Hide Main "Edit Forms" modal
					tkt_forms_edit_modal.style.display = "none";
					//The postform "Code Editor" Modal
					render_codeditor_post_modal();
					//Populate the Title and Codemirror instances with values from AJAX object
					document.getElementById("form_name").value = response.form_title;
					document.querySelectorAll('.CodeMirror')[0].CodeMirror.setValue(response.form_html);
					document.querySelectorAll('.CodeMirror')[1].CodeMirror.setValue(response.form_js);
					document.querySelectorAll('.CodeMirror')[2].CodeMirror.setValue(response.form_css);
					document.getElementById('form_html').value = response.form_html;
					document.getElementById('form_js').value = response.form_js;
					document.getElementById('form_css').value = response.form_css;
					//We are done, remove the spinner
					$(".spinner").removeClass("is-active");
				},
				fail : function( err ) {
					//Ouput some feedback if it fails
					alert( "There was an error: " + err );
				}
			});
			
			//Make sure the hidden "value" of field_id is populated in The postform "Code Editor" Modal
			document.getElementById("form_id").value = $event_target.getAttribute("id");
			
			// This return prevents the submit event to refresh the page.
			return false;
		}

		function update_form_ajax($formdata){
			//get POSTed form data
		    var form_data = $( $formdata ).serializeArray();
		    //Push the NONCE to the POSTed formdata
		    form_data.push( { "name" : "tkt_forms_admin_ajax_secure", "value" : tkt_forms_ajax_data.ajax_nonce } );
		 	
		    $.ajax({
		        url : tkt_forms_ajax_data.ajax_url,
		        type : 'post',
		        data : form_data,
		        success : function( response ) {
		        	//The form was saved
		        	$(".spinner").removeClass("is-active");
		        	//Nice funky feedback
		        	if(response == 'Form saved!'){
			        	$('.tkt_feedback').addClass('tkt_success').css("display", "inline-flex").hide().text(response).fadeIn(1618, function() {
			        		setTimeout(function () {

						        $('.tkt_feedback').fadeOut(1618, function() {
						        	$('.tkt_feedback').removeClass('tkt_success').text('');
						        });

						    }, 2617);
						});
			    	}
			    	else{
			    		$('.tkt_feedback').addClass('tkt_error').css("display", "inline-flex").hide().text(response).fadeIn(1618, function() {
			        		setTimeout(function () {

						        $('.tkt_feedback').fadeOut(1618, function() {
						        	$('.tkt_feedback').removeClass('tkt_error').text('');
						        });

						    }, 2617);
						});
			    	}

		        },
		        fail : function( err ) {
		        	// add the spinner is-active class before the Ajax posting
		            $(".spinner").removeClass("is-active");
		            //Nice fancy response
		           	$('.tkt_feedback').addClass('tkt_error').css("display", "inline-flex").hide().text(err).fadeIn(1618, function() {
		        		setTimeout(function () {

					        $('.tkt_feedback').fadeOut(1618, function() {
					        	$('.tkt_feedback').removeClass('tkt_error').text('');
					        });

					    }, 2617);
					});
		        }
		    });
		     
		    // This return prevents the submit event to refresh the page.
		    return false;
		}

		/**
		 * Ajax method to delete form by ID
		 */
		function delete_form_ajax($event_target){

			var single_id = $event_target.getAttribute("id").substring($event_target.getAttribute("id").indexOf("_") + 1);
			single_id = single_id.substring(single_id.indexOf("_") + 1);
			$.ajax({
				url : tkt_forms_ajax_data.ajax_url,
				type : 'get',
				data : {
					'action':'db_delete_form_ajax',
					'form_id': single_id,
					'tkt_forms_admin_ajax_secure' : tkt_forms_ajax_data.ajax_nonce
				},
				success : function( response ) {
					
					$(".spinner").removeClass("is-active");
				},
				fail : function( err ) {
					//Ouput some feedback if it fails
					alert( "There was an error: " + err );
				}
			});
			
			// This return prevents the submit event to refresh the page.
			return false;
		}

	});

	/**
	 * Instantiate CodeMirror on the Textareas for Form HTML, JS and CSS
	 */
	$(function(){
        if( $('#form_html').length ) {
            var form_html_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            form_html_editorSettings.codemirror = _.extend(
                {},
                form_html_editorSettings.codemirror,
                {
                    lineNumbers: true,
	                indentUnit: 2,
	                tabSize: 2,
                }
            );
            var form_html_editor = wp.codeEditor.initialize( $('#form_html'), form_html_editorSettings );
            
            $(document).on('keyup', '.CodeMirror-code', function(){
            	form_html_editor.codemirror.save();
		      	$('#form_html').html(form_html_editor.codemirror.getValue());
	            $('#form_html').trigger('change');
	        });
        }

        if( $('#form_js').length ) {
            var form_js_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            form_js_editorSettings.codemirror = _.extend(
                {},
                form_js_editorSettings.codemirror,
                {
                    indentUnit: 4,
                    tabSize: 4,
                    mode: 'javascript',
                }
            );
            var form_js_editor = wp.codeEditor.initialize( $('#form_js'), form_js_editorSettings );

            $(document).on('keyup', '.CodeMirror-code', function(){
            	form_js_editor.codemirror.save();
		      	$('#form_js').html(form_js_editor.codemirror.getValue());
	            $('#form_js').trigger('change');
	        });
        }

        if( $('#form_css').length ) {
            var form_css_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            form_css_editorSettings.codemirror = _.extend(
                {},
                form_css_editorSettings.codemirror,
                {
                    indentUnit: 4,
                    tabSize: 4,
                    mode: 'css',
                }
            );
            var form_css_editor = wp.codeEditor.initialize( $('#form_css'), form_css_editorSettings );
            $(document).on('keyup', '.CodeMirror-code', function(){
            	form_css_editor.codemirror.save();
		      	$('#form_css').html(form_css_editor.codemirror.getValue());
	            $('#form_css').trigger('change');
	        });
        }

        if( $('#notification_content').length ) {
            var notification_content_editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
            notification_content_editorSettings.codemirror = _.extend(
                {},
                notification_content_editorSettings.codemirror,
                {
                    indentUnit: 4,
                    tabSize: 4,
                    mode: 'html',
                }
            );
            var notification_content_editor = wp.codeEditor.initialize( $('#notification_content'), notification_content_editorSettings );
            notification_content_editor.codemirror.setSize(null, '61vh');
            $(document).on('keyup', '.CodeMirror-code', function(){
            	notification_content_editor.codemirror.save();
		      	$('#notification_content').html(notification_content_editor.codemirror.getValue());
	            $('#notification_content').trigger('change');
	        });
        }
    });

})( jQuery );