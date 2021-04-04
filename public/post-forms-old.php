<?php
class Post_Edit_And_Add_Forms extends TukuToi_Forms{

    const NONCE_VALUE = 'front_end_edit_add_posts';
    const NONCE_FIELD = 'feeap_nonce';

    protected $errors = array();
    protected $data = array();

    function __construct() {

        //Invoke Parent Class
        parent::__construct();
        
        //Get URL parameters for Edit Mode
        $this->ID   = !empty($_GET['post_id']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['post_id']) : '';
        $this->type = !empty($_GET['post_type']) ? preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET['post_type']) : '';

        //Enqueue scripts and styles
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_select2') );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        //Filter WP Native Taxonomy DropDown
        add_filter( 'wp_dropdown_cats', array( $this, 'dropdown_filter'), 10, 2);
        
        //Remove Toolset Buttons from Forms
        if (!is_admin()){
			add_filter( 'toolset_editor_add_form_buttons', '__return_false' );
			add_filter( 'toolset_cred_button_before_print', '__return_false' );
			add_filter( 'toolset_editor_add_access_button', function(){
				return array();
			} );
		}

        //Register ShortCode to display Form
        add_shortcode( 'add_or_edit_post_form', array( $this, 'add_or_edit_form_shortcode' ) );

        //Listen for the form submit & process before headers output
        add_action( 'template_redirect',  array( $this, 'handleForm' ) );

    }

    function enqueue_select2() {
        wp_register_style( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css', false, false, 'all' );
        wp_register_script( 'select2', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js', array( 'jquery' ), '1.0', true );
		wp_register_script( 'select2-fr', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/i18n/fr.js', array( 'select2' ), '2.0', true );

    }

    function enqueue_styles() {
        
        wp_register_style( 'tinymce_stylesheet', '/wp-includes/css/editor.min.css', false, false, 'all' );
        wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css', false, false, 'all' );
        wp_register_style( 'tukutoi-forms-style', $this->pluginUrl.'/assets/css/custom-forms.css', false, false, 'all' );
        
        
    }

    function enqueue_scripts() {
		
		wp_register_script('jquery-validation-plugin', 'https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('jquery'));
        wp_register_script( 'tukutoi-forms-script', $this->pluginUrl.'/assets/js/custom-forms.js', array('jquery'), false, true );
        wp_register_script( 'tukutoi-repeater-js', $this->pluginUrl.'/assets/js/repeater.js', array('jquery'), false, true );

    }

    function dropdown_filter( $output, $r ) {
        $output = preg_replace( '/<select (.*?) >/', '<select $1 size="5" multiple>', $output);
        return $output;
    }

    function add_or_edit_form_shortcode() {
        if ( ! current_user_can( 'publish_posts' ) )
            return sprintf( '<p>Merci de <a href="%s">vous connecter</a> pour pouvoir publier.</p>', esc_url( wp_login_url(  get_permalink() ) ) );
        elseif ( $this->isFormSuccess() ) {
			$success_url = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			$success_url = strstr($success_url, '&', true);
            return '<div class="row d-flex justify-content-center"><div class="col-12 d-flex justify-content-center"><p class="success">Vos modifications ont bien été enregistrées.</p></div><div class="col d-flex justify-content-center"><a class="elementor-button-link elementor-button elementor-size-lg wp_grid_btn_bkg" href="'.$success_url.'">Continuer à modifier cet article</a></div><div class="col d-flex justify-content-center"><a class="elementor-button-link elementor-button elementor-size-lg wp_grid_btn_bkg" href="'. site_url( '/publier/', 'https' ) .'">Publier un nouvel article</a></div></div>';
		}
        else
            return $this->getForm();
    }

    function array_diff_compare($a, $b) {
        return (int)$a['repeater_instance'] -  (int)$b['repeater_instance'];
    }

    function handleForm() {
        if ( ! $this->isFormSubmitted() )
            return false;

        //Simple Data Filtering
        $data = filter_input_array( INPUT_POST , array(
            'postTitle'         => FILTER_SANITIZE_STRING,
            'postBody'          => FILTER_DEFAULT,
            'postParent'        => FILTER_SANITIZE_NUMBER_INT,
            'postFeaturedImage' => FILTER_SANITIZE_URL,
            'postExcerpt'       => FILTER_DEFAULT,
            //'credits'           => FILTER_SANITIZE_STRING,
            //'nomeResource'      => FILTER_SANITIZE_STRING,
            'urlResource'       => FILTER_SANITIZE_URL,
            'date'              => FILTER_SANITIZE_STRING,
			'dateDebut'         => FILTER_DEFAULT,
			'dateFin'           => FILTER_DEFAULT,
			'lieuEvenement'     => FILTER_SANITIZE_STRING,
            'postType'          => FILTER_SANITIZE_STRING,
            'postStatus'        => FILTER_SANITIZE_STRING,
        ));

        $data = wp_unslash( $data );
        $data = array_map( 'trim', $data );

        //This is needed because PHP filter_input_array() can NOT deal with Multidimensional Arrays.
        $tax_array = array('category','thematique','type-de-document','post_tag','zone','auteur','langue');
		                
        foreach ($tax_array as $tax) {
            //if (isset($_POST[$tax])) {
                $data[$tax] = isset($_POST[$tax]) ? $_POST[$tax] : '';
            //}
        }

        //Additional WP Sanitization
        $data['postTitle']      = sanitize_text_field( $data['postTitle'] );
        $data['postBody']       = wp_kses_post($data['postBody']);
        $data['postExcerpt']    = wp_kses_post($data['postExcerpt']);
        //$data['credits']        = sanitize_text_field($data['credits']);
        //$data['nomeResource']   = sanitize_text_field($data['nomeResource']);
        $data['postParent']     = intval($data['postParent']);
        $data['postExcerpt']    = wp_kses_post($data['postExcerpt']);
        $data['postType']       = sanitize_text_field($data['postType']);
        $data['postStatus']     = sanitize_text_field($data['postStatus']);

        //make '2010-02-23 18:57:33'; of date calendar input
        //strtotime($original_date);
        $data['date'] = str_replace ( '/' , '-' , $data['date']);
		$data['dateDebut'] = str_replace ( '/' , '-' , $data['dateDebut']);
		$data['dateFin'] = str_replace ( '/' , '-' , $data['dateFin']);
        $data['date'] = date("Y-m-d", strtotime($data['date']));
		$data['dateDebut'] = strtotime($data['dateDebut']);
		$data['dateFin'] = strtotime($data['dateFin']);
        //Post parent is not always there
        $data['post_parent'] = isset($_POST['post_parent']) ? $_POST['post_parent'] : 0;

        $this->data = $data;
        //Validation and Security
        if ( ! $this->isNonceValid() )
            $this->errors[] = 'Security check failed, please try again.';

        if ( ! $data['postTitle'] )
            $this->errors[] = 'Please enter a title.';

        if ( ! $this->errors ) {

            //If this is a Create New Post Form
            if ($this->ID == '') {
                //Then Insert Post
                $post_id = wp_insert_post( 
                    array(
                        'post_status'   => $data['postStatus'],
                        'post_date'     => $data['date'],
                        'post_date_gmt' => get_gmt_from_date( $data['date'] ),
                        'post_type'     => $data['postType'], 
                        'post_title'    => $data['postTitle'], 
                        'post_content'  => $data['postBody'],
                        'post_excerpt'  => $data['postExcerpt'],
                        'post_parent'   => $data['post_parent'],
                    ) 
                );
				
				//Send Email Notification on NEW Post creation, no matter its status
				//tkt_forms_send_email(Post ID, $_POST Form Data Array, FROM, TO); 
				tkt_forms_send_email($post_id, $data, 'webmaster@inter-reseaux.org', 'notifications@inter-reseaux.org');
				//tkt_forms_send_email($post_id, $data, 'webmaster@inter-reseaux.org', 's.mail.beda@gmail.com');
            } 
            //Else it is an Edit Post Form
            else {
                //Therefore Update Post          
                $post_id = $this->ID;
			   
			   $updated_post = wp_update_post( array(
                    'ID'            => $post_id,
                    'post_type'     => $data['postType'], 
                    'post_status'   => $data['postStatus'],
                    'post_date'     => $data['date'],
                    'post_date_gmt' => get_gmt_from_date( $data['date'] ),
                    'post_title'    => $data['postTitle'], 
                    'post_content'  => $data['postBody'],
                    'post_excerpt'  => $data['postExcerpt'],
                    'post_parent'   => $data['post_parent'],
                    )
                );

				
                
            }
            
            //If post was succesfully created or is update form with valid post
            if ( $post_id ) {

                //Set Featured Image
                set_post_thumbnail( $post_id, attachment_url_to_postid($data['postFeaturedImage']) );

                //Set Post Meta
                //update_post_meta( $post_id , 'wpcf-credits', $data['credits']);
                //update_post_meta( $post_id , 'wpcf-nom-de-la-source', $data['nomeResource']);
                update_post_meta( $post_id , 'wpcf-url-de-la-source', $data['urlResource']);
				update_post_meta( $post_id , 'wpcf-lieu-de-l-evenement', $data['lieuEvenement']);
				update_post_meta( $post_id , 'wpcf-date-de-debut', $data['dateDebut']);
				update_post_meta( $post_id , 'wpcf-date-de-fin', $data['dateFin']);
                
                //Set Post Terms
                if (is_array($data['post_tag'])){
					foreach ($data['post_tag'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['post_tag'][$key] = wp_insert_term( $term, 'post_tag' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['post_tag'], 'post_tag' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'post_tag');
				}
				if (is_array($data['type-de-document'])){
					foreach ($data['type-de-document'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['type-de-document'][$key] = wp_insert_term( $term, 'type-de-document' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['type-de-document'], 'type-de-document' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'type-de-document');
				}
				if (is_array($data['auteur'])){
					foreach ($data['auteur'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['auteur'][$key] = wp_insert_term( $term, 'auteur' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['auteur'], 'auteur' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'auteur');
				}
				if (is_array($data['langue'])){
					foreach ($data['langue'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['langue'][$key] = wp_insert_term( $term, 'langue' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['langue'], 'langue' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'langue');
				}
				if (is_array($data['category'])){
					foreach ($data['category'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['category'][$key] = wp_insert_term( $term, 'category' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['category'], 'category' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'category');
				}
				if (is_array($data['zone'])){
					foreach ($data['zone'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['zone'][$key] = wp_insert_term( $term, 'zone' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['zone'], 'zone' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'zone');
				}
				if (is_array($data['thematique'])){
					foreach ($data['thematique'] as $key => $term){
						if (term_exists($term) == 0 AND term_exists(intval($term)) == 0){
							$data['thematique'][$key] = wp_insert_term( $term, 'thematique' )['term_id'];
						}
					}
					wp_set_post_terms( $post_id, $data['thematique'], 'thematique' );
				}
				else{
					wp_set_object_terms( $post_id, false, 'thematique');
				}

                //Set Media Attachments (Repeating Instance)
                $attachments = get_posts( array(
                    'numberposts' => -1,
                    'post_type'   => 'attachment',
                    'post_parent' => $post_id,
                ) );
                //Array of existing Media Attachemnts by Post
			    $old = array();
                foreach ($attachments as $attachment) {
                    $old[] = array('repeater_instance'=>$attachment->ID);
                }

                //New Media Attachemnts by Post
                $new = array();
                $repeater_instance = $_POST['repeater_instance'];
                $count = count( $repeater_instance );
                for ( $i = 0; $i < $count; $i++ ) {
                        if ( $repeater_instance[$i] == '' )
                            $new[$i]['repeater_instance'] = '';
                        else
                            $new[$i]['repeater_instance'] = abs( $repeater_instance[$i] ); 
                }

                //If there are new items, or remaining items after removing some items
                if ( !empty( $new[0]['repeater_instance'] ) && $new != $old ){

                    $arrdiff = array_udiff($old, $new, array($this,'array_diff_compare'));
                    foreach ($arrdiff  as $array) {
                        foreach ($array as $key => $id) {
                            wp_update_post(array('ID' => $id, 'post_parent'=>0)); 
                        }
                    }
                    foreach ($new  as $array) {
                        foreach ($array as $key => $id) {
						   if(!empty($id)){
                            wp_update_post(array('ID' => $id, 'post_parent'=>$post_id)); 
							}
                        }
                    }
                }
                
                //If all Items are removed
                elseif ( empty($new[0]['repeater_instance']) && $old){
                    foreach ($old  as $array) {
                        foreach ($array as $key => $id) {
                            wp_update_post(array('ID' => $id, 'post_parent'=>0)); 
                        }
                    }
                }

                //We are all set, let's send the form and redirect to target
                //wp_redirect( get_term_link( intval($this->ID ), $this->tax ) ); 
                //get_permalink( $post_id );
                if( get_post_status( $post_id ) == 'publish' ){
               		wp_redirect( get_permalink( $post_id ) );
				}
				if( get_post_status( $post_id ) != 'publish' ){
					$args = add_query_arg( array(
						'post_id' => $post_id,
						'success' => 'true',
					));
               		wp_redirect( $args );
				}
                
                exit;
            } 
            //There is no Post created or no valid post is edited
            else {
                $this->errors[] = 'This Article does not exist or is corrupt.';
            }
        }
    }

    /**
     * Use output buffering to *return* the form HTML, not echo it.
     *
     * @return string
     */
    function getForm() {
		wp_enqueue_style(  'tinymce_stylesheet' );        
        wp_enqueue_style( 'jquery-ui' );

		wp_enqueue_style( 'select2' );
        wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'select2-fr' );
		wp_enqueue_media();

        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-validation-plugin' );
        
        wp_enqueue_script( 'tukutoi-repeater-js');
        wp_localize_script( 'tukutoi-repeater-js', 'repeaterJS', array(
            'title'     => __( "Choose an image", "custom-form" ),
            'btn_txt'   => __( "Use image", "custom-form" ),
        ) );
		
		
		wp_enqueue_style( 'custom-forms-style');
		wp_enqueue_script( 'tukutoi-forms-script');
        ob_start();

        ?>
        <div id ="front_end_add_edit_posts_container">
    
            <?php 
            //Error handling
            foreach ( $this->errors as $error ) { 
                ?><p class="error"><?php echo $error ?></p><?php 
            }; 
            ?>
            <form id="front_end_add_edit_posts_form" method="post" enctype="multipart/form-data">
                
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="postType"><?php _e( 'Type d\'article', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Choisir un modèle d'article.">i</button>
                            <?php
                            $selected = '';
                            $type_options = '';
                            $post_types = array('post'=>'Actualité','ressource'=>'Ressource','publication'=>'Publication','evenement'=>'Agenda');
                            foreach ($post_types as $post_type_slug => $post_type_name) {
								$selected = $post_type_slug == 'ressource' ? 'selected' : '';
                                if ( $this->ID != '' )
                                    $selected = get_post($this->ID)->post_type == $post_type_slug ? 'selected' : '';
                                $type_options .= '<option value="'.$post_type_slug.'" '.$selected.'>'.$post_type_name.'</option>';
                            }   
                            ?>
                            <select name="postType" id="postType">
                              <?php echo $type_options;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="postTitle"><?php _e( 'Titre', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Titre de l'article, en minuscules (requis)">i</button>
                            <input type="text" name="postTitle" id="postTitle" value="<?php
                    
                                if ( $this->ID != '' ) 
                                    echo get_post( $this->ID )->post_title;
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['postTitle'] ) )
                                    echo esc_attr( $this->data['postTitle'] );

                            ?>" />
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="postFeaturedImage"><?php _e( 'Image à la une', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Image qui sera utilisée en page d’accueil et sur la page de l’article. Format : jpg ou png. Largeur minimum : 300px. Pas de largeur maximum.">i</button>
                            <button id="add_fr_media" ><?php _e( 'Télécharger ou choisir une image', 'custom-form' ); ?></button>
                            <input type="hidden" name="postFeaturedImage" id="postFeaturedImage" value="<?php

                                if ( $this->ID != '' ) {
                                    echo get_the_post_thumbnail_url($this->ID, 'thumbnail');
                                }

                                if ( isset( $this->data['postFeaturedImage'] ) )
                                    echo esc_textarea( $this->data['postFeaturedImage'] );

                            ?>"/>
                            <?php $image = !empty( get_the_post_thumbnail_url($this->ID, 'thumbnail') ) ? get_the_post_thumbnail_url($this->ID, 'thumbnail') : ''; ?>
                            <img id="postFeaturedImage" src="<?php echo $image ?>" >
							
                        </div>
                    </div>
                </div>
				<div class="row" id="evenement_data" style="display:none;">
					<div class="col-4 form-group">
                            <label for="dateDebut"><?php _e( 'Date de début', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Une date de début d’événement est obligatoire. Si l’événement se tient sur une seule journée, ne remplir que la date de début. Seuls les événements dont la date de fin est postérieure à aujourd’hui sont affichés dans l’agenda.">i</button>
                            <input type="text" name="dateDebut" id="dateDebut" value="<?php

                                if ( $this->ID != '' ) {
									$dateDebut = get_post_meta($this->ID, 'wpcf-date-de-debut', true) != '' ? date('d/m/Y',get_post_meta($this->ID, 'wpcf-date-de-debut', true)) : '';
                                    echo $dateDebut;
								}
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['dateDebut'] ) )
                                    echo esc_attr( $this->data['dateDebut'] );

                            ?>" />
                        </div>
					<div class="col-4 form-group">
                            <label for="dateFin"><?php _e( 'Date de fin', 'custom-form' ); ?></label>
                            <input type="text" name="dateFin" id="dateFin" value="<?php

                                if ( $this->ID != '' ) {
									$dateFin = get_post_meta($this->ID, 'wpcf-date-de-fin', true) != '' ? date('d/m/Y',get_post_meta($this->ID, 'wpcf-date-de-fin', true)) : '';
                                    echo $dateFin;
								}
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['dateFin'] ) )
                                    echo esc_attr( $this->data['dateFin'] );

                            ?>" />
                        </div>
					<div class="col-4 form-group">
                            <label for="lieuEvenement"><?php _e( 'Lieu', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Si l’événement est en ligne, ne rien renseigner, sinon être le plus précis possible (ville mais aussi lieu exact de la rencontre quand c’est possible).">i</button>
                            <input type="text" name="lieuEvenement" id="lieuEvenement" value="<?php
                    
                                if ( $this->ID != '' ) 
                                    echo get_post_meta($this->ID, 'wpcf-lieu-de-l-evenement', true);
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['lieuEvenement'] ) )
                                    echo esc_attr( $this->data['lieuEvenement'] );

                            ?>" />
                        </div>
					
				</div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="postBody"><?php _e( 'Contenu', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Tout le contenu de l'article.">i</button>
                            <?php
                                if ( $this->ID != '' ) {
                                    wp_editor( get_post( $this->ID)->post_content, 'postBody', array('media_buttons'=>true,'default_editor'=>'visual','editor_height'=>'300'));
                                }
                                else{
                                    wp_editor('', 'postBody', array('media_buttons'=>true,'default_editor'=>'visual','editor_height'=>'300'));
                                }

                                if ( isset( $this->data['postBody'] ) )
                                    wp_editor(esc_textarea( $this->data['postBody'] ), 'postBody', array('media_buttons'=>true,'default_editor'=>'visual','editor_height'=>'300'));

                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="form-group">
                            <label for="postExcerpt"><?php _e( 'Référencement', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Phrase descriptive comprenant le maximum de mots-clés (en 240 caractères) tout en incitant à la lecture et en restant compréhensible. Est utilisée pour la description de la page dans les résultats des moteurs de recherche.">i</button>
                            <?php
                                if ( $this->ID != '' ) {
									?>
									<textarea rows="5" name="postExcerpt" id="postExcerpt"><?php echo get_post( $this->ID)->post_excerpt ?></textarea>
									<?php
                                    //wp_editor( get_post( $this->ID)->post_excerpt, 'postExcerpt', array('media_buttons'=>false,'default_editor'=>'visual','editor_height'=>'130'));
                                }
                                else{
									?>
									<textarea rows="5" name="postExcerpt" id="postExcerpt"></textarea>
									<?php
                                    //wp_editor('', 'postExcerpt', array('media_buttons'=>false,'default_editor'=>'visual','editor_height'=>'130'));
                                }

                                if ( isset( $this->data['postExcerpt'] ) ){
									?>
									<textarea rows="5" name="postExcerpt" id="postExcerpt"><?php echo $this->data['postExcerpt'] ?></textarea>
									<?php
								}
                                    //wp_editor(esc_textarea( $this->data['postExcerpt'] ), 'postExcerpt', array('media_buttons'=>false,'default_editor'=>'visual','editor_height'=>'130'));

                            ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <div class="form-group">
                            <label for="category"><?php _e( 'Rubrique', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Obligatoire. Le ou la rubrique dans laquelle sera affiché et recherché cet article.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                        $selected_them = get_the_terms($this->ID,'category');
                                            $selected_them_terms = array();
                                            if ($selected_them != false){
                                                foreach ($selected_them as $selected_the){
                                                    $selected_them_terms[] = $selected_the->term_id;
                                                }
                                            }
                                            wp_dropdown_categories( array('taxonomy' => 'category','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_them_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'categorySelect', 'name'=>'category[]')  );
                                        }
                                        else {
                                            wp_dropdown_categories( array('taxonomy' => 'category','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'categorySelect', 'name'=>'category[]')  );
                                        }
                                ?>

                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="thematique"><?php _e( 'Thématique', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Les principaux mots-clés thématiques.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                        $selected_them = get_the_terms($this->ID,'thematique');
                                        $selected_them_terms = array();
                                        if ($selected_them != false){
                                            foreach ($selected_them as $selected_the){
                                                $selected_them_terms[] = $selected_the->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'thematique','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect_Advanced(),'selected' => $selected_them_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'thematiqueSelect', 'name'=>'thematique[]')  );
                                    }
                                    else {
                                        wp_dropdown_categories( array('taxonomy' => 'thematique','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect_Advanced(), 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'thematiqueSelect', 'name'=>'thematique[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label for="zone"><?php _e( 'Zones géographiques', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Les principaux mots-clés géographiques.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                       $selected_zones_terms = array();
                                       $selected_zones = get_the_terms($this->ID,'zone');
                                        if ($selected_zones != false){
                                            foreach ($selected_zones as $selected_zone){
                                                $selected_zones_terms[] = $selected_zone->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'zone','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_zones_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'zoneSelect', 'name'=>'zone[]')  );
                                    }
                                    else {
                                       wp_dropdown_categories( array('taxonomy' => 'zone','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(), 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'zoneSelect', 'name'=>'zone[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-3">
                        <div class="form-group">
                            <label for="post_tag"><?php _e( 'Mots-clés', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Utilisé pour afficher un article à la Une de la page d’accueil ou pour l’intégrer dans un cycle thématique.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                        $selected_terms = array();
                                        $selected = get_the_terms($this->ID,'post_tag');
                                        if ($selected != false){
                                            foreach ($selected as $term){
                                                $selected_terms[] = $term->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'post_tag','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'post_tagSelect', 'name'=>'post_tag[]')  );
                                    }
                                    else {
                                       wp_dropdown_categories( array('taxonomy' => 'post_tag','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(), 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'post_tagSelect', 'name'=>'post_tag[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="type-de-document"><?php _e( 'Type de Document', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Utilisé principalement pour les ressources, mais aussi pour l’agenda.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                        $selected = get_the_terms($this->ID,'type-de-document');
                                        if ($selected != false){
                                            foreach ($selected as $term){
                                                $selected_terms[] = $term->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'type-de-document','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'type-de-documentSelect', 'name'=>'type-de-document[]')  );
                                    }
                                    else {
                                        wp_dropdown_categories( array('taxonomy' => 'type-de-document','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(), 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'type-de-documentSelect', 'name'=>'type-de-document[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="auteur"><?php _e( 'Auteurs', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Le ou les auteurs (individuel ou institutionnel) ayant écrit le document ou la ressources.">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                       $selected = get_the_terms($this->ID,'auteur');
                                        if ($selected != false){
                                            foreach ($selected as $term){
                                                $selected_terms[] = $term->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'auteur','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'auteurSelect', 'name'=>'auteur[]')  );
                                    }
                                    else {
                                        wp_dropdown_categories( array('taxonomy' => 'auteur','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'auteurSelect', 'name'=>'auteur[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                    <div class="col-3">
                        <div class="form-group">
                            <label for="langue"><?php _e( 'Langues', 'custom-form' ); ?></label>
                            <button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="« Français » est sélectionné par défaut mais peut être supprimé et remplacé par « Anglais ».">i</button>
                                <?php   
                                    if ( $this->ID != '' ) {
                                        $selected = get_the_terms($this->ID,'langue');
                                        if ($selected != false){
                                            foreach ($selected as $term){
                                                $selected_terms[] = $term->term_id;
                                            }
                                        }
                                        wp_dropdown_categories( array('taxonomy' => 'langue','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(),'selected' => $selected_terms, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'langueSelect', 'name'=>'langue[]')  );
                                    }
                                    else {
                                       wp_dropdown_categories( array('taxonomy' => 'langue','orderby' => 'name','order' => 'ASC','multiple' => true,'walker' => new Tkt_Forms_Taxonomy_MultiSelect(), 'selected' => 13, 'hide_empty' => false, 'hierarchical' => true, 'option_none_value'=>'0', 'id'=>'langueSelect', 'name'=>'langue[]')  );
                                    }
                                ?>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="urlResource"><?php _e( 'URL de la ressource', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Permet d’afficher en haut d’article, après les auteurs et les mots-clés, « Accéder à la ressource » qui est cliquable et renvoie sur l’URL de la ressource.">i</button>
                            <input type="text" name="urlResource" id="urlResource" value="<?php

                                if ( $this->ID != '' ) 
                                    echo get_post_meta( $this->ID,'wpcf-url-de-la-source', true);
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['urlResource'] ) )
                                    echo esc_attr( $this->data['urlResource'] );

                            ?>" />
                        </div>
                        <?php  //} ?>
                    </div>
                    <!--<div class="col-4">
                        <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="nomeResource"><?php _e( 'Nom de la ressource', 'custom-form' ); ?></label>
                            <input type="text" name="nomeResource" id="nomeResource" value="<?php

                                if ( $this->ID != '' ) 
                                    echo get_post_meta( $this->ID,'wpcf-nom-de-la-source', true);
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['nomeResource'] ) )
                                    echo esc_attr( $this->data['nomeResource'] );

                            ?>" />
                        </div>
                        <?php  //} ?>
                    </div>
                    <div class="col-4">
                        <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="credits"><?php _e( 'Crédits', 'custom-form' ); ?></label>
                            <input type="text" name="credits" id="credits" value="<?php

                                if ( $this->ID != '' ) 
                                    echo get_post_meta( $this->ID,'wpcf-credits', true);
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['credits'] ) )
                                    echo esc_attr( $this->data['credits'] );

                            ?>" />
                        </div>
                        <?php  //} ?>
                    </div>-->
                </div>

                <div class="row">
					<div class="col-12">
                    <?php
                    //$repeatable_fields = get_post_meta( $this->ID, 'repeatable_fields', true);
                    if (!empty($this->ID)){
                        $attachments = get_posts( array(
                          'numberposts' => -1,
                          'post_type'   => 'attachment',
                          'post_parent' => $this->ID,
                        ) );
                        foreach ($attachments as $attachment) {
                            $repeatable_fields[] = array('repeater_instance'=>$attachment->ID);
                        }
                    }
                    else {
                        $repeatable_fields[] = array();
                    }
                    ?>
						
                    <label>Pièces jointes</label>
					<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Vous renvoie à la médiathèque où vous pouvez soit prendre un document existant (en utilisant la recherche par exemple), soit ajouter une nouvelle image ou document. Si vous utilisez un document existant, sachez que le titre du document sera identique dans tous les usages.
Ce document est accessible en haut d’article, après les mots clés.
Poids maximum du document : 512 Mo.">i</button>
                    <table id="repeatable-fieldset-one" width="100%">
                        <!--<col width="30%">-->
                    <tbody id="tktforms-sortable">
                    <?php   
                    if ( isset($repeatable_fields) ) : 
                        foreach ( $repeatable_fields as $key => $field ) {
                            $field['repeater_instance']  = isset( $field['repeater_instance'] )? $field['repeater_instance'] : false;
                            ?>
                            <tr class="ui-state-default" style="background: white; padding: 5px; min-height: 50px;">
                                <td class="tktforms-repeater-repeater_instance-wrapper">
                                    <?php if($field['repeater_instance'] ) { ?>
									<div class="row">
										<div class="col-md-3"><img src="<?php echo esc_url(  $image = !empty(wp_get_attachment_thumb_url( $field['repeater_instance'] )) ?  wp_get_attachment_thumb_url( $field['repeater_instance'] ) : '/wp-content/plugins/tukutoi-forms/assets/generic-file-placeholder.png' ); ?>" width="150px" height="150px" /></div>
										<div class="col-md-9"><h5 class="RepeaterFileName"><?php echo !empty(wp_get_attachment_url( $field['repeater_instance'] )) ?  basename(wp_get_attachment_url( $field['repeater_instance'] )) : ''; ?></h5></div>
                                    </div>
                                    <?php } ?>  
                                    <input type="hidden" class="tktforms-repeater_instance" name="repeater_instance[]" value="<?php if( $field['repeater_instance'] != '') echo esc_attr( $field['repeater_instance'] ); ?>" />
                                    <button type="button" class="tktforms-upload_image_button button" style="display:<?php echo ( $field['repeater_instance'] )? 'none' : 'block';?>"><?php _e( 'Télécharger ou choisir une pièce jointe', 'tktforms' ); ?></button>
                                
                                    <button type="button" class="tktforms-remove_image_button button" style="display:<?php echo ( !$field['repeater_instance'] )? 'none' : 'block';?>;"><?php _e( 'Supprimer l\'image', 'tktforms' ); ?></button>    
                                </td>
                                <td class="tktforms-repeater-repeater_delete-wrapper"><a class="button remove-row" href="#"><span class="dashicons dashicons-trash"></span></a></td>
                            </tr>
                        <?php
                        }
                    else :
                    // show a blank one
                    ?>
                    <tr class="ui-state-default" style="background: white; padding: 5px; min-height: 50px;">
                        <td class="tktforms-repeater-repeater_instance-wrapper">
                            <input type="hidden" class="tktforms-repeater_instance" name="repeater_instance[]" />              
                            <button type="button" class="tktforms-upload_image_button button"><?php _e( 'Télécharger ou choisir une pièce jointe', 'tktforms' ); ?></button>
                            <button type="button" class="tktforms-remove_image_button button" style="display:none;"><?php _e( 'Supprimer l\'image', 'tktforms' ); ?></button>
                        </td>
                        <td class="tktforms-repeater-repeater_delete-wrapper"><a class="button remove-row" href="#"><span class="dashicons dashicons-trash"></span></a></td>
                    </tr>
                    <?php endif; ?> 
                    <!-- empty hidden one for jQuery -->
                    <tr class="ui-state-default empty-row screen-reader-text" style="background: white; padding: 5px; min-height: 50px;">       
                        <td class="tktforms-repeater-repeater_instance-wrapper">
                            <input type="hidden" class="tktforms-repeater_instance" name="repeater_instance[]" />              
                            <button type="button" class="tktforms-upload_image_button button"><?php _e( 'Télécharger ou choisir une pièce jointe', 'tktforms' ); ?></button>
                            <button type="button" class="tktforms-remove_image_button button" style="display:none;"><?php _e( 'Supprimer l\'image', 'tktforms' ); ?></button>               
                        </td>
                        <td class="tktforms-repeater-repeater_delete-wrapper"><a class="button remove-row" href="#"><span class="dashicons dashicons-trash"></span></a></td>
                    </tr>

                    </tbody>
                    </table>
                    
                    <p><a id="add-row" class="button" href="#">Ajouter plus de pièces jointes</a></p>
					</div>
                </div>

                <div class="row">
                    <?php 
					$has_parent = '';
                    if( $this->ID != '' ){
                        if ( is_post_type_hierarchical( get_post_type($this->ID) ) == true ){
                            $has_parent = '';
                            $col = 'col-3';
                        }
                        else{
                            $has_parent = 'display:none;';
                            $col = 'col-4';
                        }
                    }
                    else {
                        $col = 'col-4';
                    }
                    ?>
                    <div class="toggle-col <?php echo $col ?>">
                        <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="postStatus"><?php _e( 'Statut', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Par défaut, l’article est publié dès qu’il est créé. Il est possible de modifier et de le proposer à la relecture ou de le mettre en mode brouillon pour qu’il ne soit pas rendu public.">i</button>
                            <?php
                            $selected_state = '';
                            $options_state = '';
                            $post_states = array('publish'=>'Publié','draft'=>'Brouillon','pending'=>'En attente de relecture');
                            foreach ($post_states as $post_states_slug => $post_states_name) {
                                if ( $this->ID != '' )
                                    $selected_state = get_post($this->ID)->post_status == $post_states_slug ? 'selected' : '';
                                $options_state .= '<option value="'.$post_states_slug.'" '.$selected_state.'>'.$post_states_name.'</option>';
                            }   
                            ?>
                            <select name="postStatus" id="postStatus">
                              <?php echo $options_state;?>
                            </select>
                        </div>
                        <?php  //} ?>
                    </div>
                        <div id="post_parent_section" class="toggle-col <?php echo $col ?>" style="<?php echo  $has_parent?>">
                            <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                            <div class="form-group">
                                <label for="post_parent"><?php _e( 'Parent', 'custom-form' ); ?></label>
								<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="N’est proposé que pour les publications, et utile surtout pour Grain de Sel. Un article « contient » tous les autres du même numéro et devient ainsi la « publication mère ». Il faut donc d’abord créer l’article sommaire puis les autres articles, qui se rangeront dans le sommaire par ordre ANTE-chronologique (publier les articles dans l'ordre inverse).">i</button>
                                <?php 
								//array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash') 
								//array('publish', 'draft')
                                $parents = get_posts( array(  'post_type' => 'publication', 'post_status' => array('publish', 'draft'), 'numberposts' => -1 ) );
                                        $outp = '';
                                        if (!empty($parents)){
                                            $outp = '<select name="post_parent" id="post_parent">';
                                            $outp .= '<option value="0">Publication mère</option>';
                                            foreach ($parents as $parent){
                                                $selected_parent = '';
                                                if($this->ID != '')
                                                    $selected_parent = get_post($this->ID)->post_parent == $parent->ID ? 'selected' : '';
                                                $outp .= '<option value="'.$parent->ID.'" '.$selected_parent.'>'.$parent->post_title.'</option>';
                                            }
                                            $outp .= '</select>';  
                                        }
                                        echo $outp;
                                ?>
                            </div>
                            <?php  //} ?>
                        </div>

                    <div class="toggle-col <?php echo $col ?>">
                        <?php //if ( !in_array($this->tax, $this->no_parents) ) { ?>
                        <div class="form-group">
                            <label for="date"><?php _e( 'Date', 'custom-form' ); ?></label>
							<button class="tooltip button" type="button" class="btn btn-secondary" data-toggle="tooltip" data-html="true" title="Obligatoire. Date automatique de la publication. Peut être modifiée manuellement mais ne peut pas être vide.">i</button>
                            <input type="text" name="date" id="date" value="<?php

                                if ( $this->ID != '' ){
                                    echo date('d/m/Y', strtotime(get_post( $this->ID )->post_date));
								}
								else{
									echo date('d/m/Y');
								}
                                // "Sticky" field, will keep value from last POST if there were errors
                                if ( isset( $this->data['date'] ) )
                                    echo esc_attr( $this->data['date'] );

                            ?>" />
                        </div>
                        <?php  //} ?>
                    </div>
                    <div class="toggle-col <?php echo $col ?> d-flex justify-content-center">
                        <div class="form-group">
                            <?php 
								if( isset( $_GET['post_id']) ){
									?><button class="elementor-button-link elementor-button elementor-size-lg wp_grid_btn_bkg" type="submit" name="submitForm" ><?php _e( 'Mettre à jour', 'custom-form' ); ?></button><?php
								}
								else{
									?><button class="elementor-button-link elementor-button elementor-size-lg wp_grid_btn_bkg" type="submit" name="submitForm" ><?php _e( 'Publier', 'custom-form' ); ?></button><?php
								}
							?>
                        </div>
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
    function isFormSuccess() {
        return filter_input( INPUT_GET, 'success' ) === 'true';
    }

    /**
     * Is the nonce field valid?
     *
     * @return bool
     */
    function isNonceValid() {
        return isset( $_POST[ self::NONCE_FIELD ] ) && wp_verify_nonce( $_POST[ self::NONCE_FIELD ], self::NONCE_VALUE );
    }
}
