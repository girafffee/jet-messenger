<div class="jet-msg-notifications-repeater">
	<div class="cx-vui-inner-panel">
		<cx-vui-repeater
		    :button-label="'<?php _e( 'New Notification', 'jet-messenger' ); ?>'"
			:button-style="'accent'"
			v-model="settings.notifications"
			@add-new-item="addNewNotification"
		>
            <cx-vui-repeater-item
                v-for="( notif, index ) in settings.notifications"
                :title="getNotificationTitle( index )"
                :subtitle="getNotificationSubTitle( index )"
                :collapsed="true"
                :index="index"
                @clone-item="cloneNotification( index )"
                @delete-item="deleteNotification( index )"
                :key="index" 
		    >
		        <cx-vui-select
					label="<?php _e( 'Bot from Messenger', 'jet-messenger' ); ?>"
					description="<?php _e( 'Select the bot', 'jet-messenger' ); ?>"
					:options-list="botsList"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					size="fullwidth"
					v-model="notif.bot_id"
				></cx-vui-select>

                <template v-if="notif.bot_id">

                    <cx-vui-select
                        label="<?php _e( 'Action', 'jet-messenger' ); ?>"
                        description="<?php _e( 'Select the user`s action', 'jet-messenger' ); ?>"
                        :options-list="actionsList"
                        :wrapper-css="[ 'equalwidth' ]"
                        :placeholder="'Select...'"
                        size="fullwidth"
                        v-model="notif.action"
                    ></cx-vui-select>

                    <template v-if="notif.action">

                        <div class="cx-vui-inner-panel">
                            <cx-vui-repeater
                                    :button-label="'<?php _e( 'New Condition', 'jet-messenger' ); ?>'"
                                    :button-style="'accent'"
                                    :button-size="'mini'"
                                    v-model="notif.conditions"
                                    @add-new-item="addNewCondition( index )"
                            >
                                <cx-vui-repeater-item
                                        v-for="( condition, cond_index ) in notif.conditions"
                                        :title="getConditionTitle( index, cond_index )"
                                        :subtitle="getConditionSubTitle( index, cond_index )"
                                        :collapsed="true"
                                        :index="cond_index"
                                        @clone-item="cloneCondition( index, cond_index )"
                                        @delete-item="deleteCondition( index, cond_index )"
                                        :key="'cond_' + cond_index"
                                >
                                    <cx-vui-select
                                            label="<?php _e( 'Action Type', 'jet-messenger' ); ?>"
                                            description="<?php _e( 'Select the action`s trigger', 'jet-messenger' ); ?>"
                                            :options-list="getActionsOn( index )"
                                            :wrapper-css="[ 'equalwidth' ]"
                                            :placeholder="'Select...'"
                                            size="fullwidth"
                                            v-model="condition.action_type"
                                    ></cx-vui-select>
                                    <cx-vui-input
                                            v-if="'form_value' === condition.action_type"
                                            :label="'<?php _e( 'Field Name', 'jet-messenger' ) ?>'"
                                            :description="'<?php _e( 'Key (name/ID)', 'jet-messenger' ); ?>'"
                                            :wrapper-css="[ 'equalwidth' ]"
                                            :size="'fullwidth'"
                                            v-model="condition.field_name"
                                    ></cx-vui-input>

                                    <cx-vui-select
                                            label="<?php _e( 'Operator', 'jet-messenger' ); ?>"
                                            :options-list="operators_for_form"
                                            :description="'<?php _e( 'For In, Not in, Between and Not between compare separate multiple values with comma', 'jet-messenger' ) ?>'"
                                            :wrapper-css="[ 'equalwidth' ]"
                                            :placeholder="'Select...'"
                                            size="fullwidth"
                                            v-model="condition.operator"
                                    ></cx-vui-select>


                                    <cx-vui-input
                                            :label="'<?php _e( 'Action Value', 'jet-messenger' ) ?>'"
                                            :description="'<?php _e( 'Input or choose the action`s trigger', 'jet-messenger' ); ?>'"
                                            :wrapper-css="[ 'equalwidth' ]"
                                            :size="'fullwidth'"
                                            v-model="condition.action_value"
                                    ></cx-vui-input>
                                </cx-vui-repeater-item>
                            </cx-vui-repeater>

                        </div>
                        <cx-vui-textarea
                                :rows="15"
                                id="message-notification"
                                :description="'<?php _e( '<br>You can use <b>macros</b> here<br>Example: <i><b>%field_name%</b></i><br><br>You can also filter the output of received data through <b>filters</b><br>Example: <i><b>%field_name|filter_name|name_param:value%</b></i>','jet-engine' ); ?>'"
                                label="<?php _e( 'Message', 'jet-engine' ); ?>"
                                :wrapper-css="[ 'equalwidth' ]"
                                size="fullwidth"
                                @on-input-change="onInputMessage( $event, index )"
                                v-model="notif.message"
                        ></cx-vui-textarea>
                        <div class="save-notification-button">
                            <cx-vui-button
                                    @click="saveNotification( index )"
                                    :button-style="'accent'"
                                    :button-size="'mini'"
                            >
                                    <span slot="label">
                                        <?php esc_html_e( 'Save Notification', 'jet-messenger' );?>
                                    </span>
                            </cx-vui-button>
                        </div>
                    </template>
                </template>
			</cx-vui-repeater-item>
		</cx-vui-repeater>
    </div>
</div>