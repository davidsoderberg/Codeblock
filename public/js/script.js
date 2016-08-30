global.jQuery = require('../../bower_components/jquery-legacy/jquery');
var page = require('./components/page');
var post = require('./components/post');
var async = require('./components/async');
var accordion = require('./components/accordion');
var tabs = require('./components/tabs');
var validator = require('./components/validator');
require('../../bower_components/chosen/chosen.jquery.js');

jQuery(document).ready(() => {

	page.init();
	post.init();
	async.init(appConfig);

	if (jQuery.fn.chosen) {
		jQuery(".chosen-select").chosen();
	}

	validator({keypress: false});
	tabs(jQuery('#browseTabs'));
	accordion(jQuery('#accordion'));

	jQuery('#documentation #filter').keyup((e) => {
		var val = jQuery(e.currentTarget).val().toLowerCase();
		var number_of_classes = jQuery('#documentation .wrapper:first-of-type ul li').length;
		if (val.length > 0) {
			jQuery('#documentation .wrapper:first-of-type ul li').each((index, element) => {
				var current_text = jQuery(element).find('a').text().toLowerCase();
				if (current_text.indexOf(val) !== -1) {
					jQuery(element).show();
				} else {
					jQuery(element).hide();
				}

				if (number_of_classes === (index + 1)) {
					var visible_lis = jQuery('#documentation .wrapper:first-of-type ul li:visible');
					var list_item = visible_lis[0];
					jQuery(list_item).find('a').trigger('click');
				}
			});
		} else {
			jQuery('#documentation .wrapper:first-of-type ul li').each((index, element) => {
				jQuery(element).show();

				if (number_of_classes === (index + 1)) {
					var visible_lis = jQuery('#documentation .wrapper:first-of-type ul li:visible');
					var list_item = visible_lis[0];
					jQuery(list_item).find('a').trigger('click');
				}
			});
		}
	});

	jQuery('#documentation a').click((e) => {
		e.preventDefault();

		jQuery('#documentation .wrapper:first-of-type ul li').removeClass('active');
		var id = jQuery(e.currentTarget).attr('href');
		jQuery("#documentation .wrapper:first-of-type ul li a[href='" + id + "']").parent().addClass('active');
		jQuery('#documentation .wrapper:last-of-type div').removeClass('show');
		jQuery(id).addClass('show');
	});
});
