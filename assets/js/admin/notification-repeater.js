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
            chatId: Number,
            deleteAjaxHook: String,
        },

        data: function () {
            return {
                defaultCondition: {
                    action_value: '',
                    action_type: '',
                    operator: '',
                },
                settings: this.settingsProp,
                notificationDefault: {
                    action: '',
                    conditions: [],
                    bot_id: '',
                    message: 'Hello world',
                    jet_msg_chat_id: this.chatId,
                    collapsed: false,
                },
                actionsList: this.prepareForSelectOptions( this.settingsProp.actionsList ),
                botsList: this.prepareForSelectOptions( this.settingsProp.botsList ),
                operators_for_form: window.JetMSGConfig.operators_for_form || [],
            };
        },
        mounted: function() {
            //
        },
        methods: {

		    prepareForSelectOptions( object ) {
		        const values = Object.values( object );

		        return values.map( ( { value, label } ) => {
		            return { value, label };
                } );
            },

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
                    return self.prepareForSelectOptions( self.settings.actionsOn );
                }

                let relations = self.settings.actionsList[ notif.action ].relation;
                let triggers = [];
                
                for ( let name in self.settings.actionsOn ) {
                    if ( self.settings.actionsOn.hasOwnProperty( name ) && relations.includes( name ) ) {
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

            getConditionTitle( index, cond_index ) {
                const condition = this.settings.notifications[ index ].conditions[ cond_index ];

                if ( ! condition.action_type ) {
                    return 'Condition';
                }

                return this.settings.actionsOn[ condition.action_type ].label;
            },

            getConditionSubTitle( index, cond_index ) {
                const condition = this.settings.notifications[ index ].conditions[ cond_index ];
                const subTitle = [];

                if ( condition.action_type && condition.action_type === 'form_value' && condition.field_name ) {
                    subTitle.push( condition.field_name );
                } else if( condition.action_type && condition.action_type === 'form_value' ) {
                    subTitle.push( '[field]' );
                }

                if ( condition.operator ) {
                    subTitle.push( condition.operator );
                } else {
                    subTitle.push( '[operator]' );
                }

                if ( condition.action_value ) {
                    subTitle.push( condition.action_value );
                } else {
                    subTitle.push( '[value]' );
                }

                return subTitle.join(' ');
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
                this.settings.notifications.push( { ...this.notificationDefault, conditions: [] } );
            },

            addNewCondition: function( index ) {
                this.settings.notifications[ index ].conditions.push( { ...this.defaultCondition } );
            },

            cloneNotification( index ) {
                let newNotification = Object.assign( {}, this.settings.notifications[ index ] );
                
                delete newNotification.id;
                this.settings.notifications.push( newNotification );
            },

            cloneCondition( index, cond_index ) {
                this.settings.notifications[ index ].conditions.push( {
                    ...this.settings.notifications[ index ].conditions[ cond_index ]
                } );
            },

            deleteCondition( index, cond_index ) {
                this.settings.notifications[ index ].conditions.splice( cond_index, 1 );
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