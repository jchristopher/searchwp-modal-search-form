<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function searchwp_modal_form_get_templates() {
	$templates = array( 'default' );

	return $templates;
}

/**
 * Generates a map of available modals which are defined by all combinations
 * of search engines and modal templates (which are file-based for the time being)
 */
function searchwp_get_modal_forms() {
	$engines   = SWP()->settings['engines'];
	$templates = searchwp_modal_form_get_templates();
	$forms     = array();

	foreach ( $engines as $engine_name => $engine_settings ) {
		foreach ( $templates as $template ) {
			$form_name = $engine_name . '-' . $template;

			$engine_label = isset( $engine_settings['searchwp_engine_label'] )
								? $engine_settings['searchwp_engine_label']
								: __( 'Default', 'searchwp' );

			$template_label = sanitize_title_with_dashes( $template );
			$template_label = explode( '-', $template_label );
			$template_label = array_map( 'ucfirst', $template_label );
			$template_label = implode( ' ', $template_label );

			$forms[ $form_name ] = array(
				'name'           => $form_name,
				'template_name'  => $template,
				'template_label' => $template_label,
				'engine_name'    => $engine_name,
				'engine_label'   => $engine_label,
			);

			// $engine_label . '/' . $template_label;
		}
	}

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
