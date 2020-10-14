(function () {

    "use strict";

    Vue.config.devtools = true;
    let duration = 2500;

    new Vue({
		el: '#jet-msg-private-notifications-page',
        template: '#jet-msg-private-notifications',
        data: {
            notifData: {
                botsList: window.JetMSGConfig.active_bots_list,
                actionsList: window.JetMSGConfig.actions_list,
                actionsOn: window.JetMSGConfig.actions_on,
                notifications: window.JetMSGConfig.notifications_list,
            },
            chatData : window.JetMSGConfig.chat_data,
            showPopup: false,
            showSyncButton: false,
            showEnabledIcon: false,
            issetBots: window.JetMSGConfig.isset_bots,
            emptyNotification: {
                action: '',
                do_action_on: '',
                action_value: '',
                bot_id: '',
                message: '# hello',
                collapsed: false,
            },
            emptyChat: {
                bot_slug: "telegram",
                chat_id: '',
                chat_type: '',
                status: '',
                sync_code: ''
            }
        },
        mounted: function () {
		    this.emptyNotification.jet_msg_chat_id = this.chatData.id
        },

        methods: {

            returnToGeneral() {
                document.location.href = window.JetMSGConfig.return_to_options_url;
            },

            getBotLabel() {
                let botData = this.notifData.botsList[ '1' ];
                return botData.label + '@' + botData.name;
            },

            getBotUrl() {
                let botData = this.notifData.botsList[ '1' ];
                return 'https://t.me/' + botData.name;
            },

            isSynced() {
                var self = this;

                if ( ! self.chatData.id || ! self.showPopup ) return;

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'jet_msg_is_synced_chat',
                        id: self.chatData.id,
                        bot_slug: self.chatData.bot_slug
                    },
                }).done( function( response ) {

                    if ( response.success ) {
                        self.showPopup = false;
                        self.showSyncButton = false;
                        self.showEnabledIcon = true;

                        self.$CXNotice.add( {
                            message: response.data.message,
                            type: 'success',
                            duration: duration,
                        } );
                    }
                    else {
                        self.onChangeSyncCode();
                    }

                } ).fail( function( jqXHR, textStatus, errorThrown ) {
                    self.showPopup = false;

                    self.$CXNotice.add( {
                        message: errorThrown,
                        type: 'error',
                        duration: duration,
                    } );
                } );
            },

            syncChat() {
                var self = this;

                if ( ! self.chatData.id ) return;

                if ( self.chatData.sync_code ) {
                    self.showPopup = true;

                    self.onChangeSyncCode();
                    return;
                }

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'jet_msg_sync_chat',
                        bot_slug: self.chatData.bot_slug,
                        id: self.chatData.id
                    },
                }).done( function( response ) {

                    if ( response.success ) {
                        self.chatData.sync_code = response.data.sync_code
                        self.showPopup = true;

                        self.onChangeSyncCode();
                    }

                } ).fail( function( jqXHR, textStatus, errorThrown ) {
                    self.$CXNotice.add( {
                        message: errorThrown,
                        type: 'error',
                        duration: duration,
                    } );
                } );

            },
            onChangeSyncCode() {
                var self = this;
                if ( ! self.chatData.sync_code ) return;

                self.isSynced();
            },
            saveChat() {
                var self = this;

                let mode = self.chatData.id ? 'update_mode' : 'insert_mode';

                Object.assign( self.chatData, self.emptyChat );

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'jet_msg_save_chat',
                        data: self.chatData,
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
                            self.attachInsertedId( response.data.id, self.chatData );
                            self.emptyNotification.jet_msg_chat_id = response.data.id;
                        }
                        self.showSyncButton = true;
                        self.showEnabledIcon = false;
                    }
                    else {
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

            saveNotification( index ) {
                var self = this;

                let mode = self.notifData.notifications[ index ].id ? 'update_mode' : 'insert_mode';

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'jet_msg_save_private_notification',
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
                            self.attachInsertedId( response.data.id, self.notifData.notifications[ index ] );
                        }
                    }
                    else {
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

            deleteNotification() {
                return 'jet_msg_delete_private_notification';
            },

            attachInsertedId( id, object ) {
                id = Number(id);

                if (typeof id === 'number' && id > 0) {
                    object.id = id;
                    return;
                }
                location.reload();
            }
        }
    });

})();