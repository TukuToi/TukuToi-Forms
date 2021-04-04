<?php
/**
 * Form To Update Terms
 */

class Term_Edit_Forms extends TukuToi_Forms {

    const NONCE_VALUE = 'front_end_edit_add_terms';
    const NONCE_FIELD = 'feeat_nonce';

    protected $errors = array();
    protected $data = array();

    function __construct() {

        //Invoke Parent Class
        parent::__construct();

        //Get URL parameters for Edit Mode
        $this->ID           = !empty($_GET['term_id']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['term_id']) : '';
        $this->tax          = !empty($_GET['taxonomy']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['taxonomy']) : '';

        //Define non-hierarchical Taxonomies that are in fact registered as Hierarchical
        $this->no_parents   = array('post_tag','type-de-document','auteur', 'langue', 'zone');

        //Enqueue scripts and styles
        //add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        //add_shortcode( 'add_or_edit_term_form', array( $this, 'add_or_edit_form_shortcode' ) );

        //add_action( 'template_redirect',  array( $this, 'handleForm' ) );
    }

    // function enqueue_styles() {
    //     wp_register_style( 'tinymce_stylesheet', '/wp-includes/css/editor.min.css', false, false, 'all' );
    //     wp_register_style( 'tukutoi-forms-style', $this->pluginUrl.'/assets/css/custom-forms.css', false, false, 'all' );
    // }

    // function enqueue_scripts(){
    //     wp_register_script( 'tukutoi-forms-script', $this->pluginUrl.'/assets/js/custom-forms.js', array('jquery'), false, true );
    // }

    // function add_or_edit_form_shortcode() {
    //     if ( ! current_user_can( 'publish_posts' ) )
    //         return sprintf( '<p>Please <a href="%s">login</a> to use.</p>', esc_url( wp_login_url(  get_permalink() ) ) );
    //     elseif ( $this->isFormSuccess() )
    //         return '<p class="success">Success</p>';
    //     else
    //         return $this->getForm();
    // }

    function handleForm() {
        if ( ! $this->isFormSubmitted() )
            return false;

        //Simple Data Filtering
        $data = filter_input_array( INPUT_POST, array(
            'termTitle'         => FILTER_SANITIZE_STRING,
            'termDescription'   => FILTER_DEFAULT,
            'termParent'        => FILTER_SANITIZE_NUMBER_INT,
            'termAvatar'        => FILTER_SANITIZE_URL,
			'siteAuteur'		=> FILTER_SANITIZE_URL,
        ));

        $data = wp_unslash( $data );
        $data = array_map( 'trim', $data );
        
        //Additional WP Sanitization
        $data['termTitle']          = sanitize_text_field( $data['termTitle'] );
        $data['termDescription']    = wp_kses_post($data['termDescription']);
        $data['termParent']         = intval($data['termParent']);
        
        $this->data = $data;

        //Validation and Security
        if ( ! $this->isNonceValid() )
            $this->errors[] = 'Security check failed, please try again.';

        if ( ! $data['termTitle'] )
            $this->errors[] = 'Please enter a title.';
		
		/**
		 * Fix for current term being validated and failing the "term exists
		 * $id_of_term_slug_might_exist = get_term_by( 'slug', sanitize_title($data['termTitle']), $this->tax)->term_id;
		 * Then check: 
		 * if($id_of_term_slug_might_exist != $this->ID);//If it is not equal it means the term exists elsewhere already
		 */
		if( !is_wp_error( get_term_link( sanitize_title($data['termTitle']), $this->tax) ) && $this->ID != get_term_by( 'slug', sanitize_title($data['termTitle']), $this->tax)->term_id){
			$term_archive_exist = get_term_link( sanitize_title($data['termTitle']), $this->tax);
			$this->errors[] = 'Ce slug est déjà utilisé <a href="'. $term_archive_exist . '" target="_blank">ici</a>.';
		}
		
        if ( ! $this->errors ) {

            //If this is a Create New Term Form

            //Else it is an Edit Term Form
            $term_id = wp_update_term( 
                $this->ID , $this->tax, array(
                    'description'   => wpautop( wptexturize( $data['termDescription'])), 
                    'parent'        => $data['termParent'], 
                    'slug'          => sanitize_title($data['termTitle']), 
                    'name'          => $data['termTitle']
                ) 
            );
            //If term was succesfully created or is update form with valid term
            if ( $term_id ) {
			
            	//Set Term Meta
                update_term_meta( $this->ID , 'wpcf-avatar', $data['termAvatar']);
				update_term_meta( $this->ID, 'wpcf-site-auteur', $data['siteAuteur'] );
                //We are all set, let's send the form and redirect to target
          		//wp_redirect( add_query_arg( 'success', 'true' ) );
                wp_redirect( get_term_link( intval($this->ID ), $this->tax ) ); 
                		
				exit;
					
            } 
            //There is no Post created or no valid post is edited
            else {
                $this->errors[] = 'This Term does not exist or is corrupt.';
            }
        }
    }

    /**
     * Use output buffering to *return* the form HTML, not echo it.
     *
     * @return string
     */
    function getForm() {
		wp_enqueue_style( 'tinymce_stylesheet' );
        wp_enqueue_style( 'tukutoi-forms-style');
		wp_enqueue_media();
        wp_enqueue_script( 'tukutoi-forms-script' );

        ob_start();

        ?>
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

        <?php
        return ob_get_clean();
    }

    /**
     * Has the form been submitted?
     *
     * @return bool
     */
    function isFormSubmitted() {
        return isset( $_POST['submitForm'] );
    }

    /**
     * Has the form been successfully processed?
     *
     * @return bool
     */
    // function isFormSuccess() {
    //     return filter_input( INPUT_GET, 'success' ) === 'true';
    // }

    /**
     * Is the nonce field valid?
     *
     * @return bool
     */
    function isNonceValid() {
        return isset( $_POST[ self::NONCE_FIELD ] ) && wp_verify_nonce( $_POST[ self::NONCE_FIELD ], self::NONCE_VALUE );
    }
}
