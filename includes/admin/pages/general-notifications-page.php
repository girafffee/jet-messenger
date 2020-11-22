<?php
namespace JET_MSG\Admin\Pages;

use JET_MSG\Admin\Helpers\Page_Config;
use JET_MSG\Plugin;

/**
 * Base dashboard page
 */
class General_Notifications_Page extends Base {

	/**
	 * Page slug
	 * @return string
	 */
	public function slug() {
		return 'jet-msg-general-notifications';
	}

	/**
	 * Page title
	 * @return string
	 */
	public function title() {
		return __( 'General Notifications', 'jet-messenger' );
	}
    
	/**
	 * Page render funciton
	 * @return void
	 */
	public function render() {
		echo '<div id="jet-msg-general-notifications-page"></div>';
	}

	/**
	 * Return  page config object
	 *
	 * @return [type] [description]
	 */
	public function page_config() {
		$active_bots = $this->get_active_bots_for_select();
		
		if ( empty( $active_bots ) ) {
			$fields = [
				'isset_bots' 			=> false,
				'return_to_options_url'	=> jet_msg()->dashboard->get_url_of( 'jet-msg-general-options' )
			];
		}
		else {
			$fields = [
				'actions_list'			=> $this->prepare_for_js_select( jet_msg()->general_notifications->column__action() ),
				'actions_on'			=> $this->prepare_for_js_select( jet_msg()->general_notifications->column__do_action_on() ),
                'operators_for_form'    => $this->prepare_for_js_select( jet_msg()->private_notifications->get_operators_options() ),
				'notifications_list'	=> jet_msg()->general_notifications->select_all(),
                'conditions'            => array(),
				'active_bots_list'		=> $active_bots,
				'isset_bots' 			=> true,
			];
		}

		return new Page_Config( $this->slug(), $fields );
	}

	/**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {
		//Enqueue script
		wp_enqueue_script( 'jet-msg-general-notifications-marked' );
		wp_enqueue_script( 'jet-msg-notifications-repeater' );
		$this->enqueue_script( $this->slug(), 'admin/general-notifications.js' );

		//Enqueue style
        wp_enqueue_style( 'jet-msg-general-notifications-admin' );
    }

	/**
	 * Page components templates
	 *
	 * @return [type] [description]
	 */
	public function vue_templates() {
		return array(
			'general-notifications',
			'notifications-repeater'
		);
	}

}