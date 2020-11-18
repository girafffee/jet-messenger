<div class="wrap">
	<h1 class="cx-vui-title"><?php esc_html_e( 'JetMessenger Notifications', 'jet-messenger' ); ?></h1>

	<div class="jet-msg-notifications__none" v-if="!notifData.issetBots">
		<div class="cx-vui-panel">
			<div class="description">
				<?php _e( 
					'You must specify the Bot Token for one of the messengers or Upgrade Database to set the notification settings.',
				'jet-messenger' );
				?>
            </div>
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


	<jet-msg-notifications-repeater
		:settings-prop="notifData"
        :delete-ajax-hook="deleteNotification()"
        @save-notification="saveNotification"
		v-else
	></jet-msg-notifications-repeater>

    
</div>