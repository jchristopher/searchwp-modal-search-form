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

/**
 * Class SearchWP_Modal_Form
 */
class SearchWP_Modal_Form {

	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'assets' ) );
	}

	function display() {
		include 'views/default.php';
	}

	function assets() {
		$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG === true ) || ( isset( $_GET['script_debug'] ) ) ? '' : '.min';

		wp_enqueue_script(
			'searchwp-modal-form',
			plugin_dir_url( __FILE__ ) . "assets/dist/searchwp-modal-form${debug}.js",
			array(),
			SEARCHWP_MODAL_FORM_VERSION,
			true
		);
	}
}

new SearchWP_Modal_Form();




/**
 * Instantiate the updater
 */
if ( ! class_exists( 'SWP_Modal_Form_Updater' ) ) {
	// load our custom updater
	include_once( dirname( __FILE__ ) . '/updater.php' );
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
	$searchwp_modal_form_updater = new SWP_Modal_Form_Updater( SEARCHWP_EDD_STORE_URL, __FILE__, array(
			'item_id' 	=> 184439,
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
