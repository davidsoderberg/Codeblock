module.exports = {
	init: function () {
		this.bindEvents();
	},

	bindEvents: function () {
		jQuery('#header .menu li a.search').click(this.search);
		jQuery('li.dropdown > a').click(this.dropdownLink);
		jQuery('.toogleModal').click(this.toggleModal);
		jQuery('#menubutton').click(this.menu);
		jQuery('.close-alert').click(this.closeAlert);
		jQuery('.menu-button a:first-of-type, .close-button').on('click', this.closeMenu);
	},

	search: function (event) {
		event.preventDefault();
		jQuery(this).remove();
		jQuery('#header .menu li form').animate({width: 'toggle'}, 1000);
		jQuery('#header .menu li form input').focus();
	},

	dropdownLink: function (event) {
		event.preventDefault();
		jQuery(this).toggleClass('hideUl');
	},

	toggleModal: function (event) {
		event.preventDefault();
		jQuery('.modal').toggleClass('open');
	},

	menu: function () {
		jQuery(this).toggleClass('hideUl');
	},

	closeMenu: function (event) {
		event.preventDefault();
		jQuery('body').toggleClass('show-menu');
	},

	closeAlert: function () {
		jQuery(this).parent().fadeOut('slow', function () {
			jQuery(this).remove();
		});
	}
};
