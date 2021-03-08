<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormMenu {

	public function __construct() {
		// We rely on DOMDocument when outputting Menu Items.
		if ( class_exists( 'DOMDocument' ) ) {
			add_action( 'load-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );
			add_action( 'admin_print_footer_scripts-nav-menus.php', array( $this, 'customize_nav_items' ) );
			add_filter( 'wp_nav_menu', array( $this, 'wp_nav_menu' ), 10, 2 );
			add_filter( 'wp_setup_nav_menu_item', array( $this, 'check_menu_item' ) );
		}
	}

	/**
	 * Checks whether an existing Menu Item is valid.
	 */
	public function check_menu_item( $menu_item ) {
		$modal_name = searchwp_modal_form_get_name_from_uri( $menu_item->url );

		// If it's not a Modal Form Menu Item, return it.
		if ( empty( $modal_name ) ) {
			return $menu_item;
		}

		// Make sure that the Menu Item is still valid by verifying the modal name.
		$forms = searchwp_modal_form_get_forms();
		if ( ! array_key_exists( $modal_name, $forms ) ) {
			$menu_item->_invalid = true;
		}

		return $menu_item;
	}

	/**
	 * Because we can't walk Menus directly (see note for @customize_nav_items)
	 * we're going to manually process the fully generated HTML because
	 * there's no other reliable way to do it so here we go...
	 */
	public function wp_nav_menu( $nav_menu, $args ) {
		// If there's no reference to our modal URL flag, bail out.
		if ( false === strpos( strtolower( $nav_menu ), '#searchwp-modal-' ) ) {
			return $nav_menu;
		}

		$dom = new DOMDocument();
		libxml_use_internal_errors( true );

		if ( function_exists( 'utf8_decode' ) ) {
			$dom->loadHTML( utf8_decode( $nav_menu ) );
		} else {
			$dom->loadHTML( $nav_menu );
		}

		foreach ( $dom->getElementsByTagName( 'a' ) as $link ) {

			// If there's no URI flag for a modal, skip this.
			$modal_name = searchwp_modal_form_get_name_from_uri( $link->getAttribute( 'href' ) );

			if ( empty( $modal_name ) ) {
				continue;
			}

			// Ensure that the modal name is valid.
			$forms = searchwp_modal_form_get_forms();
			if ( array_key_exists( $modal_name, $forms ) ) {
				// Attach our data attribute that acts as a trigger for this modal.
				$link->setAttribute( 'data-searchwp-modal-trigger', esc_attr( 'searchwp-modal-' . $modal_name ) );

				// Enqueue modal template.
				add_filter( 'searchwp_modal_form_queue', function( $forms ) use ( $modal_name ) {
					$forms[] = $modal_name;

					return $forms;
				} );
			} else {
				$link->nodeValue = $link->nodeValue . ' ' . __( '(SearchWP Modal Form error!)', 'searchwpmodalform' ); // phpcs:ignore
			}
		}

		// We have a fully developed HTML document, but we only want the menu itself.
		$full_html = $dom->saveHTML();
		$start = strpos( $full_html, '<body>' ) + 6;
		$length = strpos( $full_html, '</body>' ) - $start;
		$nav_menu = substr(
			$full_html,
			$start,
			$length
		);

		return $nav_menu;
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
				var _SEARCHWP_MODAL_FORMS = JSON.parse('<?php echo wp_json_encode( searchwp_modal_form_get_forms() ); ?>');

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

						var hash = $this.find('.field-url input').val().substr(16);

						// If there's no matching hash, it's because an existing Menu Item
						// is no longer valid. This can happen if a SearchWP-engine'd Menu
						// Item was created, but SearchWP is no longer active.
						// TODO: i18n.
						var menu_item_note = '<span class="dashicons dashicons-info"></span> The configured SearchWP Modal Form cannot be loaded. Please remove this Menu Item and add a newly configured SearchWP Modal Form in its place.';

						// If there IS a matching hash, we can update the Menu Item accordingly.
						if (_SEARCHWP_MODAL_FORMS.hasOwnProperty(hash)) {
							var data = _SEARCHWP_MODAL_FORMS[ hash ];

							// TODO: i18n.
							menu_item_note = 'This is a SearchWP Modal Search Form.<br><strong>Engine:</strong> ' + data.engine_label  + '<br><strong>Template:</strong> ' + data.template_label;
						}

						// Set a proper title, customize content, and hide inapplicable elements.
						$this.find('.item-type').text('Modal Search Form');
						$this.find('.menu-item-settings')
							.prepend('<p style="margin-bottom: 1em;" class="description searchwp-modal-search-form-note">' + menu_item_note + '</p>')
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
		$forms = searchwp_modal_form_get_forms();

		?>
		<div id="posttype-searchwp-modal-forms" class="posttypediv">
			<div id="tabs-panel-searchwp-modal-forms" class="tabs-panel-active">
				<?php if ( ! class_exists( 'SearchWP' ) && apply_filters( 'searchwp_modal_form_upgrade_link', true ) ) : ?>
					<p style="display: flex; align-items: center; margin-top: -0.8em;">
						<span style="transform: scale(0.75); margin-right: 0.25em;"><span class="dashicons dashicons-star-filled"></span></span>
						<span>Improve your search with <a href="https://searchwp.com/?utm_source=wordpressorg&utm_medium=link&utm_campaign=modalform&utm_content=menuitem" target="_BLANK">SearchWP!</a>
						</span>
					</p>
				<?php endif; ?>
				<?php /* This markup is because of WordPress' JS selectors... the inline styles are because of lazy and MVP :) */ ?>
				<ul class="menu-item-title">
					<li>
						<table style="width: 100%; margin-top: 0; border-collapse: collapse;" id="searchwp-modal-forms-checklist" class="categorychecklist form-no-clear">
							<thead>
								<tr>
									<th></th>
									<th><?php echo esc_html_e( 'Engine', 'searchwp' ); ?></th>
									<th><?php echo esc_html_e( 'Template', 'searchwp' ); ?></th>
								</tr>
							</thead>
							<tbody>
							<?php
							$i = -1; // This is borrowed from WooCommerce's implementation, but can probably be a proper count, there's a global IIRC.
							foreach ( $forms as $key => $value ) :
								$input_id = 'searchwp-modal-ref-' . absint( $i );
								?>
								<tr>
									<td style="padding: 0.25em 0;">
									<?php /* This markup is because of WordPress' JS selectors... the inline styles are because of lazy and MVP :) */ ?>
										<ul>
											<li style="margin: 0;">
												<input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" id="<?php echo esc_attr( $input_id ); ?>" />
												<input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
												<input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( $value['engine_label'] ); ?>" />
												<input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="#searchwp-modal-<?php echo esc_attr( $value['name'] ); ?>" />
												<input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" />
											</li>
										</ul>
									</td>
									<td style="padding: 0.25em 0;">
										<label style="display: block;" for="<?php echo esc_attr( $input_id ); ?>">
											<?php echo esc_html( $value['engine_label'] ); ?>
										</label>
									</td>
									<td style="padding: 0.25em 0;">
										<label style="display: block;" for="<?php echo esc_attr( $input_id ); ?>">
											<?php echo esc_html( $value['template_label'] ); ?>
										</label>
									</td>
								</tr>
								<?php
								$i--;
								endforeach;
							?>
							</tbody>
						</table>
					</li>
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
