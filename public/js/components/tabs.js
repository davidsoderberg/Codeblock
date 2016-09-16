module.exports = (self) => {
	for(var i = 0; i < self.length; i++) {
		var that = self[i];
		var id = jQuery(that).attr('id');
		if (id !== undefined) {
			var selector = '#' + id;
			var lastUl = jQuery(selector + ' > ul:last-of-type > li');
			var open = 'open';
			var ActiveTab = 'ActiveTab-' + id;
			var inStorage = localStorage.getItem(ActiveTab);

			if (inStorage !== null) {
				jQuery(selector + ' > ul:first-of-type li').removeClass(open);
				var selectedTab = jQuery(selector + ' > ul > li a:contains("' + inStorage + '")').parent();
				selectedTab.addClass(open);
				lastUl.removeClass(open);
				jQuery(lastUl.get(selectedTab.index())).addClass(open);
			}

			jQuery(selector + ' > ul:first-of-type > li > a').on('click', (event) => {
				event.preventDefault();
				var id = jQuery(event.currentTarget).parent().parent().parent().attr('id');
				var ActiveTab = 'ActiveTab-' + id;


				localStorage.setItem(ActiveTab, jQuery(event.currentTarget).text());
				jQuery(event.currentTarget).parent().parent().find('li').removeClass(open);
				jQuery(event.currentTarget).parent().addClass(open);
				lastUl = jQuery(event.currentTarget).parent().parent().next();
				lastUl.find('li.open').removeClass('open');
				var currentLi = lastUl.find('li')[jQuery(event.currentTarget).parent().index()];
				jQuery(currentLi).addClass(open);
			});
		}
	}
};
