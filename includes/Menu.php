<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SearchWPModalFormMenu {

	public function __construct() {
		add_action( 'load-nav-menus.php', array( $this, 'add_nav_menu_meta_boxes' ) );
		add_action( 'admin_print_footer_scripts-nav-menus.php', array( $this, 'customize_nav_items' ) );
		add_filter( 'wp_nav_menu', array( $this, 'wp_nav_menu' ), 10, 2 );
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

		// TODO: We need to check for the existence of this class on init of this plugin as a whole.
		$dom = new DOMDocument();
		$dom->loadHTML( $nav_menu );

		foreach ( $dom->getElementsByTagName( 'a' ) as $link ) {

			// If there's no URI flag for a modal, skip this.
			$modal_name = searchwp_modal_form_get_name_from_uri( $link->getAttribute( 'href' ) );
			if ( ! $modal_name ) {
				continue;
			}

			// Attach our data attribute that acts as a trigger for this modal.
			$link->setAttribute( 'data-searchwp-modal-trigger', esc_attr( 'searchwp-modal-' . $modal_name ) );

			// Enqueue modal template.
			add_filter( 'searchwp_modal_form_queue', function( $forms ) use ( $modal_name ) {
				$forms[] = $modal_name;

				return $forms;
			} );
		}

		// We have a fully developed HTML document, but we only want the menu itself.
		$full_html = $dom->saveHTML();
		$nav_menu = substr(
			$full_html,
			strpos( $full_html, '<body>' ) + 6,
			strpos( $full_html, '</body>' )
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
						var data = _SEARCHWP_MODAL_FORMS[ hash ];

						$this.find('.item-type').text('Modal Search Form');
						$this.find('.menu-item-settings')
							.prepend('<p style="margin-bottom: 1em;" class="description searchwp-modal-search-form-note">This is a SearchWP Modal Search Form.<br><strong>Engine:</strong> ' + data.engine_label  + '<br><strong>Template:</strong> ' + data.template_label + '</p>')
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

			<?php
			/*
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
			*/
			?>

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
