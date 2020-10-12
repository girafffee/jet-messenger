(function () {

    "use strict";
    
    let duration = 2500;

    Vue.component( 'jet-msg-notifications-repeater', {
		template: '#jet-msg-notifications-repeater',
		props: {
			settingsProp: {
				type: Object,
				default: {},
			},
            defaultNotifData: {
                type: Object,
                default: {
                    action: '',
                    do_action_on: '',
                    action_value: '',
                    bot_id: '',
                    message: '# hello',
                    collapsed: false,
                },
            },
            deleteAjaxHook: String,
        },

        data: function () {
            return {
                settings: this.settingsProp,
                notificationDefault: this.defaultNotifData,
            };
        },
        mounted: function() {
            //
        },
        methods: {
            isValuesReady: function ( index ) {
                let notif = this.settings.notifications[ index ];
                if ( ! notif ) return;

                return ( ! Boolean( notif.bot_id && notif.action && notif.do_action_on && notif.action_value ) );
            },

            compiledMarkdown: function( index ) {
                return marked( this.settings.notifications[ index ].message, { sanitize: true } );
            },

            updateEditor( e, index ) {
                this.settings.notifications[ index ].message = e.target.value || '';
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
                if ( ! this.settings.notifications[ index ] ) return;

                let notif = self.settings.notifications[ index ];

                if ( notif.action === '' ) {
                    return self.settings.actionsOn;
                }
                let relations = self.settings.actionsList[ notif.action ].relation;
                let triggers = [];
                
                for ( let name in self.settings.actionsOn ) {
                    if ( relations.includes( name ) ) {
                        triggers.push( self.settings.actionsOn[ name ] );
                    }
                }

                if ( ! triggers.find( trig => trig.value === notif.do_action_on ) ) {
                    notif.do_action_on = '';
                }

                return triggers;
            },

            getNotificationTitle( index ) {
                let notif = this.settings.notifications[ index ];

                if ( ! notif.bot_id ) {
                    return '';
                }

                let title = '#' + Number( index + 1 ) + '&nbsp;';
                title += this.settings.botsList[ notif.bot_id ].label;
                return title;
            },

            getNotificationSubTitle( index ) {
                if ( ! this.settings.notifications[ index ].action ) {
                    return '';
                }
                let notif = this.settings.notifications[ index ];

                let subtitle = [ this.settings.actionsList[ notif.action ].label ];
                
                if ( notif.do_action_on ) {
                    subtitle.push( this.settings.actionsOn[ notif.do_action_on ].label );
                }

                if ( notif.action_value ) {
                    subtitle.push( notif.action_value );
                }

                return subtitle.join(' -> ');
            },

            addNewNotification: function() {
                let emptyNotification = Object.assign( {}, this.notificationDefault );
                this.settings.notifications.push( emptyNotification );
            },

            cloneNotification( index ) {
                let newNotification = Object.assign( {}, this.settings.notifications[ index ] );
                
                delete newNotification.id;
                this.settings.notifications.push( newNotification );
            },

            deleteNotification( index ) {

                let notif = this.settings.notifications[ index ];
                let self = this;

                if ( ! notif.id ) {
                    self.settings.notifications.splice(index, 1);
                    return;
                }

                jQuery.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: this.deleteAjaxHook,
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

                        self.settings.notifications.splice( index, 1 );
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

            saveNotification( index ) {
                this.$emit('save-notification', index );
            },
            

        },
    } );

})();