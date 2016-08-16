global.jQuery = require('../../bower_components/jquery-legacy/jquery');
var page = require('./components/page');
var post = require('./components/post');
var async = require('./components/async');
require('../../bower_components/chosen/chosen.jquery.js');
global.CodeMirror = require('../../bower_components/codemirror/lib/codemirror.js');
require('../../bower_components/mention/bootstrap-typeahead.js');
require('../../bower_components/mention/mention.js');
require('./components/accordion');
require('./components/tabs');
require('./components/validator');

jQuery(document).ready(function ($) {

	page.init();
	post.init();
	async.init(appConfig);

	if ($.fn.chosen) {
		$(".chosen-select").chosen();
	}

	if ($.fn.validator) {
		$('form').validator({keypress: false});
	}

	if ($.fn.tabs) {
		$('#browseTabs').tabs();
	}

	if ($.fn.accordion) {
		$('#accordion').accordion();
	}
});
