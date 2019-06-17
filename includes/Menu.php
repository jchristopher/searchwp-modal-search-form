<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormMenu {

	function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	 * Initializer
	 */
	function init() {
		add_action( 'admin_init', array( $this, 'add_meta_box' ) );
		add_filter( 'wp_setup_nav_menu_item', array( $this, 'customize_menu_item_label' ) );
		add_filter( 'wp_edit_nav_menu_walker', array( $this, 'wp_edit_nav_menu_walker' ) );
		add_filter( 'searchwp_modal_form.nav_menu_item_fields', array( $this, 'nav_menu_item_fields' ), 10, 2 );
		add_action( 'wp_update_nav_menu_item', array( $this, 'wp_update_nav_menu_item' ), 10, 3 );
		add_filter( 'walker_nav_menu_start_el', array( $this, 'render_modal_nav_menu_item' ), 10, 4 );
	}

	/**
	 * Output the markup of our menu item.
	 */
	public function render_modal_nav_menu_item( $item_output, $item, $depth, $args ){
		$form_name = searchwp_get_modal_name_from_menu_item( $item );

		if ( empty( $form_name ) ) {
			return $item_output;
		}

		/** This filter is documented in wp-includes/post-template.php */
		$title = apply_filters( 'the_title', $item->title, $item->ID );
		/** This filter is documented in wp-includes\nav-menu-template.php */
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$modal = new SearchWP_Modal_Form();
		ob_start();
		$modal->display();
		$item_output = ob_get_clean();

		return $item_output;
	}

	/**
	 * Callback when updating a nav menu item. Store applicable metadata.
	 */
	public function wp_update_nav_menu_item( $menu_id = 0, $menu_item_db_id = 0, $args ) {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			return;
		}

		// Update settings for existing menu items.
		if ( isset( $_REQUEST['update-nav-menu-nonce'] ) && wp_verify_nonce( $_REQUEST['update-nav-menu-nonce'], 'update-nav_menu' ) ) {

			// TODO: update any metadata we need to save.
			// if ( ! empty( $_POST['menu-item-button-text'][ $menu_item_db_id ] ) ) {
			// 	update_post_meta(
			// 		$menu_item_db_id,
			// 		'_menu_item_rcmit_button_text',
			// 		sanitize_text_field( $_POST['menu-item-button-text'][ $menu_item_db_id ] )
			// 	);
			// }
		}
	}

	/**
	 * Filters list of settings fields of a menu item.
	 *
	 * @param array $nav_menu_item_fields Mapping of ID to the field paragraph HTML.
	 * @param array $context {
	 *     Context for applied filter.
	 *
	 *     @type \Walker_Nav_Menu_Edit $walker Nav menu walker.
	 *     @type object                $item   Menu item data object.
	 *     @type int                   $depth  Current depth.
	 * }
	 * @return array Mapping of ID to the field paragraph HTML.
	 */
	public function nav_menu_item_fields( $nav_menu_item_fields, $context ) {
		$form_name = searchwp_get_modal_name_from_menu_item( $context['item'] );

		if ( empty( $form_name ) ) {
			return $nav_menu_item_fields;
		}

		unset( $nav_menu_item_fields['css-classes'] );

		// We're going to hide the URL field because that's our flag for everything.
		ob_start();
		?>
			<input type="hidden" id="edit-menu-item-custom-<?php echo $context['item']->ID; ?>" class="widefat edit-menu-item-custom" name="menu-item-custom[<?php echo $context['item']->ID; ?>]" value="<?php echo esc_attr( $context['item']->url ); ?>" />
		<?php
		$nav_menu_item_fields['custom'] = ob_get_clean();

		// TODO: figure out what we need to customize for this specific form
		// We already have the form defined from adding this nav menu item in the first place
		// so I'm not sure what else needs to be added.

		return $nav_menu_item_fields;
	}

	/**
	 * Set up our custom Nav Menu Walker to control our customization fields.
	 */
	public function wp_edit_nav_menu_walker() {
		include_once dirname( __FILE__ ) . '/MenuWalker.php';

		return 'SearchWPModalFormMenuWalker';
	}

	/**
	 * Customizes the menu label from 'Custom Link' as that's what we're utlizing.
	 * Our custom menu entries are defined by their URL being prefixed with a hash we know.
	 * We can also pull in any relevant metadata we need from here.
	 */
	public function customize_menu_item_label( $menu_item ) {
		if ( empty( searchwp_get_modal_name_from_menu_item( $menu_item ) ) ) {
			return $menu_item;
		}

		$menu_item->type_label = __( 'Modal Search Form', 'searchwpmodalform' );

		// TODO: Apply any necessary customizations stored as postmeta
		// e.g. $menu_item->custom_prop ?? get_post_meta( $menu_item->ID, '_menu_item_custom_prop', true );

		return $menu_item;
	}

	/**
	 * Add menu meta box.
	 */
	public function add_meta_box() {
		add_meta_box(
			'searchwp_modal_search_forms',
			__( 'SearchWP Modal Search Forms', 'searchwpmodalform' ),
			array( $this, 'meta_box_markup' ),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Displays a metabox for the custom links menu item.
	 *
	 * @global int|string $nav_menu_selected_id
	 */
	public function meta_box_markup() {
		global $nav_menu_selected_id;

		$forms = searchwp_get_modal_forms();
		$i     = -1;

		?>
		<div class="posttypediv" id="searchwp-modal-search-forms">
			<div id="tabs-panel-searchwp-modal-search-forms" class="tabs-panel tabs-panel-active">
				<ul id ="searchwp-modal-search-forms-checklist" class="categorychecklist form-no-clear">
					<?php foreach( $forms as $form_name => $form_label ) : ?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo $i; ?>][menu-item-object-id]" value="-1"> <?php echo esc_html( $form_label ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo $i; ?>][menu-item-type]" value="custom">
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo $i; ?>][menu-item-title]" value="<?php echo esc_attr( $form_label ); ?>">
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo $i; ?>][menu-item-url]" value="#searchwp-modal-<?php echo esc_attr( $form_name ); ?>">
						</li>
					<?php $i--; endforeach; ?>
				</ul>
			</div>
			<input type="hidden" value="custom" name="menu-item[<?php echo $i; ?>][menu-item-type]" />

			<p class="button-controls wp-clearfix">
				<span class="add-to-menu">
					<input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'custom-menu-item-types' ); ?>" name="add-custom-menu-item" id="submit-searchwp-modal-search-forms" />
					<span class="spinner"></span>
				</span>
			</p>

		</div><!-- /.searchwp-modal-search-forms -->
		<?php
	}
}

new SearchWPModalFormMenu();
