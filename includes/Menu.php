<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormMenu {

	public function __construct() {
		add_action( 'load-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );
		add_action( 'admin_print_footer_scripts-nav-menus.php', array( $this, 'customize_nav_items' ) );
	}

	/**
	 * Customize Menu nav items to provide a UI that makes sense for what we're doing.
	 * Why do we not use PHP to do this you may ask? Valid question! It's because in order
	 * to customize individual Menu items you need to use a custom Walker, which works.
	 * Unfortunately because of the implementation of Walkers, there can be only one. Many
	 * other plugins/themes use their own Walkers to do this exact job (e.g. ACF) and if
	 * we also do this job, we're overriding everything they do. This forces us to resort
	 * to JavaScript hacks based on a known URL structure for the Menu item that allows us
	 * to determine which Menu items are in fact ours, and we can then customize from there.
	 */
	public function customize_nav_items() {
		?>
			<script type="text/javascript">
				var searchwp_modal_forms_update_menu_items = function() {
					let $menu = jQuery('#menu-to-edit');

					// Pluck out our menu items based on their URL.
					let $nav_items = $menu.children().filter(function(index){
						let $nav_item_url_field = jQuery( '.menu-item-settings .field-url input', this );

						if(!$nav_item_url_field.length) {
							return false;
						}

						let nav_item_url = $nav_item_url_field.val().substr(0, 16);
						return nav_item_url === '#searchwp-modal-';
					});

					// Hide WordPress core UI that's not applicable.
					$nav_items.each(function(){
						var $this = jQuery(this);
						$this.find('.item-type').text('Modal Search Form');
						$this.find('.menu-item-settings')
							.prepend('<p style="margin-bottom: 1em;" class="description searchwp-modal-search-form-note">This is a SearchWP Modal Search Form. You can customize the details of this Menu Item <a href="#">here</a>.</p>')
							.children()
							.not('.description, .field-move, .menu-item-actions, .searchwp-modal-search-form-note')
							.hide();
						$this.find('p.description.field-url').hide();
					});
				};

				jQuery(document).ready(function($){
					searchwp_modal_forms_update_menu_items();
				}).on('menu-item-added', function($args) {
					searchwp_modal_forms_update_menu_items();
				});
			</script>
		<?php
	}

	public function add_nav_menu_meta_boxes() {
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
	 * Output Menu links as group in 'Add menu items' list
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
							<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="#searchwp-modal-<?php echo esc_attr( $key ); ?>" />
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
}

new SearchWPModalFormMenu();
