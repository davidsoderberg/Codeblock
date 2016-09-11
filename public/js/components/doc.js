var doc = {
	init: () => {
		if (jQuery('#documentation').length) {
			var hash = window.location.hash;
			var storage = doc.storage();

			jQuery('#documentation #filter').keyup(doc.filter);
			jQuery('#documentation a').click(doc.link);
			if (hash || storage && storage.getItem('current_class')) {
				if(!hash){
					hash = storage.getItem('current_class');
				}
				jQuery("#documentation .wrapper:first-of-type ul li a[href='" + hash + "']").trigger('click');
			}

		}

		if (jQuery('#api_documentation').length) {
			doc.api();
		}
	},

	syntaxHighlight: (json) => {
		if (typeof json != 'string') {
			json = JSON.stringify(json, undefined, 2);
		}
		json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
			var cls = 'number';
			if (/^"/.test(match)) {
				if (/:$/.test(match)) {
					cls = 'key';
				} else {
					cls = 'string';
				}
			} else if (/true|false/.test(match)) {
				cls = 'boolean';
			} else if (/null/.test(match)) {
				cls = 'null';
			}
			return '<span class="' + cls + '">' + match + '</span>';
		});
	},

	storage: () => {
		var uid = new Date;
		var storage;
		var result;
		try {
			(storage = window.localStorage).setItem(uid, uid);
			result = storage.getItem(uid) == uid;
			storage.removeItem(uid);
			return result && storage;
		} catch (exception) {
		}
	},

	api: () => {

		var storage = doc.storage();

		jQuery.fn.serializeObject = function () {
			var o = {};
			var a = this.serializeArray();
			jQuery.each(a, function () {
				if (!this.value) {
					return;
				}
				if (o[this.name] !== undefined) {
					if (!o[this.name].push) {
						o[this.name] = [o[this.name]];
					}
					o[this.name].push(this.value || '');
				} else {
					o[this.name] = this.value || '';
				}
			});
			return o;
		};

		if (storage) {
			jQuery('#apikey_value').val(storage.getItem('apiKeyValue'));
		}

		jQuery('code[id^=response]').hide();

		jQuery.each(jQuery('pre[id^=sample_response],pre[id^=sample_post_body]'), function () {
			if (jQuery(this).html() == 'NA') {
				return;
			}
			var str = JSON.stringify(JSON.parse(jQuery(this).html().replace(/'/g, '"')), undefined, 2);
			jQuery(this).html(doc.syntaxHighlight(str));
		});

		jQuery('body').on('click', '#save_auth_data', function (e) {
			if (storage) {
				storage.setItem('apiKey', jQuery('#apikey_key').val());
				storage.setItem('apiKeyValue', jQuery('#apikey_value').val());
			} else {
				alert('Your browser does not support local storage');
			}
		});

		jQuery('body').on('click', '.send', function (e) {
			e.preventDefault();
			var form = jQuery(this).closest('form');
			//added /g to get all the matched params instead of only first
			var matchedParamsInRoute = jQuery(form).attr('action').match(/[^{]+(?=\})/g);
			var theId = jQuery(this).attr('rel');
			//keep a copy of action attribute in order to modify the copy
			//instead of the initial attribute
			var url = jQuery(form).attr('action');

			//get form serialized data in order to remove matchedParams
			var serializedData = jQuery(form).serializeObject();

			var index, key, value;

			if (matchedParamsInRoute) {
				for (index = 0; index < matchedParamsInRoute.length; ++index) {
					try {
						key = matchedParamsInRoute[index];
						value = serializedData[key];
						if (typeof value == "undefined") value = "";
						url = url.replace("{" + key + "}", value);
						delete serializedData[key];
					} catch (err) {
						console.log(err);
					}
				}
			}

			var st_headers = {};
			var apiKey = jQuery('#apikey_key').val();
			var apiKeyValue = jQuery('#apikey_value').val();

			if (apiKey.length > 0 && apiKeyValue.length > 0) {
				st_headers[apiKey] = apiKeyValue;
			}

			jQuery("#sandbox" + theId + " .headers input[type=text]").each(function () {
				val = jQuery(this).val();
				if (val.length > 0) {
					st_headers[jQuery(this).prop('name')] = val;
				}
			});

			jQuery.ajax({
				url: jQuery('#apiUrl').val() + url,
				data: serializedData,
				type: jQuery(form).attr('method') + '',
				dataType: 'json',
				headers: st_headers,
				success: function (data, textStatus, xhr) {
					if (typeof data === 'object') {
						var str = JSON.stringify(data, null, 2);
						jQuery('#response' + theId).html(doc.syntaxHighlight(str));
					} else {
						jQuery('#response' + theId).html(data);
					}
					jQuery('#response_headers' + theId).html('HTTP ' + xhr.status + ' ' + xhr.statusText + '<br/><br/>' + xhr.getAllResponseHeaders());
					jQuery('#response' + theId).show();
				},
				error: function (xhr, textStatus, error) {
					var str = JSON.stringify(jQuery.parseJSON(xhr.responseText), null, 2);
					jQuery('#response_headers' + theId).html('HTTP ' + xhr.status + ' ' + xhr.statusText + '<br/><br/>' + xhr.getAllResponseHeaders());
					jQuery('#response' + theId).html(doc.syntaxHighlight(str));
					jQuery('#response' + theId).show();

				}
			});
			return false;
		});
	},

	filter: (e) => {
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
	},

	link: (e) => {
		e.preventDefault();

		jQuery('#documentation .wrapper:first-of-type ul li').removeClass('active');
		var id = jQuery(e.currentTarget).attr('href');
		window.location.hash = id;
		jQuery("#documentation .wrapper:first-of-type ul li a[href='" + id + "']").parent().addClass('active');
		jQuery('#documentation .wrapper:last-of-type div').removeClass('show');
		jQuery(id).addClass('show');
		var storage = doc.storage();
		if (storage) {
			storage.setItem('current_class', id);
		}
	}
};

module.exports = doc;