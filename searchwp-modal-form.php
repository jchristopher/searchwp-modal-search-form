<?php
/*
Plugin Name: SearchWP Modal Search Form
Plugin URI: https://searchwp.com/extensions/modal-form/
Description: Lightweight and accessible search form
Version: 0.4.1
Requires PHP: 5.6
Author: SearchWP, LLC
Author URI: https://searchwp.com/
Text Domain: searchwpmodalform

Copyright 2019-2021 SearchWP, LLC

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, see <http://www.gnu.org/licenses/>.
*/

// exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'SEARCHWP_MODAL_FORM_VERSION' ) ) {
	define( 'SEARCHWP_MODAL_FORM_VERSION', '0.4.1' );
}

if ( ! defined( 'SEARCHWP_MODAL_FORM_DIR' ) ) {
	define( 'SEARCHWP_MODAL_FORM_DIR', dirname( __FILE__ ) );
}

/**
 * Class SearchWP_Modal_Form
 */
class SearchWP_Modal_Form {

	private $modal_template_input = 'swpmfe';

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_block_type' ) );
		add_filter( 'block_categories', array( $this, 'block_categories' ) );

		add_action( 'plugins_loaded', function() {
			$this->includes();

			add_action( 'wp_footer', array( $this, 'render_modals' ) );
		});

		// By default all generated modal forms will be using the Default SearchWP engine
		// when applicable, but we're tagging each form with a reference to the modal
		// configuration, and we can peek at that during runtime and swap out the engine
		// configuration with the defined engine during the request.
		add_filter( 'searchwp\query\args', array( $this, 'maybe_swap_engine' ), 99 );
		add_filter( 'searchwp_engine_settings_default', array( $this, 'maybe_swap_engine' ), 99 );
	}

	/**
	 * Callback to swap out SearchWP engine configuration during runtime when applicable.
	 */
	public function maybe_swap_engine( $engine_settings ) {
		if (
			! isset( $_REQUEST[ $this->modal_template_input ] ) // phpcs:ignore
			|| empty( $_REQUEST[ $this->modal_template_input ] ) ) { // phpcs:ignore
			return $engine_settings;
		}

		$modal_hash = $_REQUEST[ $this->modal_template_input ]; // phpcs:ignore
		$forms      = searchwp_modal_form_get_forms();

		if ( ! array_key_exists( $modal_hash, $forms ) ) {
			return $engine_settings;
		}

		$engine = $forms[ $modal_hash ]['engine_name'];

		// SearchWP 3.x compat.
		if ( class_exists( 'SearchWP\Settings' ) ) {
			$new_engine = \SearchWP\Settings::get_engine_settings( $engine );

			if ( $new_engine ) {
				$engine_settings['engine'] = $engine;
			}

			return $engine_settings;
		} else if ( function_exists( 'SWP' ) ) {
			$engines = SWP()->settings['engines'];
			return array_key_exists( $engine, $engines ) ? $engines[ $engine ] : $engine_settings;
		}
	}

	/**
	 * Callback to utilize all modals that have been enqueued for this page load
	 * and output all applicable assets to make them all work.
	 */
	public function render_modals() {
		// All modals use this hook to enqueue themselves when implemented.
		$enqueued_modals = apply_filters( 'searchwp_modal_form_queue', array() );

		if ( ! is_array( $enqueued_modals ) || empty( $enqueued_modals ) ) {
			return;
		}

		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min'; // phpcs:ignore

		// Output the main trigger handler and modal framework.
		wp_enqueue_script(
			'searchwp-modal-form',
			plugin_dir_url( __FILE__ ) . "assets/dist/searchwp-modal-form${debug}.js",
			array(),
			SEARCHWP_MODAL_FORM_VERSION,
			true
		);

		// Output all enqueued modal templates that are used on this page load.
		foreach ( array_unique( $enqueued_modals ) as $modal_hash ) {
			do_action( 'searchwp_modal_form_template_start', array( 'modal' => $modal_hash ) );
			$this->render_modal( $modal_hash );
			do_action( 'searchwp_modal_form_template_end', array( 'modal' => $modal_hash ) );
		}
	}

	/**
	 * Output the markup for a submitted modal hash.
	 */
	private function render_modal( $modal_hash ) {
		$modal = searchwp_modal_form_reverse_hash_lookup( $modal_hash );
		?>
		<div class="searchwp-modal-form" id="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>" aria-hidden="true">
			<?php
			if ( file_exists( $modal['template']['file'] ) ) {
				ob_start();
				include $modal['template']['file'];
				$modal_form_markup = ob_get_contents();
				ob_end_clean();

				// Tag the form with a hidden input of the modal hash for future reference.
				if ( false !== stripos( $modal_form_markup, '</form>' ) ) {
					$form_tag = '<input type="hidden" name="' . esc_attr( $this->modal_template_input ) . '" value="' . esc_attr( $modal_hash ) . '" />';

					$modal_form_markup = str_ireplace( '</form>', $form_tag . '</form>', $modal_form_markup );
				}

				// This markup is directly from the template file which is responsible for handling user input.
				echo $modal_form_markup; // phpcs:ignore
			} else {
				echo esc_html_e( 'Template not found!', 'searchwpmodalform' );
			}
			?>
		</div>
		<?php
	}

	/**
	 * Relevant includes.
	 */
	public function includes() {
		include_once dirname( __FILE__ ) . '/includes/i18n.php';
		include_once dirname( __FILE__ ) . '/includes/functions.php';
		include_once dirname( __FILE__ ) . '/includes/Shortcode.php';
		include_once dirname( __FILE__ ) . '/includes/Menu.php';
	}

	/**
	 * Register block if applicable.
	 */
	public static function register_block_type() {
		if ( ! function_exists( 'register_block_type' ) ) {
			// Gutenberg is not active.
			return;
		}

		// wp_register_style(
		// 	'searchwp-modal-form-block',
		// 	plugin_dir_url( __FILE__ ) . 'assets/dist/block.build.css',
		// 	array(),
		// 	SEARCHWP_MODAL_FORM_VERSION
		// );

		wp_register_script(
			'searchwp-modal-form-block',
			plugin_dir_url( __FILE__ ) . 'assets/dist/block.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-components', 'wp-data', 'wp-editor', 'wp-element' ),
			SEARCHWP_MODAL_FORM_VERSION,
			true
		);

		// Define our Template SelectControl data.
		$templates = array_values( array_map(
			function( $template ) {
				$template_label = $template['template_label'];

				if ( function_exists( 'SWP' ) ) {
					$template_label = $template['engine_label'] . ' Engine - ' . $template_label;
				}
				return array(
					'label' => $template_label,
					'value' => $template['name'],
				);
			},
			searchwp_modal_form_get_forms()
		) );

		wp_localize_script(
			'searchwp-modal-form-block',
			'_SEARCHWP_MODAL_FORM_DATA',
			array(
				'templates' => $templates,
				'searchwp'  => function_exists( 'SWP' ),
			)
		);

		register_block_type(
			'searchwp/modal-form',
			array(
				'editor_script'   => 'searchwp-modal-form-block',
				'editor_style'    => 'searchwp-modal-form-block',
				'attributes'      => array(
					'engine'   => array( 'type' => 'string' ),
					'template' => array( 'type' => 'string' ),
					'text'     => array( 'type' => 'string' ),
					'type'     => array( 'type' => 'string' ),
				),
				'render_callback' => array( get_called_class(), 'render_block_modal_form' ),
			)
		);

		// TODO: Implement i18n.
		// if ( function_exists( 'wp_set_script_translations' ) ) {
		// 	wp_set_script_translations( 'searchwp-modal-form-block', 'searchwpmodalform', SEARCHWP_MODAL_FORM_DIR . '/languages' );
		// }
	}

	/**
	 * Render the server-side block on the front-end.
	 *
	 * @param array  $attributes The block attributes.
	 * @param string $content The block inner content.
	 *
	 * @return string
	 */
	public static function render_block_modal_form( $attributes, $content ) {
		$args = array_merge(
			$attributes,
			array(
				'post_id' => empty( $_GET['post_id'] ) ? null : abs( $_GET['post_id'] ), // phpcs:ignore
			)
		);

		$args['echo'] = false;

		return searchwp_modal_form_trigger( $args );
	}

	/**
	 * Add a block category for SearchWP if it doesn't exist already.
	 *
	 * @param array $categories Array of block categories.
	 *
	 * @return array
	 */
	public static function block_categories( $categories ) {
		$category_slugs = wp_list_pluck( $categories, 'slug' );
		return in_array( 'searchwp', $category_slugs, true ) ? $categories : array_merge(
			$categories,
			array(
				array(
					'slug'  => 'searchwp',
					'title' => 'SearchWP',
					'icon'  => null,
				),
			)
		);
	}
}

// Kickoff!
new SearchWP_Modal_Form();
