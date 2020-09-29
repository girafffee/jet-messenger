<?php
namespace JET_MSG\Admin\Pages;

use JET_MSG\Admin\Helpers\Page_Config;
use JET_MSG\Plugin;

/**
 * Base dashboard page
 */
class Users_Notifications_Page extends Base {

	/**
	 * Page slug
	 * @return string
	 */
	public function slug() {
		return 'jet-msg-users-notifications';
	}

	/**
	 * Page title
	 * @return string
	 */
	public function title() {
		return __( 'Users Notifications', 'jet-messenger' );
	}
    
	/**
	 * Page render funciton
	 * @return void
	 */
	public function render() {
		echo '<div class="wrap"><div id="jet-msg-users-notifications-page"><h1>Users notifications page!</h1></div></div>';
	}

	/**
	 * Return  page config object
	 *
	 * @return [type] [description]
	 */
	public function page_config() {

		return new Page_Config(
			$this->slug(),
			array()
		);
	}

	/**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {
		//Enqueue script
	}

	/**
	 * Page components templates
	 *
	 * @return [type] [description]
	 */
	public function vue_templates() {
		return array();
	}

}