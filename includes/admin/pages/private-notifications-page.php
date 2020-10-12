<?php
namespace JET_MSG\Admin\Pages;

use JET_MSG\Admin\Helpers\Page_Config;
use JET_MSG\Plugin;

/**
 * Base dashboard page
 */
class Private_Notifications_Page extends Base {

	/**
	 * Page slug
	 * @return string
	 */
	public function slug() {
		return 'jet-msg-private-notifications';
	}

	/**
	 * Page title
	 * @return string
	 */
	public function title() {
		return __( 'Private Notifications', 'jet-messenger' );
	}
    
	/**
	 * Page render funciton
	 * @return void
	 */
	public function render() {
		echo '<div id="jet-msg-private-notifications-page"></div>';
	}

	/**
	 * Return  page config object
	 *
	 * @return [type] [description]
	 */
	public function page_config() {
        $active_bots = $this->get_active_bots_for_select();
        $chat = $this->get_chat_for_current_user();

        if ( empty( $active_bots ) ) {
            $fields = [
                'isset_bots' 			=> false,
                'return_to_options_url'	=> jet_msg()->dashboard->get_url_of( 'jet-msg-general-options' )
            ];
        }
        else if ( ! $chat ) {
            $fields = [
                'isset_chat' 			=> false,
                'return_to_options_url'	=> jet_msg()->dashboard->get_url_of( 'jet-msg-general-options' )
            ];
        }
        else {
            $fields = [
                'actions_list'			=> $this->prepare_for_js_select( jet_msg()->private_notifications->column__action() ),
                'actions_on'			=> $this->prepare_for_js_select( jet_msg()->private_notifications->column__do_action_on() ),
                'notifications_list'	=> jet_msg()->private_notifications->select_all_for_current_user(),
                'active_bots_list'		=> $active_bots,
                'chat_data'             => $this->get_chat_for_current_user(),
                'isset_bots' 			=> true,
                'isset_chat'            => true,
                'return_to_options_url'	=> ''
            ];
        }

        return new Page_Config( $this->slug(), $fields );
	}

    public function get_chat_for_current_user() {
	    $user_id = get_current_user_id();
	    $chat = jet_msg()->chats->get_chat_by_wp_user_id( $user_id );

	    return ( empty( $chat ) ? jet_msg()->chats->get_empty_columns() : $chat );
    }

	/**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {

        wp_enqueue_script( 'jet-msg-general-notifications-marked' );
        wp_enqueue_script( 'jet-msg-notifications-repeater' );
        wp_enqueue_script( 'jet-msg-private-notifications' );

        wp_enqueue_style( 'jet-msg-general-notifications-admin' );
        wp_enqueue_style( 'jet-msg-private-notifications-admin' );
	}

	/**
	 * Page components templates
	 *
	 * @return [type] [description]
	 */
	public function vue_templates() {
		return [
			'private-notifications',
			[
				'dir'  => 'jet-msg-general-notifications',
				'file' => 'notifications-repeater'
			]
		];
	}

}