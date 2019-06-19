<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormMenu {

	function __construct() {
		// Appearance > Menus.
		add_action( 'load-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );

		// Customizer.
		// add_filter( 'customize_nav_menu_available_item_types',
		// 	array( $this, 'register_customize_nav_menu_item_types' ), 21 );
		// add_filter( 'customize_nav_menu_available_items',
		// 	array( $this, 'register_customize_nav_menu_items' ), 21, 4 );
	}

	function add_nav_menu_meta_boxes() {
		add_meta_box(
			'searchwp_modal_form_nav_link',
			__( 'SearchWP Modal Search Forms', 'searchwpmodalform' ),
			array( $this, 'nav_menu_links' ),
			'nav-menus',
			'side',
			'low'
		);
	}

	/**
	 * Output menu links.
	 */
	public function nav_menu_links() {
		$forms = searchwp_get_modal_forms();

		?>
		<div id="posttype-searchwp-modal-forms" class="posttypediv">
			<div id="tabs-panel-searchwp-modal-forms" class="tabs-panel tabs-panel-active">
				<ul id="searchwp-modal-forms-checklist" class="categorychecklist form-no-clear">
					<?php
					$i = -1;
					foreach ( $forms as $key => $value ) :
						?>
						<li>
							<label class="menu-item-title">
								<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $value ); ?>
							</label>
							<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
							<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( $value ); ?>" />
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="<?php echo esc_url( wc_get_account_endpoint_url( $key ) ); ?>" />
							<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
						</li>
						<?php
						$i--;
					endforeach;
					?>
				</ul>
			</div>
			<p class="button-controls">
				<span class="list-controls">
					<a href="<?php echo esc_url( admin_url( 'nav-menus.php?page-tab=all&selectall=1#posttype-searchwp-modal-forms' ) ); ?>" class="select-all"><?php esc_html_e( 'Select all', 'searchwpmodalform' ); ?></a>
				</span>
				<span class="add-to-menu">
					<button type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to menu', 'searchwpmodalform' ); ?>" name="add-post-type-menu-item" id="submit-posttype-searchwp-modal-forms"><?php esc_html_e( 'Add to menu', 'searchwpmodalform' ); ?></button>
					<span class="spinner"></span>
				</span>
			</p>
		</div>
		<?php
	}

	/**
	 * Register customize new nav menu item types.
	 * This will register SearchWP Modal Search Forms as a nav menu item type.
	 *
	 * @since  1,0
	 * @param  array $item_types Menu item types.
	 * @return array
	 */
	public function register_customize_nav_menu_item_types( $item_types ) {
		$item_types[] = array(
			'title'      => __( 'SearchWP Modal Search Forms', 'searchwpmodalform' ),
			'type_label' => __( 'SearchWP Modal Search Form', 'searchwpmodalform' ),
			'type'       => 'searchwp_modal_form_nav',
			'object'     => 'searchwp_modal_form',
		);

		return $item_types;
	}

	/**
	 * Register account endpoints to customize nav menu items.
	 *
	 * @since  3.1.0
	 * @param  array   $items  List of nav menu items.
	 * @param  string  $type   Nav menu type.
	 * @param  string  $object Nav menu object.
	 * @param  integer $page   Page number.
	 * @return array
	 */
	public function register_customize_nav_menu_items( $items = array(), $type = '', $object = '', $page = 0 ) {
		if ( 'searchwp_modal_form' !== $object ) {
			return $items;
		}

		// Don't allow pagination since all items are loaded at once.
		if ( 0 < $page ) {
			return $items;
		}

		// Get items from account menu.
		$forms = searchwp_get_modal_forms();

		foreach ( $forms as $form => $title ) {
			$items[] = array(
				'id'         => $form,
				'title'      => $title,
				'type_label' => __( 'Modal Search', 'searchwpmodalform' ),
				'url'        => esc_url_raw( site_url() ),
			);
		}

		return $items;
	}
}

new SearchWPModalFormMenu();
