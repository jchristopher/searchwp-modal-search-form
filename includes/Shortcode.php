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
		), $atts );

		if ( function_exists( 'SWP' ) ) {
			$engine = SWP()->is_valid_engine( $args['engine'] ) ? $args['engine'] : 'default';
		} else {
			$engine = '{wp_native}';
		}

		$template   = searchwp_modal_form_get_template_from_label( $args['template'] );
		$modal_hash = searchwp_modal_form_get_template_hash( $engine, $template['file'] );

		add_filter( 'searchwp_modal_form_queue', function( $forms ) use ( $modal_hash ) {
			$forms[] = $modal_hash;

			return $forms;
		} );

		ob_start();
		if ( 'button' === $args['type'] ) {
			?>
			<button data-searchwp-modal-trigger="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>"><?php echo esc_html( $args['text'] ); ?></button>
			<?php
		} else {
			?>
			<a href="<?php echo esc_attr( '#searchwp-modal-' . $modal_hash ); ?>" data-searchwp-modal-trigger="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>"><?php echo esc_html( $args['text'] ); ?></a>
			<?php
		}

		return ob_get_clean();
	}
}

new SearchWPModalFormShortcode();
