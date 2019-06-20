<?php
/*
Plugin Name: SearchWP Modal Form
Plugin URI: https://searchwp.com/
Description: Lightweight and accessible search form
Version: 0.1
Author: SearchWP, LLC
Author URI: https://searchwp.com/

Copyright 2019 SearchWP, LLC

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
	define( 'SEARCHWP_MODAL_FORM_VERSION', '0.1' );
}

if ( ! defined( 'SEARCHWP_MODAL_FORM_DIR' ) ) {
	define( 'SEARCHWP_MODAL_FORM_DIR', dirname( __FILE__ ) );
}

/**
 * Class SearchWP_Modal_Form
 */
class SearchWP_Modal_Form {

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

		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min';

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
			$modal = searchwp_modal_form_reverse_hash_lookup( $modal_hash );
			?>
			<div class="searchwp-modal-form" id="<?php echo esc_attr( 'searchwp-modal-' . $modal_hash ); ?>" aria-hidden="true">
				<?php
				if ( file_exists( $modal['template']['file'] ) ) {
					include $modal['template']['file'];
				} else {
					echo esc_html_e( 'Template not found!', 'searchwpmodalform' );
				}
				?>
			</div>
			<?php
		}
	}

	/**
	 * Relevant includes.
	 */
	public function includes() {
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

		wp_register_style(
			'searchwp-modal-form-block',
			plugin_dir_url( __FILE__ ) . 'assets/dist/block.build.css',
			array(),
			SEARCHWP_MODAL_FORM_VERSION
		);

		wp_register_script(
			'searchwp-modal-form-block',
			plugin_dir_url( __FILE__ ) . 'assets/dist/block.build.js',
			array( 'wp-blocks', 'wp-i18n', 'wp-components', 'wp-data', 'wp-editor', 'wp-element' ),
			SEARCHWP_MODAL_FORM_VERSION,
			true
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
				'render_callback' => array( $this, 'render_block_modal_form' ),
			)
		);

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
				'post_id' => empty( $_GET['post_id'] ) ? null : abs( $_GET['post_id'] ),
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


if ( ! class_exists( 'SWP_Modal_Form_Updater' ) ) {
	include_once dirname( __FILE__ ) . '/updater.php';
}

/**
 * Set up the updater
 *
 * @return bool|SWP_Modal_Form_Updater
 */
function searchwp_modal_form_update_check() {

	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return false;
	}

	// environment check
	if ( ! defined( 'SEARCHWP_PREFIX' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_EDD_STORE_URL' ) ) {
		return false;
	}

	if ( ! defined( 'SEARCHWP_MODAL_FORM_VERSION' ) ) {
		return false;
	}

	// retrieve stored license key
	$license_key = trim( get_option( SEARCHWP_PREFIX . 'license_key' ) );
	$license_key = sanitize_text_field( $license_key );

	// instantiate the updater to prep the environment
	$searchwp_modal_form_updater = new SWP_Modal_Form_Updater(
		SEARCHWP_EDD_STORE_URL,
		__FILE__,
		array(
			'item_id'   => 184439,
			'version'   => SEARCHWP_MODAL_FORM_VERSION,
			'license'   => $license_key,
			'item_name' => 'Modal Form',
			'author'    => 'SearchWP, LLC',
			'url'       => site_url(),
		)
	);

	return $searchwp_modal_form_updater;
}

add_action( 'admin_init', 'searchwp_modal_form_update_check' );
