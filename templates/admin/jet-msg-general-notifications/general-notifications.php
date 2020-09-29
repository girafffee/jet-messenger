<div class="wrap">
	<h1 class="cx-vui-title"><?php esc_html_e( 'JetMessenger Notifications', 'jet-messenger' ); ?></h1>

	<div class="jet-msg-notifications__none" v-if="!issetBots">
		<div class="cx-vui-panel">
			<div class="description">
				<?php _e( 
					'You must specify the Bot Token for one of the messengers or Upgrade Database to set the notification settings.',
				'jet-messenger' );
				?></div>
				<cx-vui-button
							@click="returnToGeneral"
							button-style="accent"
						>
						<span slot="label"><?php
							esc_html_e( 'Return to General Options', 'jet-messenger' );
						?></span>
				</cx-vui-button>
		</div>
	</div>
	<div class="jet-msg-notifications" v-else>
	
		<div class="cx-vui-inner-panel">
					<cx-vui-repeater
						:button-label="'<?php _e( 'New Notification', 'jet-messenger' ); ?>'"
						:button-style="'accent'"
						v-model="notifications"
						@add-new-item="addNewNotification"
					>
						<cx-vui-repeater-item
							v-for="( notif, index ) in notifications"
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
								:options-list="botsList"
								:wrapper-css="[ 'equalwidth' ]"
								:placeholder="'Select...'"
								size="fullwidth"
								v-model="notif.bot_id"
							></cx-vui-select>
							<cx-vui-select
								label="<?php _e( 'Action', 'jet-messenger' ); ?>"
								description="<?php _e( 'Select the user`s action', 'jet-messenger' ); ?>"
								:options-list="actionsList"
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
								<div v-html="compiledMarkdown( index )"></div>
							</div>
							<div class="save-button">
								<cx-vui-button
												@click="saveNotification( index )"
												button-style="accent"
												:disabled="isValuesReady( index )"
											>
											<span slot="label"><?php
												esc_html_e( 'Save Notification', 'jet-messenger' );
											?></span>
								</cx-vui-button>
							</div>
							
							
						</cx-vui-repeater-item>
					</cx-vui-repeater>
		</div>

	</div>
    
</div>