<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function searchwp_get_modal_forms() {
	$forms = array(
		'default'      => 'Default',
		'supplemental' => 'Supplemental',
	);

	$forms = apply_filters( 'searchwp_modal_search_form_refs', $forms );

	return $forms;
}

/**
 * Determine whether the provided menu item is one of ours.
 */
function searchwp_get_modal_name_from_menu_item( $menu_item ) {
	if ( 'custom' !== $menu_item->type ) {
		return '';
	}

	if ( '#searchwp-modal-' !== substr( $menu_item->url, 0, 16 ) ) {
		return '';
	}

	return substr( $menu_item->url, 16 );
}
