require('../../../bower_components/mention/bootstrap-typeahead.js');
require('../../../bower_components/mention/mention.js');
require('../../../bower_components/pusher-websocket-iso/dist/web/pusher');
var toast = require('./toast');

var async = {
	request: 0,
	pusher: {},
	config: {},
	init: (config) => {
		async.config = config;
		toast.init();
		async.pusher();
	},

	getJWT: () => {
		jQuery.get('/api/v1/auth', (data) => {
			var date = new Date();
			date.setHours(date.getHours() + 2);
			date = date.getTime();

			if (data.token) {
				localStorage.setItem('token', JSON.stringify({date: date, token: data.token}));
				async.pusher();
			}
		});
	},

	pusher: () => {
		var storage = JSON.parse(localStorage.getItem('token'));
		if (storage !== null && storage.date > Date.now()) {
			async.pusher = new Pusher(async.config.PUSHER_KEY, {
				auth: {
					headers: {
						'X-Auth-Token': storage.token
					}
				}
			});
			async.pusher.connection.bind('error', function (err) {
				if (err.data.code === 4004) {
					toast.create('Please reload page to connect to the real time message service.');
					return;
				}
			});

			var channel = async.pusher.subscribe('presence-test_token');

			channel.bind('pusher:subscription_error', () => {
				localStorage.setItem('token', null);
				async.getJWT();
			});

			channel.bind('pusher:subscription_succeeded', () => {
				if (async.config.AUTH_ID) {
					async.pusher_user();
					if (async.config.POST) {
						async.pusher_comment();
					}
					if (async.config.TOPIC) {
						async.pusher_topic();
					}
				}
				async.pusher.unsubscribe('presence-test_token');
			});
		} else {
			async.request++;
			if (async.request < 3) {
				async.getJWT();
			}
		}
	},

	pusher_user: () => {
		var channel = async.pusher.subscribe('presence-user_' + async.config.AUTH_ID);
		channel.bind('toast', (data) => {
			toast.create(data.message);
		});
	},

	pusher_comment: () => {
		var oldHtml = '';
		var channel = async.pusher.subscribe('presence-post_' + async.config.POST);
		channel.bind('new_comment', (data) => {
			if (data.message !== oldHtml) {
				if (data.user_id !== async.config.AUTH_ID) {
					if (data.parent !== 0) {
						jQuery('#comment').before(data.message);
					} else {
						jQuery('#comment-' + data.parent + ' .reply').after(data.message);
					}
					toast.create('New comment have been added to current codeblock.');
				}
			}
			oldHtml = data.message;
		});
	},

	pusher_topic: () => {
		var oldHtml = '';
		var channel = async.pusher.subscribe('presence-topic_' + async.config.TOPIC);
		channel.bind('new_reply', (data) => {
			if (data.message !== oldHtml) {
				if (data.user_id !== async.config.AUTH_ID) {
					jQuery('.forum').append(data.message);
					toast.create('New reply have been added to current topic.');
				}
			}
			oldHtml = data.message;
		});
	}
};

module.exports = async;
