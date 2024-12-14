<?php

/**
 * Get translated object ID if the WPML plugin is installed
 * Return the original ID if this plugin is not installed
 *
 * @param int    $id            The object ID
 * @param string $type          The object type 'post', 'page', 'post_tag', 'category' or 'attachment'. Default is 'page'
 * @param bool   $original      Set as 'true' if you want WPML to return the ID of the original language element if the translation is missing.
 * @param bool   $language_code If set, forces the language of the returned object and can be different than the displayed language.
 *
 * @return mixed
 */
function sober_addons_get_translated_object_id( $id, $type = 'page', $original = true, $language_code = null ) {
	return apply_filters( 'wpml_object_id', $id, $type, $original, $language_code );
}

/**
 * Get terms array for select control
 *
 * @param string $taxonomy
 * @return array
 */
function sober_addons_get_terms_hierarchy( $taxonomy = 'category', $separator = '-' ) {
	$terms = get_terms( array(
		'taxonomy'   => $taxonomy,
		'hide_empty' => true,
		'update_term_meta_cache' => false,
	) );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	$taxonomy = get_taxonomy( $taxonomy );

	if ( $taxonomy->hierarchical ) {
		$terms = sober_addons_sort_terms_hierarchy( $terms );
		$terms = sober_addons_flatten_hierarchy_terms( $terms, $separator );
	}

	return $terms;
}

/**
 * Recursively sort an array of taxonomy terms hierarchically.
 *
 * @param array $terms
 * @param integer $parent_id
 * @return array
 */
function sober_addons_sort_terms_hierarchy( $terms, $parent_id = 0 ) {
	$hierarchy = array();

	foreach ( $terms as $term ) {
		if ( $term->parent == $parent_id ) {
			$term->children = sober_addons_sort_terms_hierarchy( $terms, $term->term_id );
			$hierarchy[] = $term;
		}
	}

	return $hierarchy;
}

/**
 * Flatten hierarchy terms
 *
 * @param array $terms
 * @param integer $depth
 * @return array
 */
function sober_addons_flatten_hierarchy_terms( $terms, $separator = '&mdash;', $depth = 0 ) {
	$flatted = array();

	foreach ( $terms as $term ) {
		$children = array();

		if ( ! empty( $term->children ) ) {
			$children = $term->children;
			$term->has_children = true;
			unset( $term->children );
		}

		$term->depth = $depth;
		$term->name = $depth && $separator ? str_repeat( $separator, $depth ) . ' ' . $term->name : $term->name;
		$flatted[] = $term;

		if ( ! empty( $children ) ) {
			$flatted = array_merge( $flatted, sober_addons_flatten_hierarchy_terms( $children, $separator, ++$depth ) );
			$depth--;
		}
	}

	return $flatted;
}
