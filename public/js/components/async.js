require('../../../bower_components/mention/bootstrap-typeahead.js');
require('../../../bower_components/mention/mention.js');

var async = {
	request: 0,
	config: {},
	init: (config) => {
		async.config = config;
		if (jQuery('.mentionarea').length > 0) {
			jQuery.get('/api/v1/users', async.getUsers);
		}
		if (localStorage.getItem('token') == null) {
			async.getJWT();
		} else {
			//async.websocket();
		}
		jQuery('.close-toast').click(async.closeToast);
	},

	getUsers: (data) => {
		jQuery('.mentionarea').mention({
			delimiter: '@',
			queryBy: ['username'],
			users: data.data
		});
	},

	closeToast: (event) => {
		event.preventDefault();
		jQuery(event.currentTarget).parent().addClass('lightSpeedOut');
		setTimeout(() => {
			jQuery(event.currentTarget).parent().remove();
		}, 3000);
	},

	getJWT: () => {
		jQuery.get('/api/v1/auth', (data) => {
			var date = new Date();
			date.setHours(date.getHours() + 2);
			date = date.getTime();

			if (data.token) {
				localStorage.setItem('token', JSON.stringify({date: date, token: data.token}));
				//async.websocket();
			}
		});
	},

	createToast: (text) => {
		var toast = jQuery('<div></div>').addClass('toast animated lightSpeedIn');
		toast.html(text);

		jQuery('#toast-container').prepend(toast);
		setTimeout(() => {
			toast.addClass('lightSpeedOut');
			setTimeout(() => {
				toast.remove();
			}, 3000);
		}, 5000);
	},

	websocket: () => {
		var self = async;
		var oldHtml = '';
		var storage = JSON.parse(localStorage.getItem('token'));
		if (storage.date > Date.now()) {
			self.request = 0;
			var conn = new WebSocket('ws://' + self.config.SOCKET_ADRESS + ':' + self.config.SOCKET_PORT);
			conn.onopen = () => {
				conn.send(JSON.stringify({'channel': 'auth', 'token': storage.token}));
			};

			conn.onmessage = (e) => {
				var data = JSON.parse(e.data);
				switch (data.channel) {
					case 'toast':
						self.createToast(data.message);
						break;
					case 'Topic':
						if (data.message !== oldHtml) {
							jQuery('.forum').append(data.message);
						}
						oldHtml = data.message;
						break;
				}
			};
		} else {
			self.request++;
			if (self.request < 4) {
				self.getJWT();
			}
		}
	}
};

module.exports = async;
