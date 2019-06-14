import MicroModal from 'micromodal';

MicroModal.init({
	onShow: function(modal) {
		let $el = modal.querySelectorAll('[name="s"]');
		$el.forEach(function($input) {
			$input.focus();
			$input.select();
		});
	}
});
