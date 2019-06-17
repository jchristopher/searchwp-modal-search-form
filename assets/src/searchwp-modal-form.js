import MicroModal from 'micromodal';

MicroModal.init({
	onShow: function(modal) {
		let $el = modal.querySelectorAll('[name="s"]')[0];
		$el.focus();
		$el.select();
	}
});
