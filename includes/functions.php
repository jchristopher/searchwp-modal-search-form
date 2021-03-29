<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function searchwp_modal_form_trigger( $args ) {
	$defaults = array(
		'engine'   => 'default',
		'template' => 'Default',
		'text'     => __( 'Search', 'searchwpmodalform' ),
		'type'     => 'link',
		'class'    => array(),
		'echo'     => true,
	);

	$args = wp_parse_args( $args, $defaults );

	if ( ! is_array( $args['class'] ) ) {
		$args['class'] = (array) $args['class'];
	}

	$args['class'] = implode(
		' ',
		array_merge(
			array( 'searchwp-modal-form-trigger-el' ),
			$args['class']
		)
	);

	if ( class_exists( 'SearchWP' ) ) {
		// SearchWP 3.x compat.
		if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engine_settings = \SearchWP\Settings::get_engine_settings( $args['engine'] );
			$engine = $engine_settings ? $args['engine'] : 'default';
		} else if ( function_exists( 'SWP' ) ) {
			$engine = SWP()->is_valid_engine( $args['engine'] ) ? $args['engine'] : 'default';
		}
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
		<button
			class="<?php echo esc_attr( 'button ' . $args['class'] ); ?>"
			data-searchwp-modal-trigger="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>">
			<?php echo wp_kses( $args['text'], apply_filters( 'searchwp_modal_form_trigger_text_kses', 'post' ) ); ?>
		</button>
		<?php
	} else {
		?>
		<a
			class="<?php echo esc_attr( $args['class'] ); ?>"
			href="<?php echo esc_attr( '#searchwp-modal-' . $modal_hash ); ?>"
			data-searchwp-modal-trigger="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>">
			<?php echo wp_kses( $args['text'], apply_filters( 'searchwp_modal_form_trigger_text_kses', 'post' ) ); ?>
		</a>
		<?php
	}

	$output = ob_get_clean();

	if ( $args['echo'] ) {
		echo wp_kses(
			$output,
			array(
				'a' => array(
					'class'                       => array(),
					'href'                        => array(),
					'data-searchwp-modal-trigger' => array(),
				),
				'button' => array(
					'class'                       => array(),
					'data-searchwp-modal-trigger' => array(),
				),
			)
		);
	} else {
		return $output;
	}
}

/**
 * Load all available templates and their labels.
 */
function searchwp_modal_form_get_templates() {
	$templates    = array();
	$template_dir = apply_filters( 'searchwp_modal_form_template_dir', 'searchwp-modal-form' );

	// Scan all applicable directories for template files.
	$template_files = array_unique( array_merge(
		glob( trailingslashit( SEARCHWP_MODAL_FORM_DIR ) . 'templates/*.[pP][hH][pP]' ), // Plugin.
		glob( trailingslashit( get_stylesheet_directory() ) . trailingslashit( $template_dir ) . '*.[pP][hH][pP]' ), // Child Theme.
		glob( trailingslashit( get_template_directory() ) . trailingslashit( $template_dir ) . '*.[pP][hH][pP]' ) // Parent Theme.
	) );

	// Scan all files for required 'header' data.
	foreach ( $template_files as $key => $template_file ) {
		$data = searchwp_modal_form_get_template_data( $template_file );

		if ( ! empty( $data['template_label'] ) ) {
			$templates[] = array(
				'file'  => $template_file,
				'label' => $data['template_label'],
			);
		}
	}

	return $templates;
}

/**
 * Retrieve file data from file path.
 *
 * @return array
 */
function searchwp_modal_form_get_template_data( $template ) {
	include_once ABSPATH . 'wp-admin/includes/file.php';
	WP_Filesystem();

	if ( ! file_exists( $template ) ) {
		return array();
	}

	return get_file_data( $template, array(
		'template_label' => 'SearchWP Modal Form Name',
	) );
}

/**
 * Retrieve available search engines.
 */
function searchwp_modal_form_get_engines() {
	// If SearchWP is NOT available, we've only got one engine to work with.
	// We're going to mimic SearchWP's settings structure and then override.
	$engines = array(
		'{wp_native}' => array(
			// We're again mimicking the SearchWP storage here.
			'searchwp_engine_label' => __( 'Native WordPress', 'searchwpmodalform' ),
		),
	);

	// Override if SearchWP is active.
	if ( class_exists( 'SearchWP' ) ) {
		// SearchWP 3.x compat.
		if ( class_exists( '\\SearchWP\\Settings' ) ) {
			$engines_settings = \SearchWP\Settings::_get_engines_settings();
			$engines = array();

			foreach ( $engines_settings as $name => $settings ) {
				$engines[ $name ] = array( 'searchwp_engine_label' => $settings['label'] );
			}
		} else if ( function_exists( 'SWP' ) ) {
			$engines = SWP()->settings['engines'];
		}
	}

	return $engines;
}

/**
 * Generates a map of available modals which are defined by all combinations
 * of search engines and modal templates (which are file-based for the time being)
 */
function searchwp_modal_form_get_forms() {
	$engines   = searchwp_modal_form_get_engines();
	$templates = searchwp_modal_form_get_templates();
	$forms     = array();

	foreach ( $engines as $engine_name => $engine_settings ) {

		// SearchWP 3.x compat.
		if ( is_object( $engine_settings ) && method_exists( $engine_settings, 'get_label' ) ) {
			$engine_label = $engine_settings->get_label();
		} else {
			$engine_label = isset( $engine_settings['searchwp_engine_label'] )
				? $engine_settings['searchwp_engine_label']
				: __( 'Default', 'searchwp' );
		}

		foreach ( $templates as $template ) {
			$hash = searchwp_modal_form_get_template_hash( $engine_name, $template['file'] );

			$forms[ $hash ] = array(
				'name'           => $hash,
				'template_file'  => $template['file'],
				'template_label' => $template['label'],
				'engine_name'    => $engine_name,
				'engine_label'   => $engine_label,
			);
		}
	}

	$forms = apply_filters( 'searchwp_modal_search_form_refs', $forms );

	return $forms;
}

/**
 * Given a hash, determine engine and template file.
 */
function searchwp_modal_form_get_template_from_label( $label = 'Default' ) {
	$templates = searchwp_modal_form_get_templates();

	if ( empty( $templates ) ) {
		return null;
	}

	foreach ( $templates as $template ) {
		if ( $template['label'] === $label ) {
			break;
		}
	}

	return $template;
}

/**
 * Given a hash, determine engine and template file.
 */
function searchwp_modal_form_reverse_hash_lookup( $hash ) {
	$engines   = searchwp_modal_form_get_engines();
	$templates = searchwp_modal_form_get_templates();
	$found     = false;

	foreach ( $engines as $engine_name => $engine_settings ) {
		foreach ( $templates as $template ) {
			$template_hash = searchwp_modal_form_get_template_hash( $engine_name, $template['file'] );

			if ( $template_hash === $hash ) {
				$found = array(
					'engine'   => $engine_name,
					'template' => $template,
				);

				break;
			}
		}

		if ( $found ) {
			break;
		}
	}

	return $found;
}

/**
 * Builds a hash based on combination of engine name and relative template path.
 */
function searchwp_modal_form_get_template_hash( $engine, $file ) {
	$form_name = $engine . '-' . str_replace( ABSPATH, '', $file );

	return md5( $form_name . $engine );
}

/**
 * Extracts the modal name from an existing URI.
 */
function searchwp_modal_form_get_name_from_uri( $uri ) {
	$name = '#searchwp-modal-' === substr( $uri, 0, 16 ) ? substr( $uri, 16 ) : false;

	return $name;
}

/**
 * Determine whether the provided menu item is one of ours.
 */
function searchwp_modal_form_get_name_from_menu_item( $menu_item ) {
	if ( 'custom' !== $menu_item->type ) {
		return '';
	}

	$modal_name = searchwp_modal_form_get_name_from_uri( $menu_item->url );

	if ( ! $modal_name ) {
		return '';
	}

	return $modal_name;
}
