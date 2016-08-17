module.exports = (self) => {
	var selector = self.selector;
	var open = 'open';
	var ActiveTab = 'ActiveTab';
	var lastUl = jQuery(selector + ' ul:last-of-type li');
	var inStorage = localStorage.getItem(ActiveTab);

	if (inStorage !== null) {
		jQuery(selector + ' ul:first-of-type li').removeClass(open);
		var selectedTab = jQuery(selector + ' ul li a:contains("' + inStorage + '")').parent();
		selectedTab.addClass(open);
		lastUl.removeClass(open);
		jQuery(lastUl.get(selectedTab.index())).addClass(open);
	}

	jQuery(selector + ' ul:first-of-type li a').on('click', (event) => {
		event.preventDefault();
		localStorage.setItem(ActiveTab, jQuery(event.currentTarget).text());
		jQuery(selector + ' ul li').removeClass(open);
		jQuery(event.currentTarget).parent().addClass(open);
		jQuery(lastUl.get(jQuery(event.currentTarget).parent().index())).addClass(open);
	});
};
