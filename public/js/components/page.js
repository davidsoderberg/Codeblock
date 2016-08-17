var page = {
	init: () => {
		page.bindEvents();
	},

	bindEvents: () => {
		jQuery('#header .menu li a.search').click(page.search);
		jQuery('li.dropdown > a').click(page.dropdownLink);
		jQuery('.toogleModal').click(page.toggleModal);
		jQuery('#menubutton').click(page.menu);
		jQuery('.close-alert').click(page.closeAlert);
		jQuery('.menu-button a:first-of-type, .close-button').on('click', page.closeMenu);
	},

	search: (event) => {
		event.preventDefault();
		jQuery(event.currentTarget).remove();
		jQuery('#header .menu li form').animate({width: 'toggle'}, 1000);
		jQuery('#header .menu li form input').focus();
	},

	dropdownLink: (event) => {
		event.preventDefault();
		jQuery(event.currentTarget).toggleClass('hideUl');
	},

	toggleModal: (event) => {
		event.preventDefault();
		jQuery('.modal').toggleClass('open');
	},

	menu: (event) => {
		jQuery(event.currentTarget).toggleClass('hideUl');
	},

	closeMenu: (event) => {
		event.preventDefault();
		jQuery('body').toggleClass('show-menu');
	},

	closeAlert: (event) => {
		jQuery(event.currentTarget).parent().fadeOut('slow', (event) => {
			jQuery(event.currentTarget).remove();
		});
	}
};

module.exports = page;
