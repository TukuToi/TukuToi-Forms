<?php
class Tkt_Forms_Taxonomy_MultiSelect extends Walker_CategoryDropdown {
    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        $pad = str_repeat('&nbsp;', $depth * 3);

        /** This filter is documented in wp-includes/category-template.php */
        $cat_name = apply_filters( 'list_cats', $category->name, $category );

        if ( isset( $args['value_field'] ) && isset( $category->{$args['value_field']} ) ) {
            $value_field = $args['value_field'];
        } else {
            $value_field = 'term_id';
        }

        $output .= "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";

        // Type-juggling causes false matches, so we force everything to a string.
        if ( in_array( $category->{$value_field}, (array)$args['selected'], true ) )
            $output .= ' selected="selected"';
        $output .= '>';
        $output .= $pad.$cat_name;
        if ( $args['show_count'] )
            $output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) .')';
        $output .= "</option>\n";
    }
}

class Tkt_Forms_Taxonomy_MultiSelect_Advanced extends Walker_CategoryDropdown {
	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ){
		$pad = str_repeat('&nbsp;', $depth * 3);
		$cat_name = apply_filters('list_cats', $category->name, $category);
		if( isset( $args['value_field'] ) && isset( $category->{$args['value_field']} ) ){
			$value_field = $args['value_field'];
		} else {
			$value_field = 'term_id';
		}
		$output .= $depth < 1 ? "\t<optgroup class=\"level-$depth\" label=\"" . $cat_name . "\"" : "\t<option class=\"level-$depth\" value=\"" . esc_attr( $category->{$value_field} ) . "\"";
		if( $depth > 0 && in_array( $category->{$value_field}, (array)$args['selected'], true ) )
			$output .= ' selected="selected"';
		$output .= '>';
		$output .= $pad.$cat_name;
		if( $depth > 0 && $args['show_count'] )
			$output .= '&nbsp;&nbsp;('. number_format_i18n( $category->count ) . ')';
		$output .= "</option>\n";
	}
}

function tkt_forms_send_email($post_id, $data, $from, $to){
	$base_url = 'https://www.inter-reseaux.org/';

	$subject = $data['postStatus'] . ' : ' . html_entity_decode($data['postTitle'], ENT_QUOTES, 'UTF-8');
	$auteurs = '';
	if (is_array($data['auteur'])){
		foreach ($data['auteur'] as $key => $term){
			$auteurs .= get_term( $term )->name . ', ';
		}
	}
	$rubriques = '';
	if (is_array($data['category'])){
		foreach ($data['category'] as $key => $term){
			$rubriques .= get_term( $term )->name . ', ';
		}
	}
	$thematiques = '';
	if (is_array($data['thematique'])){
		foreach ($data['thematique'] as $key => $term){
			$thematiques .= get_term( $term )->name . ', ';
		}
	}
	$zones = '';
	if (is_array($data['zone'])){
		foreach ($data['zone'] as $key => $term){
			$zones .= get_term( $term )->name . ', ';
		}
	}
	$types_de_doc = '';
	if (is_array($data['type-de-document'])){
		foreach ($data['type-de-document'] as $key => $term){
			$types_de_doc .= get_term( $term )->name . ', ';
		}
	}
	$button = '<hr><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td><table border="0" cellspacing="0" cellpadding="0"><tr><td align="left" style="border-radius: 3px;" bgcolor="#FF6633"><a href="' . $base_url . 'publier/?post_id=' . $post_id . '" target="_blank" style="font-size: 16px; font-family: Helvetica, Arial, sans-serif; color: white; text-decoration: none; text-decoration: none;border-radius: 3px; padding: 12px 18px; border: 1px solid #FF6633; display: inline-block;">Modifier</a></td></tr></table></td></tr></table>';
	$body  = $data['postTitle'] . '<br>par ' . $auteurs . 'dans ' . rtrim($rubriques, ', ') . '<br>';
	$body .= 'mots-clés: ' . $thematiques . $zones . rtrim($types_de_doc, ', ') . '<hr>';
	$body .= wp_trim_words( $data['postBody'], 150 ) . '<hr>';
	$body .= '<a href="' . get_permalink( $post_id ) . '">' . get_permalink( $post_id ) . '</a><br>';
	$body .= $button;
	
	$headers = array(
		'Content-Type: text/html; charset=UTF-8',
		'Reply-To: Do Not Reply <noreply@inter-reseaux.org>', 
		'BCC: Testeur <notifications_inter-reseaux.org@romanceor.net>', 
		'From: Inter-Réseaux <' . $from . '>'
	);
	
	wp_mail( $to, $subject, $body, $headers );
}