var	async = {
	request: 0,
	config: {},
	init: function (config) {
		this.config = config;
		if(jQuery('.mentionarea').length > 0) {
			jQuery.get("/api/users", this.getUsers);
		}
		if(localStorage.getItem('token') == null){
			this.getJWT();
		}else{
			this.websocket();
		}
		jQuery('.close-toast').click(this.closeToast);
	},

	getUsers: function (data) {
		jQuery('.mentionarea').mention({
			delimiter: '@',
			queryBy: ['username'],
			users: data.data
		});
	},

	closeToast: function(event){
		event.preventDefault();
		jQuery(this).parent().addClass('lightSpeedOut');
		setTimeout(function(){
			jQuery(this).parent().remove();
		}, 3000);
	},

	getJWT: function() {
		var self = async;
		jQuery.get("/api/auth", function (data) {
			var date = new Date;
			date.setHours(date.getHours() + 2);
			date = date.getTime();

			if (data.token) {
				localStorage.setItem('token', JSON.stringify({date: date, token: data.token}));
				self.websocket();
			}
		});
	},

	createToast: function(text){
		var toast = jQuery('<div></div>').addClass('toast animated lightSpeedIn');
		toast.html(text);

		jQuery('#toast-container').prepend(toast);
		setTimeout(function() {
			toast.addClass('lightSpeedOut');
			setTimeout(function(){
				toast.remove();
			}, 3000);
		}, 5000);
	},

	websocket: function(){
		var self = async;
		var oldHtml = '';
		var storage = JSON.parse(localStorage.getItem('token'));
		if(storage.date > Date.now()) {
			self.request = 0;
			var conn = new WebSocket('ws://'+self.config.SOCKET_ADRESS+':'+self.config.SOCKET_PORT);
			conn.onopen = function (e) {
				conn.send(JSON.stringify({'channel': 'auth', 'token': storage.token}));
			};

			conn.onmessage = function (e) {
				data = JSON.parse(e.data);
				switch (data.channel){
					case 'toast':
						self.createToast(data.message);
						break;
					case 'Topic':
						if(data.message != oldHtml) {
							jQuery('.forum').append(data.message);
						}
						oldHtml = data.message;
						break;
				}
			};
		}else{
			self.request++;
			if(self.request < 4) {
				self.getJWT();
			}
		}
	}
}