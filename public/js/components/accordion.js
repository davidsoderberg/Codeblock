module.exports = (function ($) {
	$.fn.accordion = function () {
		var self = this;
		$(self.selector + '.accordion ul li > a').click(function (event) {
			event.preventDefault();
			if ($(this).parent().hasClass('open')) {
				$(this).parent().removeClass('open');
			} else {
				$(self.selector + '.accordion ul li').removeClass('open');
				$($(self.selector + '.accordion ul li').get($(this).parent().index())).addClass('open');
			}
		});
	};
}(jQuery));
