<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormShortcode {

	private $engine;

	function __construct() {
		add_shortcode( 'searchwp_modal_search_form', array( $this, 'shortcode' ) );
	}

	function shortcode( $atts ) {
		$args = shortcode_atts( array(
			'engine' => 'default',
		), $atts );

		$this->engine = SWP()->is_valid_engine( $args['engine'] ) ? $args['engine'] : 'default';
	}
}

new SearchWPModalFormShortcode();
