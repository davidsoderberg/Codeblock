global.jQuery = require('../../bower_components/jquery-legacy/jquery');
var page = require('./components/page');
var post = require('./components/post');
var async = require('./components/async');
var doc = require('./components/doc');
var accordion = require('./components/accordion');
var tabs = require('./components/tabs');
var validator = require('./components/validator');
require('../../bower_components/chosen/chosen.jquery.js');

jQuery(document).ready(() => {

	page.init();
	post.init();
	async.init(appConfig);
	doc.init();

	if (jQuery.fn.chosen) {
		jQuery(".chosen-select").chosen();
	}

	validator({keypress: false});
	tabs(jQuery('.tabs'));
	accordion(jQuery('.accordion'));
});
