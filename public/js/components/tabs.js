(function ($) {
	$.fn.tabs = function () {
		var self = this;
		var open = 'open';
		var ActiveTab = 'ActiveTab';
		var lastUl = $(self.selector + " ul:last-of-type li");
		var inStorage = localStorage.getItem(ActiveTab);

		if (inStorage !== null) {
			$(self.selector + ' ul:first-of-type li').removeClass(open);
			var selectedTab = $(self.selector + ' ul li a:contains("' + inStorage + '")').parent();
			selectedTab.addClass(open);
			lastUl.removeClass(open);
			$(lastUl.get(selectedTab.index())).addClass(open);
		}

		$(self.selector + ' ul:first-of-type li a').on('click', function (event) {
			event.preventDefault();
			localStorage.setItem(ActiveTab, $(this).text());
			$(self.selector + ' ul li').removeClass(open);
			$(this).parent().addClass(open);
			$(lastUl.get($(this).parent().index())).addClass(open);
		});
	}
}(jQuery));