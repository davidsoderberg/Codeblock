var	post = {
	editorArray: [],
	init: function () {
		this.bindEvents();
	},

	bindEvents: function () {
		jQuery('.code-editor').each(this.codeEditor);
		jQuery('.reply').click(this.reply);
		jQuery('.confirm').click(this.confirm);
		jQuery('#blockCategory').change(this.categoryChange);
		if(typeof this.editorArray['blockCode'] != 'undefined'){
			this.editorArray['blockCode'].on("blur",this.blur);
		}
	},

	confirm: function(event) {
		event.preventDefault();
		jQuery('.confirmModal .green-background').attr('href', jQuery(this).attr('href'));
		jQuery('.confirmModal').toggleClass('open');
		jQuery('.confirmModal .red-background').click(function(event){
			event.preventDefault();
			jQuery('.confirmModal').toggleClass('open');
		});
	},

	blur: function(){
		var self = post;
		jQuery('#blockCode').val(self.editorArray['blockCode'].getValue());
		jQuery('#blockCode').trigger('blur');
	},

	categoryChange: function(){
		var self = post;
		var value = jQuery(this).find("option:selected").text().toLowerCase();
		if(value === 'html'){
			value = 'xml';
		}
		self.editorArray['blockCode'].setOption('mode', value);
		jQuery('#blockCode').attr('data-lang', value);
	},

	reply: function() {
		var commentForm = jQuery('#comment').html();
		var id = jQuery(this).parent().parent().attr('id');
		if(id) {
			splitId = id.split('-');
			jQuery('#comment').html('');
			jQuery(this).after(commentForm);
			jQuery(jQuery(this).next()).find('input[name="parent"]').val(splitId[1]);
			jQuery('.close-reply').show();
			jQuery(this).hide();

			jQuery('.close-reply').on('click', function (event) {
				jQuery(this).hide();
				jQuery(this).parent().parent().parent().find('.reply').show();
				jQuery(this).parent().parent().remove();
				jQuery('#comment').html(commentForm);
			});
		}
	},

	codeEditor: function(){
		var self = post;
		var id = jQuery(this).attr('id');
		var readonly = jQuery(this).hasClass('readonly');
		var mode = jQuery(this).attr('data-lang') || 'xml';
		if(mode == 'html'){
			mode = 'xml'
		}
		if(mode == 'c#' || mode == 'asp.net'){
			mode = 'clike';
		}
		var editor = CodeMirror.fromTextArea(document.querySelector("#"+id), {
			lineNumbers: true,
			lineWrapping: true,
			theme: 'codeblock',
			mode:  mode,
			keymap: 'sublime',
			readOnly: readonly
		});

		self.editorArray[id] = editor;

		var lines = jQuery(this).attr('data-line');
		if(lines != undefined ){
			lines = lines.split(',');
			var htmlLines = jQuery(this).next().find('.CodeMirror-code').children();
			for (var i = lines.length - 1; i >= 0; i--) {
				jQuery(htmlLines[lines[i]-1]).addClass('CodeMirror-overlay');
			};
		}
	}
}