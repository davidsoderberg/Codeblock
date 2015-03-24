/*
 * jQuery Validator v1.0.0
 * https://github.com/davidsoderberg/Validator.js
 *
 * Free to use and abuse under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */

(function($) {

	$.fn.validator = function(options) {

		var settings = $.extend({
			blur: true,
			keypress: true,
			submit: true,
			errorclass: ['alert', 'error']
		}, options);

		var errorMessage = {
			alpha: 'The :attribute may only contain letters.',
			alpha_numeric : 'The :attribute may only contain letters and numbers.',
			integer: 'The :attribute must be an integer.',
			number: 'The :attribute must be a number.',
			card :'The :attribute format is invalid.',
			cvv : 'The :attribute format is invalid.',
			email : "The :attribute must be a valid email address.",
			url: 'The :attribute format is invalid.',
			domain: 'The :attribute is not a valid URL.',
			datetime: 'The :attribute does not match the format YYYY-MM-DDTHH:MM:SS.',
			date: 'The :attribute does not match the format YYYY-MM-DD.',
			time : 'The :attribute must match the format HH:MM:SS.',
			dateISO: 'The :attribute format is invalid.',
			month_day_year : 'The :attribute does not match the format MM/DD/YYYY.',
			color: 'The :attribute muse be a HEX color with 3 or 6 characters and a # first.',
			ip: 'The :attribute must be a valid IP address.',
			password: 'The :attribute must contain at least one number and one uppecase letter and one special character and be between 8 and 100.'
		};

		// http://foundation.zurb.com/docs/components/abide.html
		var patterns = {
			alpha: /^[a-zåäöA-ZÅÄÖ]+$/,
			alpha_numeric : /^[a-zåäöA-ZÅÄÖ0-9]+$/,
			integer: /^[-+]?\d+$/,
			number: /^[-+]?[1-9]\d*$/,

			// amex, visa, diners
			card : /^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/,
			cvv : /^([0-9]){3,4}$/,

			// http://www.whatwg.org/specs/web-apps/current-work/multipage/states-of-the-type-attribute.html#valid-e-mail-address
			email : /^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/,
			// /[-0-9a-zA-Z.+_]+@[-0-9a-zA-Z.+_]+\.[a-zA-Z]{2,4}/

			url: /(https?|ftp|file|ssh):\/\/(((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-zA-Z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-zA-Z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?/,
			// abc.de
			domain: /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/,

			datetime: /([0-2][0-9]{3})\-([0-1][0-9])\-([0-3][0-9])T([0-5][0-9])\:([0-5][0-9])\:([0-5][0-9])(Z|([\-\+]([0-1][0-9])\:00))/,
			// YYYY-MM-DD
			date: /(?:19|20)[0-9]{2}-(?:(?:0[1-9]|1[0-2])-(?:0[1-9]|1[0-9]|2[0-9])|(?:(?!02)(?:0[1-9]|1[0-2])-(?:30))|(?:(?:0[13578]|1[02])-31))/,
			// HH:MM:SS
			time : /(0[0-9]|1[0-9]|2[0-3])(:[0-5][0-9]){2}/,
			dateISO: /\d{4}[\/\-]\d{1,2}[\/\-]\d{1,2}/,
			// MM/DD/YYYY
			month_day_year : /(0[1-9]|1[012])[- \/.](0[1-9]|[12][0-9]|3[01])[- \/.](19|20)\d\d/,

			// #FFF or #FFFFFF
			color: /^#?([a-fA-F0-9]{6}|[a-fA-F0-9]{3})$/,

			// http://stackoverflow.com/questions/10006459/regular-expression-for-ip-address-validation
			ip: /^(?:(?:2[0-4]\d|25[0-5]|1\d{2}|[1-9]?\d)\.){3}(?:2[0-4]\d|25[0-5]|1\d{2}|[1-9]?\d)(?:\:(?:\d|[1-9]\d{1,3}|[1-5]\d{4}|6[0-4]\d{3}|65[0-4]\d{2}|655[0-2]\d|6553[0-5]))?$/,

			// http://www.mkyong.com/regular-expressions/how-to-validate-password-with-regular-expression/
			password: /((?=.*\d)(?=.*[a-zåäö])(?=.*[A-ZÅÄÖ])(?=.*[@#$%]).{8,100})/
		};

		if(settings.blur){
			$('input, select, textarea').blur(function(){
				validate($(this));
			});
		}
		if(settings.keypress){
			$('input, select, textarea').keyup(function(event){
				if(event.keyCode != 9){
					validate($(this));
				}
			});
		}

		if(settings.submit){
			$(this).submit(function(event){
				$(this).find('input, textarea, select').each(function(){
					validate($(this));
				});
				if($(this).find('[data-id]:visible').length > 0){
					event.preventDefault();
					$(this).find('[data-id]:visible').first().prev().focus();
					$(this).trigger( "invalid" );
				}else{
					$(this).trigger('valid');
					return true;
				}
			});
		}

		function validate(self){
			var element = self.prop('tagName').toLowerCase();
			if(element === 'input' || element === 'select' || element === 'textarea'){
				var errorElement = $('[data-id='+self.attr("id")+']');

				if(errorElement.length == 0){
					self.after('<div data-id="'+self.attr("id")+'" class="'+settings.errorclass.join(' ')+'"></div>');
				}

				var label = self.prev().text().slice(0,-1).toLowerCase();
				var value = self.val();

				if(self.attr('data-validator') != undefined){
					var ruleArray = self.attr('data-validator').split('|');
					for (var i = 0; i < ruleArray.length; i++) {
						var validation = ruleArray[i];
						ruleArray[i] = ruleArray[i].split(':');
						var parameters = ruleArray[i].slice(1).join(':');
						var rule = ruleArray[i][0];
						switch(rule){
							case 'required':
								if(value === '' || value === null){
									errorElement.text('The '+label+' field is required.');
									if(rule === 'required'){
										i = ruleArray.length;
									}
								}else{
									errorElement.text('');
								}
								break;
							case 'min':
								if(value.length < parameters){
									errorElement.text('The '+label+' must be at least '+parameters+' characters.');
								}else{
									errorElement.text('');
								}
								break;
							case 'max':
								if(value.length > parameters){
									errorElement.text('The '+label+' must be at least '+parameters+' characters.');
								}else{
									errorElement.text('');
								}
								break;
							case 'between':
								parameters = parameters.split(',');
								if(value.length < parameters[0] || value.length > parameters[1]){
									errorElement.text('The '+label+' must be between '+parameters[0]+' and '+parameters[1]+' characters.');
								}else{
									errorElement.text('');
								}
								break;
							case 'digits_between':
								parameters = parameters.split(',');
								if(value < parameters[0] || value > parameters[1]){
									errorElement.text('The '+label+' must be between '+parameters[0]+' and '+parameters[1]+' digits.');
								}else{
									errorElement.text('');
								}
								break;
							case 'date_between':
								parameters = parameters.split(',');
								if(new Date(value) < new Date(parameters[0]) || new Date(value) > new Date(parameters[1])){
									errorElement.text('The '+label+' must be between the dates '+parameters[0]+' and '+parameters[1]+'.');
								}else{
									errorElement.text('');
								}
								break;
							case 'pattern':
								if(patterns[parameters] != undefined){
									if(!value.match(patterns[parameters])){
										errorElement.text(errorMessage[parameters].replace(":attribute", label));
									}else{
										errorElement.text('');
									}
								}else{
									if(!value.match(validation.substring(8))){
										errorElement.text('The '+label+' format is invalid.');
									}else{
										errorElement.text('');
									}
								}
								break;
							case 'different':
								if(value === $('[name='+parameters+']').val()){
									errorElement.text('The '+label+' and '+parameters+' must be different.');
								}else{
									errorElement.text('');
								}
								break;
							case 'confirmed':
								if(value != $('[name='+parameters+']').val()){
									errorElement.text('The '+label+' confirmation does not match.');
								}else{
									errorElement.text('');
								}
								break;
							case 'before':
								if(new Date(value) > new Date(parameters)){
									errorElement.text('The '+label+' must be a date before '+parameters+'.');
								}else{
									errorElement.text('');
								}
								break;
							case 'after':
								if(new Date(value) < new Date(parameters)){
									errorElement.text('The '+label+' must be a date after '+parameters+'.');
								}else{
									errorElement.text('');
								}
								break;
						}
						if(errorElement.text() == ''){
							self.removeClass('has-error');
						}else{
							self.addClass('has-error');
						}
					};
					if(errorElement.text() == ''){
						errorElement.remove();
					}
				}
			}
		}

	}

}(jQuery));