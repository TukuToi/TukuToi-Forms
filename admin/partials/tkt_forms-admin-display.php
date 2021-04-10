<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/admin/partials
 */
?>

<!-- The main action buttons ADD FORMS, EDIT FORMS and ADD/EDIT NOTIFICATIONS -->
<div class="<?php echo $this->plugin_name ?>_actions_wrapper">
	<div id="<?php echo $this->plugin_name ?>_add" class="<?php echo $this->plugin_name ?>_actions tkt_tooltip">
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-plus-square" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
		<span class="tkt_tooltiptext tkt_tooltiptext_tiny"><small>Create New Forms</small></span>
	</div>
	<div id="<?php echo $this->plugin_name ?>_edit" class="<?php echo $this->plugin_name ?>_actions tkt_tooltip">
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-pencil-square" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"/></svg>
		<span class="tkt_tooltiptext tkt_tooltiptext_tiny"><small>Edit Existing Forms</small></span>
	</div>
	<div id="<?php echo $this->plugin_name ?>_notification" class="<?php echo $this->plugin_name ?>_actions tkt_tooltip">
		<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-envelope-open" viewBox="0 0 16 16"><path d="M8.47 1.318a1 1 0 0 0-.94 0l-6 3.2A1 1 0 0 0 1 5.4v.818l5.724 3.465L8 8.917l1.276.766L15 6.218V5.4a1 1 0 0 0-.53-.882l-6-3.2zM15 7.388l-4.754 2.877L15 13.117v-5.73zm-.035 6.874L8 10.083l-6.965 4.18A1 1 0 0 0 2 15h12a1 1 0 0 0 .965-.738zM1 13.117l4.754-2.852L1 7.387v5.73zM7.059.435a2 2 0 0 1 1.882 0l6 3.2A2 2 0 0 1 16 5.4V14a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V5.4a2 2 0 0 1 1.059-1.765l6-3.2z"/></svg>
		<span class="tkt_tooltiptext tkt_tooltiptext_tiny"><small>Add/Edit Notifications</small></span>
	</div>
</div>
<!-- The main "Add New Form" Modal -->
<div id="tkt_forms_add_modal" class="tkt_modal">
	<div class="tkt_modal_wrap">
		<div class="tkt_modal_header">
			<span class="tkt_modal_title"><?php echo $this->human_plugin_name ?> | Add Forms</span>
			<span id="tkt_forms_add_modal_close" class="tkt_modal_close">&times;</span>
		</div>
		<div class="tkt_modal_content"> 
			<div class="<?php echo $this->plugin_name ?>_formtypes_wrapper">
				<div id="<?php echo $this->plugin_name ?>_codeditor_post" class="<?php echo $this->plugin_name ?>_formtypes tkt_tooltip">
					<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-file-earmark-post" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/><path d="M4 6.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7zm0-3a.5.5 0 0 1 .5-.5H7a.5.5 0 0 1 0 1H4.5a.5.5 0 0 1-.5-.5z"/></svg>
					<span class="tkt_tooltiptext"><small>Create New Post Form</small></span>
				</div>
				<div id="<?php echo $this->plugin_name ?>_codeditor_term" class="<?php echo $this->plugin_name ?>_formtypes tkt_tooltip">
					<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-tags-fill" viewBox="0 0 16 16"><path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/><path d="M1.293 7.793A1 1 0 0 1 1 7.086V2a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l.043-.043-7.457-7.457z"/></svg>
					<span class="tkt_tooltiptext"><small>Create New Term Form</small></span>
				</div>
				<div id="<?php echo $this->plugin_name ?>_codeditor_user" class="<?php echo $this->plugin_name ?>_formtypes tkt_tooltip">
					<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-person-lines-fill" viewBox="0 0 16 16"><path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z"/></svg>
					<span class="tkt_tooltiptext"><small>Create New User Form</small></span>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- The main "Edit Forms" Modal -->
<div id="tkt_forms_edit_modal" class="tkt_modal">
	<div class="tkt_modal_wrap">
		<div class="tkt_modal_header">
			<span class="tkt_modal_title"><?php echo $this->human_plugin_name ?> | Edit Forms</span>
			<span id="tkt_forms_edit_modal_close" class="tkt_modal_close">&times;</span>
		</div>
		<div class="tkt_modal_content"> 
			<div class="tkt_grid_container">
				<?php 
				$forms = $this->db_get_forms(); 
				rsort($forms);
				foreach ($forms as $key => $form) {
				?>
					<div class="tkt_grid_item tkt_no_pointer"><!-- tkt_toltip -->
						<?php 
						echo '<h3 class="tkt_grid_title">'. $form->form_title .'</h3>'; 
						echo '<div class="'. $this->plugin_name .'_edit_form tkt_tooltip tkt_pad tkt_link_pointer" id="'. $form->ID .'">';
							if($form->form_type == 'post_form'){
								?><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-file-earmark-post tkt_grid_icon" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/><path d="M4 6.5a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5h-7a.5.5 0 0 1-.5-.5v-7zm0-3a.5.5 0 0 1 .5-.5H7a.5.5 0 0 1 0 1H4.5a.5.5 0 0 1-.5-.5z"/></svg>
								
								<?php
							}
							elseif ($form->form_type == 'term_form') {
								?><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-tags-fill tkt_grid_icon" viewBox="0 0 16 16"><path d="M2 2a1 1 0 0 1 1-1h4.586a1 1 0 0 1 .707.293l7 7a1 1 0 0 1 0 1.414l-4.586 4.586a1 1 0 0 1-1.414 0l-7-7A1 1 0 0 1 2 6.586V2zm3.5 4a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"/><path d="M1.293 7.793A1 1 0 0 1 1 7.086V2a1 1 0 0 0-1 1v4.586a1 1 0 0 0 .293.707l7 7a1 1 0 0 0 1.414 0l.043-.043-7.457-7.457z"/></svg>

								<?php
							}
							elseif ($form->form_type == 'user_form') {
								?><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-person-lines-fill tkt_grid_icon" viewBox="0 0 16 16"><path d="M6 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm-5 6s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zM11 3.5a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm.5 2.5a.5.5 0 0 0 0 1h4a.5.5 0 0 0 0-1h-4zm2 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2zm0 3a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1h-2z"/></svg>

								<?php 
							}
						echo '<span class="tkt_tooltiptext"><small>Edit this form</small></span></div>';
						echo '<div class="tkt_tooltip tkt_pad tkt_link_pointer tkt_forms_delete_triggers" id="delete_form_'. $form->ID .'"><svg id="delete_<?php echo $form->ID ?>" xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(243 130 120)" class="bi bi-trash notification_delete_trigger tkt_link_pointer" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg><span class="tkt_tooltiptext"><small>Delete this Form</small></span></div>';
						?>	
						<span class="spinner" id="<?php echo $form->ID ?>"></span> <!-- Add this spinner class for each form trigger -->
					</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<!-- The main "Add/Edit Notifications" Modal -->
<div id="tkt_forms_notifications_modal" class="tkt_modal">
	<div class="tkt_modal_wrap">
		<div class="tkt_modal_header">
			<span class="tkt_modal_title"><?php echo $this->human_plugin_name ?> | Add/Edit Notifications</span>
			<span id="tkt_forms_notifications_modal_close" class="tkt_modal_close">&times;</span>
		</div>
		<div class="tkt_modal_content"> 
			<?php 
			echo '<div class="'. $this->plugin_name .'_notification" id="tkt_forms_add_new_notification">';
				?><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-plus-square" viewBox="0 0 16 16"><path d="M14 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h12zM2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2z"/><path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/></svg>
				<?php
			echo '</div>';
			?>
			<div class="tkt_grid_container">
				<?php 
				$notifications = $forms = $this->db_get_notifications(); 
				rsort($notifications);
				foreach ($notifications as $key => $notification) {
				?>
					<div class="tkt_grid_item tkt_no_pointer">
						<?php 
						echo '<div class="'. $this->plugin_name .'_notifications" id="'. $key .'"><h3 class="tkt_grid_title">'. $notification->notification_name .'</h3>'; 
							?><div class="tkt_tooltip tkt_pad tkt_float_l"><svg id="edit_<?php echo $key ?>" xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#0269ac" class="bi bi-pencil-square notification_edit_trigger tkt_link_pointer" viewBox="0 0 16 16"><path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"></path><path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5v11z"></path></svg><span class="tkt_tooltiptext"><small>Edit this Notification</small></span></div>
							<div class="tkt_tooltip tkt_pad tkt_float_r"><svg id="delete_<?php echo $key ?>" xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="rgb(243 130 120)" class="bi bi-trash notification_delete_trigger tkt_link_pointer" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg><span class="tkt_tooltiptext"><small>Delete this Notification</small></span></div>
							<?php
						echo '</div>';
						?>	
					<span class="spinner" id="<?php echo $form->ID ?>"></span> <!-- Add this spinner class for each form trigger -->
					</div>
				<?php
				}
				?>
			</div>
		</div>
	</div>
</div>
<!-- The postform "Code Editor" Modal -->
<div id="tkt_forms_codeditor_post_modal" class="tkt_modal tkt_code_modal">
	<div class="tkt_modal_wrap">
		<div class="tkt_modal_header">
			<span class="tkt_modal_title"><?php echo $this->human_plugin_name ?> | Post Form Code Editor</span>
			<span id="tkt_forms_codeditor_modal_close" class="tkt_modal_close">&times;</span>
		</div>
		<div class="tkt_modal_content"> 
			<span class="spinner"></span> <!-- Add this spinner class where you want it to appear--> 
			<span class="tkt_feedback"></span> <!-- Add this spinner class where you want it to appear--> 
			<form action="" method="post" name="<?php echo $this->plugin_name ?>_add_form">
				<fieldset>
					<input type="text" name="form_name" id="form_name" value="" placeholder="Add the Form Name">
				</fieldset>
				<fieldset>
			        <h3>Form HTML</h3>
			        <p class="description">Enter your Form HTML and Inputs here</p>
			        <textarea id="form_html" name="form_html" rows="5" class="widefat textarea"></textarea>   
			    </fieldset>
			    <fieldset>
			        <h3>Add your Form JavaScript</h3>
			        <small class="description">Hint: use <code>jQuery()</code> instead of <code>$()</code> namespace.</small>
			        <textarea id="form_js" rows="5" name="form_js" class="widefat textarea"></textarea>   
			    </fieldset>

			    <fieldset>
			        <h3>Add your Form CSS</h3>
			        <textarea id="form_css" rows="5" name="form_css" class="widefat textarea"></textarea>   
			    </fieldset>
			    <input type="hidden" name="form_type" value="post_form" style="display: none; visibility: hidden; opacity: 0;">
			    <input type="hidden" name="form_id" id="form_id" value="" style="display: none; visibility: hidden; opacity: 0;">
			    <input type="hidden" name="action" value="db_save_form_ajax" style="display: none; visibility: hidden; opacity: 0;">
    			<div class="tkt_absolute_tr"><button type="submit" class="tkt_button">Save!</button></div>
		    </form>
		</div>
	</div>
</div>
<!-- The notifications "Code Editor" Modal -->
<div id="tkt_forms_notification_code_modal" class="tkt_modal tkt_notification_modal">
	<style type="text/css"></style>
	<div class="tkt_modal_wrap">
		<div class="tkt_modal_header">
			<span class="tkt_modal_title"><?php echo $this->human_plugin_name ?> | Notification Editor</span>
			<span id="tkt_forms_notification_codeditor_modal_close" class="tkt_modal_close">&times;</span>
		</div>
		<div class="tkt_modal_content"> 
			<span class="spinner"></span> <!-- Add this spinner class where you want it to appear--> 
			<span class="tkt_feedback"></span> <!-- Add this spinner class where you want it to appear--> 
			<form action="" method="post" name="<?php echo $this->plugin_name ?>_add_notification">
				<fieldset>
					<input type="text" name="notification_name" id="notification_name" value="" placeholder="Add the Notification Name">
				</fieldset>
				<fieldset>
			        <h3>Notification Content</h3>
			        <small class="description">Hint: you can use HTML. Add eventual (C)SS Inline.</small>
			        <textarea id="notification_content" name="notification_content" rows="5" class="widefat textarea"></textarea>
			    </fieldset>
			    <input type="hidden" name="notification_id" id="notification_id" value="" style="display: none; visibility: hidden; opacity: 0;">
			    <input type="hidden" name="action" value="db_save_notification_ajax" style="display: none; visibility: hidden; opacity: 0;">
    			<div class="tkt_absolute_tr"><button type="submit" class="tkt_button">Save!</button></div>
		    </form>
		</div>
	</div>
</div>

