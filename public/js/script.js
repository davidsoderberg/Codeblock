jQuery(document).ready(function($){

	var editorArray = [];

	if($.fn.chosen) {
		$(".chosen-select").chosen();
	}
	$('#header .menu li a.search').click(function(event){
		event.preventDefault();
		$(this).remove();
		$('#header .menu li form').animate({width:'toggle'},1000);
		$('#header .menu li form input').focus();
	});

	$('li.dropdown > a').click(function(event){
		if($(document).width() < 801){
			event.preventDefault();
			$(this).toggleClass('hideUl');
		}
	});

	$('.toogleModal').click(function (event) {
		event.preventDefault();
		$('.modal').toggleClass('open');
	});

	$('#menubutton').click(function(event){
		$(this).toggleClass('hideUl');
	});

	$(".close-alert").click(function(){
		$(this).parent().fadeOut('slow',function(){
			$(this).remove();
		});
	});

	if($.fn.validator) {
		$('form').validator({keypress:false});
	}

	if(typeof CodeMirror != 'undefined'){
		$('.code-editor').each(function(){
			var id = $(this).attr('id');
			var readonly = $(this).hasClass('readonly');
			var mode = $(this).attr('data-lang') || 'xml';
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

			editorArray[id] = editor;

			var lines = $(this).attr('data-line');
			if(lines != undefined ){
				lines = lines.split(',');
				var htmlLines = $(this).next().find('.CodeMirror-code').children();
				for (var i = lines.length - 1; i >= 0; i--) {
					$(htmlLines[lines[i]-1]).addClass('CodeMirror-overlay');
				};
			}
		});
	}

	var commentForm = $('#comment').html();
	$('.reply').click(function(event) {
		var id = $(this).parent().parent().attr('id');
		splitId = id.split('-');
		$('#comment').html('');
		$(this).after(commentForm);
		$($(this).next()).find('input[name="parent"]').val(splitId[1]);
		$('.close-reply').show();
		$(this).hide();

		$('.close-reply').on('click', function(event){
			$(this).hide();
			$(this).parent().parent().parent().find('.reply').show();
			$(this).parent().parent().remove();
			$('#comment').html(commentForm);
		});
	});

	$('#blockCategory').change(function(){
		var value = $(this).find("option:selected").text().toLowerCase();
		if(value === 'html'){
			value = 'xml';
		}
		editorArray['blockCode'].setOption('mode', value);
		$('#blockCode').attr('data-lang', value);
	});

	if(typeof editorArray['blockCode'] != 'undefined'){
		editorArray['blockCode'].on("blur", function(){
			$('#blockCode').val(editorArray['blockCode'].getValue());
			$('#blockCode').trigger('blur');
		});
	}

	$('.menu-button a:first-of-type, .close-button').on('click',function(event){
		event.preventDefault();
		$('body').toggleClass('show-menu');
	});

	var mentionarea = $('.mentionarea');
	if(mentionarea.length > 0) {
		$.get("/api/users", function (data) {
			mentionarea.mention({
				delimiter: '@',
				queryBy: ['username'],
				users: data.data
			});
		});
	}

	function createToast(text){
		var toast = $('<div></div>').addClass('toast animated lightSpeedIn');
		toast.text(text);
		$('#toast-container').prepend(toast);
		setTimeout(function() {
			toast.addClass('lightSpeedOut');
			setTimeout(function(){
				toast.remove();
			}, 3000);
		}, 5000);
	}

	if($.fn.tabs){
		$('#browseTabs').tabs();
	}

	if($.fn.accordion){
		$('#accordion').accordion();
	}
});