(function () {

    "use strict";
    
    Vue.config.devtools = true;
    let duration = 2500;

    new Vue({
		el: '#jet-msg-general-notifications-page',
        template: '#jet-msg-general-notifications',
        data: {
            notifData: {
                botsList:       window.JetMSGConfig.active_bots_list,
                actionsList:    window.JetMSGConfig.actions_list,
                actionsOn:      window.JetMSGConfig.actions_on,
                notifications:  window.JetMSGConfig.notifications_list,
                conditions:     window.JetMSGConfig.conditions
            },
        },
        methods: {
            returnToGeneral() {
                document.location.href = window.JetMSGConfig.return_to_options_url;
            },

            deleteNotification() {
                return 'jet_msg_delete_notification';
            },

            saveNotification( index ) {
                var self = this;

                let mode = self.notifData.notifications[ index ].id ? 'update_mode' : 'insert_mode';

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'jet_msg_save_action',
                        data: self.notifData.notifications[ index ],
                        mode: mode
                    },
                }).done( function( response ) {

                    if ( response.success ) {
                        self.$CXNotice.add( {
                            message: response.data.message,
                            type: 'success',
                            duration: duration,
                        } );

                        if ( mode === 'insert_mode' ){
                            self.attachInsertedId( response.data.id, index );
                        }
                    }

                } ).fail( function( jqXHR, textStatus, errorThrown ) {

                    self.$CXNotice.add( {
                        message: errorThrown,
                        type: 'error',
                        duration: duration,
                    } );

                } );
            },

            attachInsertedId( id, index ) {
                id = Number(id);

                if (typeof id === 'number' && id > 0) {
                    this.notifData.notifications[index].id = id;
                    return;
                }
                location.reload();
            }

		}
    });

})();