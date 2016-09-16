module.exports = (self) => {
	var id = jQuery(self).attr('id');
	if (id !== undefined) {
		var selector = '#' + id;
		var open = 'open';
		var ActiveAccordion = 'ActiveAccordion-'+id;
		var inStorage = localStorage.getItem(ActiveAccordion);

		if (inStorage !== null) {
			jQuery(selector + '.accordion > ul > li').removeClass(open);
			var currentLi = jQuery(selector + ' > ul > li > a:contains("' + inStorage + '")');
			if(currentLi.length > 1){
				currentLi = jQuery(selector + ' > ul > li > a:contains("' + inStorage + '")')[0];
			}
			jQuery(currentLi).parent().addClass(open);
		}

		jQuery(selector + '.accordion > ul > li > a').click((event) => {
			event.preventDefault();
			localStorage.setItem(ActiveAccordion, jQuery(event.currentTarget).text());
			if (jQuery(event.currentTarget).parent().hasClass(open)) {
				jQuery(event.currentTarget).parent().removeClass(open);
			} else {
				jQuery(selector + '.accordion > ul > li').removeClass(open);
				jQuery(event.currentTarget).parent().addClass(open);
			}
		});
	}
};
