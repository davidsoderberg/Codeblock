module.exports = (self) => {
	var selector = self.selector;
	jQuery(selector + '.accordion ul li > a').click((event) => {
		event.preventDefault();
		if (jQuery(event.currentTarget).parent().hasClass('open')) {
			jQuery(event.currentTarget).parent().removeClass('open');
		} else {
			jQuery(selector + '.accordion ul li').removeClass('open');
			jQuery(jQuery(selector + '.accordion ul li').get(jQuery(event.currentTarget).parent().index())).addClass('open');
		}
	});
};
