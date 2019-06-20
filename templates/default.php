<?php
/**
 * SearchWP Modal Form Name: Default
 */
?>

<div class="searchwp-modal-form-default">
	<div class="searchwp-modal-form__overlay" tabindex="-1" data-searchwp-modal-form-close>
		<div class="searchwp-modal-form__container" role="dialog" aria-modal="true">
			<main class="searchwp-modal-form__content" id="searchwp-modal-form-default-content">
				<?php echo get_search_form(); ?>
			</main>
			<footer class="searchwp-modal-form__footer">
				<button class="searchwp-modal-form__close button" aria-label="Close" data-searchwp-modal-form-close></button>
			</footer>
		</div>
	</div>
</div>

<style type="text/css">
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

	.searchwp-modal-form-default .searchwp-modal-form__content .search-form {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.searchwp-modal-form-default .searchwp-modal-form__content .search-form label {
		flex: 1;
		/*padding-right: 0.75em;*/ /* This may cause issues... */

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

	/* Animation and display interaction setup */
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
