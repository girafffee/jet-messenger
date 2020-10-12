<div class="wrap">
	<h1 class="cs-vui-title"><?php esc_html_e( 'JetMessenger', 'jet-messenger' ); ?></h1>

	<div class="jet-msg-general__loading" v-if="isLoading"></div>
	
	<div class="jet-msg-general__none" v-else-if="!isSet">
		<div class="cx-vui-panel">
			<div class="description">
				<?php _e( 
					'This plugin will create two tables in your database. 
					They will contain information about your bots from 
					such messengers as <strong>Telegram</strong>, <strong>Viber</strong> 
					and <strong>WhatsApp</strong>, as well as individual user settings.',
				'jet-messenger' );
				?></div>
				<cx-vui-button
							@click="installDB"
							button-style="accent"
						>
						<span slot="label"><?php
							esc_html_e( 'Install DB', 'jet-messenger' );
						?></span>
				</cx-vui-button>
		</div>
	</div>	
	<div class="cx-vui-panel" v-for="( bot, index ) in bots" v-else>
		<cx-vui-input
					:name="bot.bot_slug"
					:label="bot.bot_name"
					:description="'<?php _e( 'Input token your bot', 'jet-messenger' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model.trim="bot.bot_token"
					@on-input-change="updateBot( index )"
		></cx-vui-input>

		<cx-vui-input
					:label="'<?php _e( 'Channel name', 'jet-messenger' ); ?>'"
					:description="'<?php _e( 'Input your chanel name (without `@`)', 'jet-messenger' ); ?>'"
					:wrapper-css="[ 'equalwidth' ]"
					:size="'fullwidth'"
					v-model.trim="bot.channel_name"
					@on-input-change="updateBot( index )"
		></cx-vui-input>
	</div>

	
</div>
