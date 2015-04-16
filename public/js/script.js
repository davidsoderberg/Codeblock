jQuery(document).ready(function($){

	page.init();
	post.init();
	async.init(appConfig);

	if($.fn.chosen) {
		$(".chosen-select").chosen();
	}

	if($.fn.validator) {
		$('form').validator({keypress:false});
	}

	if($.fn.tabs){
		$('#browseTabs').tabs();
	}

	if($.fn.accordion){
		$('#accordion').accordion();
	}
});