<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tkt_forms
 * @subpackage Tkt_forms/public/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

        <div id ="front_end_edit_terms_container">
   
            <?php 
            //Error handling
            foreach ( $this->errors as $error ) { 
                ?><p class="error"><?php echo $error ?></p><?php 
            }; 
            ?>

            <form id="front_end_edit_terms_form" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="termTitle"><?php _e( 'Titre', 'custom-form' ); ?></label>
                            <input type="text" name="termTitle" id="termTitle" value="<?php

                                if ( $this->ID != '' && !isset( $this->data['termTitle'] ) ) 
                                    echo get_term( $this->ID, $this->tax )->name;
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['termTitle'] ) )
                                    echo esc_attr( $this->data['termTitle'] );

                            ?>" />
                        </div>
                    </div>
					<?php if( get_term( $this->ID )->taxonomy == 'auteur'){ ?>
						<div class="col-12">
							<div class="form-group">
								<label for="siteAuteur"><?php _e( 'Website', 'custom-form' ); ?></label>
								<input type="text" name="siteAuteur" id="siteAuteur" value="<?php

									if ( $this->ID != '' && !isset( $this->data['siteAuteur'] ) ) 
										echo get_term_meta( $this->ID, 'wpcf-site-auteur', true );//get_term( $this->ID, $this->tax )->name;
									// "Sticky" field, will keep value from last POST if there were errors
									if ( isset( $this->data['siteAuteur'] ) )
										echo esc_attr( $this->data['siteAuteur'] );

								?>" />
							</div>
						</div>
					<?php }?>
                    <div class="col-12">
                        <div class="form-group">
                            <label for="termDescription"><?php _e( 'Contenu', 'custom-form' ); ?></label>
                            <?php
                                if ( $this->ID != '' && !isset( $this->data['termDescription'] ) ) 
                                    wp_editor( get_term( $this->ID, $this->tax )->description, 'termDescription', array('media_buttons'=>false,'default_editor'=>'visual','editor_height'=>'300'));


                                if ( isset( $this->data['termDescription'] ) )
                                    wp_editor(esc_textarea( $this->data['termDescription'] ), 'termDescription',array('media_buttons'=>false,'default_editor'=>'visual','editor_height'=>'300'));

                            ?>
                        </div>
                    </div>
                    <div class="col-6">
                        <?php if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="termParent"><?php _e( 'Parent', 'custom-form' ); ?></label>
                            
                                <?php   
                                if ( $this->ID != '' ) {
                                    wp_dropdown_categories( array('taxonomy'=>$this->tax, 'value_field' =>'term_id', 'selected' => get_term( $this->ID, $this->tax )->parent, 'hierarchical'=>1, 'option_none_value'=>'0', 'show_option_none'=>'Parent', 'id'=>'termParentSelect', 'name'=>'termParent') );  
                                }
                                ?>

                        </div>
                        <?php  } ?>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="termAvatar"><?php _e( 'Image', 'custom-form' ); ?></label>
                            <button id="add_fr_media" ><?php _e( 'Charger', 'custom-form' ); ?></button>
                            <input type="hidden" name="termAvatar" id="termAvatar" value="<?php

                                if ( $this->ID != '' && !isset( $this->data['termAvatar'] ) ) 
                                    echo get_term_meta( $this->ID, 'wpcf-avatar', true );


                                if ( isset( $this->data['termAvatar'] ) )
                                    echo esc_textarea( $this->data['termAvatar'] );

                            ?>"/>
                            <?php $image = !empty( get_term_meta( $this->ID, 'wpcf-avatar', true ) ) ? get_term_meta( $this->ID, 'wpcf-avatar', true ) : ''; ?>
                            <img id="termAvatarImg" src="<?php echo $image ?>" width="150" height="150">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <button type="submit" name="submitForm" ><?php _e( 'Mettre à jour', 'custom-form' ); ?></button>
                    </div>
                </div>

                <?php wp_nonce_field( self::NONCE_VALUE , self::NONCE_FIELD ) ?>
            </form>
        </div>