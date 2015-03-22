(function($) {
	$.fn.tabs = function() {
		var self = this;
		$(self.selector+' ul:first-of-type li a').click(function(event){
			event.preventDefault();
			$(self.selector+' ul li').removeClass('open');
			$(this).parent().addClass('open');
			$($(self.selector+" ul:last-of-type li" ).get($(this).parent().index())).addClass('open');
		});
	}
}(jQuery));