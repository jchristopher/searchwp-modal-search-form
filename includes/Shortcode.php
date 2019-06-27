<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormShortcode {

	public function __construct() {
		add_shortcode( 'searchwp_modal_search_form', array( $this, 'shortcode' ) );
	}

	public function shortcode( $atts ) {
		$args = shortcode_atts( array(
			'engine'   => 'default',
			'template' => 'Default',
			'text'     => __( 'Search', 'searchwpmodalform' ),
			'type'     => 'link',
			'class'    => array(),
		), $atts );

		$args['echo'] = true;

		ob_start();

		searchwp_modal_form_trigger( $args );

		return ob_get_clean();
	}
}

new SearchWPModalFormShortcode();
