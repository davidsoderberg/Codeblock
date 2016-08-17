global.CodeMirror = require('../../../bower_components/codemirror/lib/codemirror.js');

var post = {
	editorArray: [],
	init: () => {
		post.bindEvents();
	},

	bindEvents: () => {
		jQuery('.code-editor').each(post.codeEditor);
		jQuery('.reply').click(post.reply);
		jQuery('.confirm').click(post.confirm);
		jQuery('#blockCategory').change(post.categoryChange);
		post.changeLang(jQuery('#blockCategory'));
		if (typeof post.editorArray['blockCode'] !== 'undefined') {
			post.editorArray['blockCode'].on('blur', post.blur);
		}
	},

	confirm: (event) => {
		event.preventDefault();
		jQuery('.confirmModal .green-background').attr('href', jQuery(event.currentTarget).attr('href'));
		jQuery('.confirmModal').toggleClass('open');
		jQuery('.confirmModal .red-background').click((event) => {
			event.preventDefault();
			jQuery('.confirmModal').toggleClass('open');
		});
	},

	blur: () => {
		var self = post;
		jQuery('#blockCode').val(self.editorArray['blockCode'].getValue());
		jQuery('#blockCode').trigger('blur');
	},

	categoryChange: (event) => {
		post.changeLang(event.currentTarget);
	},

	changeLang: (selector) => {
		var selected = jQuery(selector).find('option:selected');
		if (selected.val() > 0) {
			var value = selected.text().toLowerCase();
			if (value === 'html') {
				value = 'xml';
			}
			post.editorArray['blockCode'].setOption('mode', value);
			jQuery('#blockCode').attr('data-lang', value);
		}
	},

	reply: (event) => {
		var commentForm = jQuery('#comment').html();
		var id = jQuery(event.currentTarget).parent().parent().attr('id');
		if (id) {
			var splitId = id.split('-');
			jQuery('#comment').html('');
			jQuery(event.currentTarget).after(commentForm);
			jQuery(jQuery(event.currentTarget).next()).find('input[name="parent"]').val(splitId[1]);
			jQuery('.close-reply').show();
			jQuery(event.currentTarget).hide();

			jQuery('.close-reply').on('click', (event) => {
				jQuery(event.currentTarget).hide();
				jQuery(event.currentTarget).parent().parent().parent().find('.reply').show();
				jQuery(event.currentTarget).parent().parent().remove();
				jQuery('#comment').html(commentForm);
			});
		}
	},

	codeEditor: (index, event) => {
		var self = post;
		var id = jQuery(event).attr('id');
		var readonly = jQuery(event).hasClass('readonly');
		var mode = jQuery(event).attr('data-lang') || 'xml';
		if (mode === 'html') {
			mode = 'xml';
		}
		if (mode === 'c#' || mode === 'asp.net') {
			mode = 'clike';
		}
		var editor = CodeMirror.fromTextArea(document.querySelector('#' + id), {
			lineNumbers: true,
			lineWrapping: true,
			theme: 'codeblock',
			mode: mode,
			keymap: 'sublime',
			readOnly: readonly
		});

		self.editorArray[id] = editor;
		var lines = jQuery(event).attr('data-line');
		if (lines !== undefined) {
			lines = lines.split(',');
			var htmlLines = jQuery(event).next().find('.CodeMirror-code').children();
			for (var i = lines.length - 1; i >= 0; i--) {
				jQuery(htmlLines[lines[i] - 1]).addClass('CodeMirror-overlay');
			}
		}
	}
};

module.exports = post;
