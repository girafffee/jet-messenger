<div class="wrap">
	<h1 class="cs-vui-title"><?php esc_html_e( 'Private Notifications', 'jet-messenger' ); ?></h1>

    <div class="jet-msg-private-chat">
        <div class="cx-vui-panel">
            <cx-vui-input
                    :label="'<?php _e( 'User name', 'jet-messenger' ); ?>'"
                    :description="'<?php _e( 'Input your name (without `@`)', 'jet-messenger' ); ?>'"
                    :wrapper-css="[ 'equalwidth' ]"
                    :size="'fullwidth'"
                    v-model="chatData.chat_name"
                    @on-input-change="saveChat"
            ></cx-vui-input>

            <div class="sync-button" style="padding: 20px;">
                <cx-vui-button
                        @click="syncChat"
                        button-style="accent"
                        v-if="showSyncButton || chatData.status === 'disabled' || chatData.status === 'pending'"
                >
                    <span slot="label"><?php esc_html_e( 'Sync', 'jet-messenger' ); ?>
                    </span>
                </cx-vui-button>

                <span class="dashicons dashicons-saved chat-enabled" v-else-if="showEnabledIcon || chatData.status === 'enabled'"></span>
            </div>

        </div>
    </div>


    <jet-msg-notifications-repeater
            :settings-prop="notifData"
            @save-notification="saveNotification"
            :delete-ajax-hook="deleteNotification()"
            :chat-id="Number( chatData.id )"
            v-if="showEnabledIcon || chatData.status === 'enabled'"
    ></jet-msg-notifications-repeater>

    <div class="jet-msg-notifications__none" v-else-if="!issetBots">
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

    <div class="jet-msg-notifications__none" v-else-if="!chatData.id">
        <div class="cx-vui-panel">
            <div class="description">
                <?php _e(
                    'Please sync your Telegram account, input your @username to field and send Code to Bot',
                    'jet-messenger' );
                ?>
            </div>
        </div>
    </div>

    <cx-vui-popup
            v-model="showPopup"
            body-width="600px"
            :show-ok="false"
            cancel-label="<?php _e( 'Close', 'jet-messenger' ) ?>"
    >
        <div class="cx-vui-subtitle" slot="title"><?php
            _e( 'Preset Imported!', 'jet-messenger' );
            ?></div>
        <div slot="content">
            <p>Send this code to <a :href="getBotUrl()" target="_blank">{{ getBotLabel() }}</a></p>
            {{ chatData.sync_code }}
        </div>
    </cx-vui-popup>

</div>