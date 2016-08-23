var toast = {
	init: () => {
		jQuery('.close-toast').click((event) => {
			event.preventDefault();
			jQuery(event.currentTarget).parent().addClass('lightSpeedOut');
			setTimeout(() => {
				jQuery(event.currentTarget).parent().remove();
			}, 3000);
		});
	},
	create: (text) => {
		var toast = jQuery('<div></div>').addClass('toast success animated lightSpeedIn');
		toast.html(text);

		jQuery('#toast-container').prepend(toast);
		setTimeout(() => {
			toast.addClass('lightSpeedOut');
			setTimeout(() => {
				toast.remove();
			}, 3000);
		}, 5000);
	}
}

module.exports = toast;