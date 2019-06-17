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
