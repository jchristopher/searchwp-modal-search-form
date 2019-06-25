<?php
/**
 * SearchWP Modal Form Name: Default
 */

// The above comment block is REQUIRED to register this modal search form
// template with SearchWP Modal Form. This template file must also be placed
// in an applicable folder as outlined in the documentation.
// More info: https://searchwp.com/extensions/modal-form/#templates
?>

<?php
/**
 * This is the default markup for a SearchWP Modal Form. The structure is intended
 * to allow for rapid customization, based on WordPress' get_search_form() to output
 * the form markup.
 *
 * When creating your own custom modal search form templates, you should use a unique
 * namespace, replacing all occurrences of `searchwp-modal-form-default` with your own.
 *
 * You can tell the SearchWP Modal Form to close when an element is clicked by adding
 * the `data-searchwp-modal-form-close` attribute which has been added to both the
 * overlay and the close button in the footer in the default markup.
 *
 * See notes above the <style/> block below the markup for further documentation.
 *
 * More info: https://searchwp.com/extensions/modal-form/#templates
 */
?>
<div class="searchwp-modal-form-default">
	<div class="searchwp-modal-form__overlay" tabindex="-1" data-searchwp-modal-form-close>
		<div class="searchwp-modal-form__container" role="dialog" aria-modal="true">
			<main class="searchwp-modal-form__content">
				<?php echo get_search_form(); ?>
			</main>
			<footer class="searchwp-modal-form__footer">
				<button class="searchwp-modal-form__close button" aria-label="Close" data-searchwp-modal-form-close></button>
			</footer>
		</div>
	</div>
</div>

<?php
/**
 * As with the default markup, the default CSS is designed with simplicity in mind
 * allowing you to easily implement additional styles as you see fit.
 *
 * Ideally these styles will be moved into your theme's main stylesheet and/or loaded
 * conditionally by you where applicable. The CSS will work as expected when output
 * from within this template file, but it's not necessarily best practice.
 *
 * When creating your own custom modal search form templates, you should use a unique
 * namespace, replacing all occurrences of `searchwp-modal-form-default` with your own.
 *
 * The styles are broken into three 'parts'
 *   1) Overlay and container positioning
 *   2) WordPress search form customization
 *   3) Animation and display interaction setup
 *
 * More info: https://searchwp.com/extensions/modal-form/#templates
 */
?>
<style type="text/css">
	/* ************************************
	 *
	 * 1) Overlay and container positioning
	 *
	 ************************************ */
	.searchwp-modal-form-default .searchwp-modal-form__overlay {
		background: rgba(45, 45, 45 ,0.6);
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 9999990;
	}

	.searchwp-modal-form-default .searchwp-modal-form__container {
		width: 100%;
		max-width: 500px;
		max-height: 100vh;
	}

	.searchwp-modal-form-default .searchwp-modal-form__content {
		background-color: #fff;
		padding: 2em;
		border-radius: 2px;
		overflow-y: auto;
		box-sizing: border-box;
		position: relative;
		z-index: 9999998;
	}




	/* **************************************
	 *
	 * 2) WordPress search form customization
	 *
	 ************************************** */
	.searchwp-modal-form-default .searchwp-modal-form__content .search-form {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.searchwp-modal-form-default .searchwp-modal-form__content .search-form label {
		flex: 1;
		padding-right: 0.75em; /* This may cause issues depending on your theme styles. */

		/* Some common resets */
		float: none;
		margin: 0;
		width: auto;
	}

	.searchwp-modal-form-default .searchwp-modal-form__content .search-form label input {
		display: block;
		width: 100%;
		margin-left: 0.75em;

		/* Some common resets */
		float: none;
		margin: 0;
	}

	.searchwp-modal-form-default .searchwp-modal-form__footer {
		padding-top: 1em;
	}

	.searchwp-modal-form-default .searchwp-modal-form__close {
		line-height: 1;
		display: block;
		margin: 0 auto;
		background: transparent;
		border: 0;
		padding: 0.4em 0.5em;
	}

	.searchwp-modal-form-default .searchwp-modal-form__close:before {
		content: "\00d7";
		font-size: 2em;
	}




	/* ******************************************
	 *
	 * 3) Animation and display interaction setup
	 *
	 ***************************************** */
	@keyframes searchwpModalFadeIn {
		from { opacity: 0; }
		to { opacity: 1; }
	}

	@keyframes searchwpModalFadeOut {
		from { opacity: 1; }
		to { opacity: 0; }
	}

	@keyframes searchwpModalSlideIn {
		from { transform: translateY(15%); }
		to { transform: translateY(0); }
	}

	@keyframes searchwpModalSlideOut {
		from { transform: translateY(0); }
		to { transform: translateY(-10%); }
	}

	.searchwp-modal-form {
		display: none;
	}

	.searchwp-modal-form.is-open {
		display: block;
	}

	.searchwp-modal-form[aria-hidden="false"] .searchwp-modal-form-default .searchwp-modal-form__overlay {
		animation: searchwpModalFadeIn .3s cubic-bezier(0.0, 0.0, 0.2, 1);
	}

	.searchwp-modal-form[aria-hidden="false"] .searchwp-modal-form-default .searchwp-modal-form__container {
		animation: searchwpModalSlideIn .3s cubic-bezier(0, 0, .2, 1);
	}

	.searchwp-modal-form[aria-hidden="true"] .searchwp-modal-form-default .searchwp-modal-form__overlay {
		animation: searchwpModalFadeOut .3s cubic-bezier(0.0, 0.0, 0.2, 1);
	}

	.searchwp-modal-form[aria-hidden="true"] .searchwp-modal-form-default .searchwp-modal-form__container {
		animation: searchwpModalSlideOut .3s cubic-bezier(0, 0, .2, 1);
	}

	.searchwp-modal-form-default .searchwp-modal-form__container,
	.searchwp-modal-form-default .searchwp-modal-form__overlay {
		will-change: transform;
	}
</style>
