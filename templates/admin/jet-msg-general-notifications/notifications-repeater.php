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
                :collapsed="'true'"
                :index="index"
                @clone-item="cloneNotification( index )"
                @delete-item="deleteNotification( index )"
                :key="index" 
		    >
		        <cx-vui-select
					label="<?php _e( 'Bot from Messenger', 'jet-messenger' ); ?>"
					description="<?php _e( 'Select the bot', 'jet-messenger' ); ?>"
					:options-list="settings.botsList"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					size="fullwidth"
					v-model="notif.bot_id"
				></cx-vui-select>
				
                <cx-vui-select
				    label="<?php _e( 'Action', 'jet-messenger' ); ?>"
					description="<?php _e( 'Select the user`s action', 'jet-messenger' ); ?>"
					:options-list="settings.actionsList"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					size="fullwidth"
					v-model="notif.action"
				></cx-vui-select>

				<cx-vui-select
				    label="<?php _e( 'Action Trigger', 'jet-messenger' ); ?>"
					description="<?php _e( 'Select the action`s trigger', 'jet-messenger' ); ?>"
					:options-list="getActionsOn( index )"
					:wrapper-css="[ 'equalwidth' ]"
					:placeholder="'Select...'"
					size="fullwidth"
					v-model="notif.do_action_on"
        		></cx-vui-select>			

				<cx-vui-input
				    :label="'Action Value'"
					:description="'<?php _e( 'Input or choose the action`s trigger', 'jet-messenger' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model="notif.action_value"
				></cx-vui-input>


				<div id="jet-msg__editor">
				    <textarea :value="notif.message" @input="updateEditor( $event, index )"></textarea>
					<div class="result" v-html="compiledMarkdown( index )"></div>
                    <div class="save-button">
                        <cx-vui-button
                                @click="saveNotification( index )"
                                button-style="accent"
                                :disabled="isValuesReady( index )"
                        >
                        <span slot="label">
                            <?php esc_html_e( 'Save Notification', 'jet-messenger' );?>
                        </span>
                        </cx-vui-button>
                    </div>
                <div>

			</cx-vui-repeater-item>
		</cx-vui-repeater>
    </div>
</div>