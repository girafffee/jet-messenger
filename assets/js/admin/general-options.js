(function () {

	"use strict";
	let duration = 2500;

    new Vue({
		el: '#jet-msg-general-options-page',
		template: '#jet-msg-general-options',
		data: {
			bots: window.JetMSGConfig.bots_list,
			isSet: window.JetMSGConfig.is_set         
		},
		computed: {
			isLoading: function() {
				return (this.isSet && this.bots.length === 0); 
			}
		},
		mounted: function () {
			//this.getBots();			
		},
        methods: {
            updateBot: function( index ) {
				var self = this;
				var bot = self.bots[ index ];

				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_msg_save_token',
						data: bot
					},
				}).done( function( response ) {

					if ( response.success ) {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'success',
							duration: duration,
						} );
					} else {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'error',
							duration: duration,
						} );
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: duration,
					} );

				} );
			},
			installDB: function () {
				var self = this;
				
				jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_msg_db_install',
					},
				}).done( function( response ) {

					if ( response.success ) {
						self.$CXNotice.add( {
							message: response.data.message,
							type: 'success',
							duration: duration,
						} );

						self.isSet = true;
						self.getBots();
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: duration,
					} );

				} );
			},
			getBots: function() {
				var self = this;

				jQuery.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: {
						action: 'jet_msg_get_all_bots'
					},
				}).done( function( response ) {
					if ( response.success && response.data ) {
						self.bots	= response.data.botsInfo;	
					}
					else {
						self.isSet = false;
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {
					
					self.isSet = false;

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: duration,
					} );

				} );
			}
		}
    });

})();