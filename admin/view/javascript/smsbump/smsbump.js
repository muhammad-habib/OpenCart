(function($){
	$.smsbump = (function(){
		// Private variables
		var settings = {
			apikey: '',
			from: '',
			to: '',
			type: '',
			media: '',
			message: '',
			scheduled_date: '',
		}
		
		var eventHandlers = {
			complete: [],
			error: []
		}
		
		// Private methods
		var getApiUrl = function(method, APIKey) {
			return 'https://api.smsbump.com/'+method+'/'+APIKey+'.json';
		}
		
		var fireEvent = function(evt, evtData) {
			if (eventHandlers[evt]) {
				for(x=0; x < eventHandlers[evt].length; x++) {
					eventHandlers[evt][x].apply(document, [evtData]);
				}
			}
			
			if (settings[evt] && typeof settings[evt] == 'function') {
				settings[evt].apply(document, [evtData]);
			}
		}
		
		// Send logic
		var send_bulk = function() {
			for( x=0; x < settings.to.length; x++ ) {
				send_single(settings.apikey, settings.from, settings.to[x], settings.type, settings.message, settings.media, settings.scheduled_date);
			}
		}
		
		var send_single = function(APIKey, fromNumber, toNumber, type, msg, media, scheduled_date) {
			var xhr = $.ajax({
				url: getApiUrl('send', APIKey),
				data: {
					from: fromNumber,
					to: toNumber,
					type: type,
					message: msg,
					media: media,
					platform: 'opencart2',
					scheduled_date: scheduled_date
				},
				type: 'POST',
				dataType: 'json',
				success: function(resp, status, xhr) {
					resp['to'] = xhr.to;
					if (resp.status != 'error') {
						fireEvent('success', resp);
					} else {
						fireEvent('error', resp);
					}
				},
				error: function(xhr, resp) {
					fireEvent('error', resp);
				}
			});
			xhr.to = toNumber;
		}
		
		var send = function(options) {
			settings.success = settings.error = null;
			if (options) {
				settings = $.extend(settings, options);
			}
			
			if (settings.to) {
				if (typeof settings.to == 'object') send_bulk();
				else send_single(settings.apikey, settings.from, settings.to, settings.message);
			}
		}
		
		// Estimate logic
		var estimate_single = function(APIKey, toNumber, msg) {
			var xhr = $.ajax({
				url: getApiUrl('estimate', APIKey),
				data: {
					to: toNumber,
					message: msg
				},
				type: 'POST',
				dataType: 'json',
				success: function(resp) {
					if (resp.status != 'error') {
						fireEvent('success', {price: parseFloat(resp.data.price), currency: resp.data.currency});
					} else {
						fireEvent('error', resp);
					}
				},
				error: function(xhr, resp) {
					fireEvent('error', resp);
				}
			});
		}
		
		var estimate = function(options) {
			settings.success = settings.error = null;
			if (options) {
				settings = $.extend(settings, options);
			}
			
			if (settings.to) {
				estimate_single(settings.apikey, settings.to, settings.message);
			}
		}
		
		// Public methods
		function smsBumpObj( options ) {
			if (options) {
				send(options);
			}
		}
		
		smsBumpObj.send = send;
		
		smsBumpObj.estimate = estimate;
		
		smsBumpObj.addEventHandler = function(evt, callback) {
			if (eventHandlers[evt] && typeof eventHandlers[evt] == 'object') {
				eventHandlers[evt].push(callback);
				return true;
			}
			return false;
		}
		
		smsBumpObj.getLastMessage = function() {
			return settings;
		}
		
		return smsBumpObj;
	}())
}(jQuery))