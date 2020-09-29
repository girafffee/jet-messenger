(function () {

	"use strict";
	
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
							duration: 7000,
						} );
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 7000,
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
							duration: 7000,
						} );

						self.isSet = true;
						self.getBots();
					}

				} ).fail( function( jqXHR, textStatus, errorThrown ) {

					self.$CXNotice.add( {
						message: errorThrown,
						type: 'error',
						duration: 7000,
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
						duration: 7000,
					} );

				} );
			}
		}
    });

})();