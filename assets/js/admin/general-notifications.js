(function () {

    "use strict";
    
    let duration = 2500;
    
    new Vue({
		el: '#jet-msg-general-notifications-page',
        template: '#jet-msg-general-notifications',
        data: {
            botsList: window.JetMSGConfig.active_bots_list,
            actionsList: window.JetMSGConfig.actions_list,
            actionsOn: window.JetMSGConfig.actions_on,  
            notifications: window.JetMSGConfig.notifications_list,
            notificationDefault: {
                action: '',
                do_action_on: '',
                action_value: '',
                bot_id: '',
                message: '# hello',
                collapsed: false,
            }
        },
        
        computed: {
            //'id','taxonomy','post_type','relation_parent','relation_child','custom'
            
            issetBots: function () {
                return window.JetMSGConfig.isset_bots;
            },
            
        },

        methods: {
            isValuesReady: function ( index ) {
                if ( ! this.notifications[ index ] ) return;

                return ( ! Boolean( this.notifications[ index ].bot_id 
                    && this.notifications[ index ].action 
                    && this.notifications[ index ].do_action_on 
                    && this.notifications[ index ].action_value ) );
            },
            compiledMarkdown: function( index ) {
                return marked( this.notifications[ index ].message, { sanitize: true } );
            },

            updateEditor( e, index ) {
                this.notifications[ index ].message = e.target.value || '';
            },

            isCollapsed: function( object ) {
                if ( undefined === object.collapsed || true === object.collapsed ) {
                    return true;
                } else {
                    return false;
                }
            },

            getActionsOn( index ) {
                var self = this;
                if ( ! this.notifications[ index ] ) return;

                let notif = self.notifications[ index ];

                if ( notif.action === '' ) {
                    return self.actionsOn;
                }
                let relations = self.actionsList[ notif.action ].relation;
                let triggers = [];
                
                for ( let name in self.actionsOn ) {
                    if ( relations.includes( name ) ) {
                        triggers.push( self.actionsOn[ name ] );
                    }
                }

                if ( ! triggers.find( trig => trig.value === notif.do_action_on ) ) {
                    notif.do_action_on = '';
                }

                return triggers;
            },

            getNotificationTitle( index ) {
                let notif = this.notifications[ index ];

                if ( ! notif.bot_id ) {
                    return '';
                }

                let title = '#' + Number( index + 1 ) + '&nbsp;';
                title += this.botsList[ notif.bot_id ].label;
                return title;
            },

            /**
             * Todo: поменять индексы в do_action_on
             */
            getNotificationSubTitle( index ) {
                if ( ! this.notifications[ index ].action ) {
                    return '';
                }
                let notif = this.notifications[ index ];

                let subtitle = [
                    this.actionsList[ notif.action ].label,
                    this.actionsOn[ notif.do_action_on ].label,
                    notif.action_value
                ];

                return subtitle.join(' -> ');
            },

            addNewNotification: function() {  
                let emptyNotification = Object.assign( {}, this.notificationDefault );
                this.notifications.push( emptyNotification );
            },

            cloneNotification( index ) {
                let newNotification = Object.assign( {}, this.notifications[ index ] );
                
                delete newNotification.id;
                this.notifications.push( newNotification );
            },

            deleteNotification( index ) {
                let notif = this.notifications[ index ];
                let self = this;

                if ( notif.id ) {
                    
                    jQuery.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            action: 'jet_msg_delete_notification',
                            data: { id: notif.id },
                            mode: 'delete_mode'
                        },
                    }).done( function( response ) {
    
                        if ( response.success ) {
                            self.$CXNotice.add( {
                                message: response.data.message,
                                type: 'success',
                                duration: duration,
                            } );

                            self.notifications.splice( index, 1 );
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
                }
                else {
                    self.notifications.splice( index, 1 );
                }
                
            },

            attachInsertedId( id, index ) {
                id = Number( id );

                if ( typeof id === 'number' ) {
                    this.notifications[ index ].id = id;
                    return;
                }
                location.reload();
            },

            saveNotification( index ) {
                var self = this;
        
                let mode = self.notifications[ index ].id ? 'update_mode' : 'insert_mode';

                jQuery.ajax({
					url: ajaxurl,
					type: 'POST',
					dataType: 'json',
					data: {
						action: 'jet_msg_save_action',
                        data: self.notifications[ index ],
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
            returnToGeneral() {
                document.location.href = window.JetMSGConfig.return_to_options_url;
            }


        }
    });

})();