<p><a class="button" href="#" data-micromodal-trigger="modal-1">Trigger Modal</a></p>

<div class="modal micromodal-slide" id="modal-1" aria-hidden="true">
	<div class="modal__overlay" tabindex="-1" data-micromodal-close>
		<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="modal-1-title">
			<main class="modal__content" id="modal-1-content">
				<?php echo get_search_form(); ?>
			</main>
			<footer class="modal__footer">
				<button class="modal__close button" aria-label="Close modal" data-micromodal-close></button>
			</footer>
		</div>
	</div>
</div>

<style type="text/css">
	.modal__overlay {
		position: fixed;
		top: 0;
		left: 0;
		right: 0;
		bottom: 0;
		background: rgba(45, 45,45 ,0.6);
		display: flex;
		justify-content: center;
		align-items: center;
		z-index: 9999990;
	}

	.modal__container {
		width: 100%;
		max-width: 500px;
		max-height: 100vh;
	}

	.modal__content {
		background-color: #fff;
		padding: 2em;
		border-radius: 2px;
		overflow-y: auto;
		box-sizing: border-box;
		position: relative;
		z-index: 9999998;
	}

	.modal__content .search-form {
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.modal__content .search-form label {
		flex: 1;
		/*padding-right: 0.75em;*/ /* This may cause issues... */

		/* Some common resets */
		float: none;
		margin: 0;
		width: auto;
	}

	.modal__content .search-form label input {
		display: block;
		width: 100%;
		margin-left: 0.75em;

		/* Some common resets */
		float: none;
		margin: 0;
	}

	.modal__footer {
		padding-top: 1em;
	}

	.modal__close {
		line-height: 1;
		display: block;
		margin: 0 auto;
		background: transparent;
		border: 0;
		padding: 0.4em 0.5em;
	}

	.modal__close:before {
		content: "\00d7";
		font-size: 2em;
	}

	/***********\
	  Animation
	\***********/
	@keyframes mmfadeIn {
		from { opacity: 0; }
		  to { opacity: 1; }
	}

	@keyframes mmfadeOut {
		from { opacity: 1; }
		  to { opacity: 0; }
	}

	@keyframes mmslideIn {
		from { transform: translateY(15%); }
		  to { transform: translateY(0); }
	}

	@keyframes mmslideOut {
		from { transform: translateY(0); }
		  to { transform: translateY(-10%); }
	}

	.micromodal-slide {
		display: none;
	}

	.micromodal-slide.is-open {
		display: block;
	}

	.micromodal-slide[aria-hidden="false"] .modal__overlay {
		animation: mmfadeIn .3s cubic-bezier(0.0, 0.0, 0.2, 1);
	}

	.micromodal-slide[aria-hidden="false"] .modal__container {
		animation: mmslideIn .3s cubic-bezier(0, 0, .2, 1);
	}

	.micromodal-slide[aria-hidden="true"] .modal__overlay {
		animation: mmfadeOut .3s cubic-bezier(0.0, 0.0, 0.2, 1);
	}

	.micromodal-slide[aria-hidden="true"] .modal__container {
		animation: mmslideOut .3s cubic-bezier(0, 0, .2, 1);
	}

	.micromodal-slide .modal__container,
	.micromodal-slide .modal__overlay {
		will-change: transform;
	}
</style>
