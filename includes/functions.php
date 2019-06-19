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

function searchwp_get_modal_name_from_uri( $uri ) {
	$name = '#searchwp-modal-' === substr( $uri, 0, 16 ) ? substr( $uri, 16 ) : false;

	return $name;
}

/**
 * Determine whether the provided menu item is one of ours.
 */
function searchwp_get_modal_name_from_menu_item( $menu_item ) {
	if ( 'custom' !== $menu_item->type ) {
		return '';
	}

	if ( ! searchwp_get_modal_name_from_uri( $menu_item->url ) ) {
		return '';
	}

	return substr( $menu_item->url, 16 );
}
