<?php
namespace JET_MSG\Admin\Pages;

use JET_MSG\Admin\Helpers\Page_Config;
use JET_MSG\Plugin;

/**
 * Base dashboard page
 */
class General_Options_Page extends Base {

	/**
	 * Page slug
	 * @return string
	 */
	public function slug() {
		return 'jet-msg-general-options';
	}

	/**
	 * Page title
	 * @return string
	 */
	public function title() {
		return __( 'JetMessenger', 'jet-messenger' );
	}
    
    /**
	 * Check if is setup page
	 *
	 * @return boolean [description]
	 */
	public function is_main_page() {
		return true;
	}

	/**
	 * Page render funciton
	 * @return void
	 */
	public function render() {
		echo '<div id="jet-msg-general-options-page"></div>';
	}

	/**
	 * Return  page config object
	 *
	 * @return [type] [description]
	 */
	public function page_config() {
		$is_set = jet_msg()->installer->checks_is_exists_secondary();

		$options = [
			'is_set' 	=> $is_set,
			'bots_list'	=> $is_set ? jet_msg()->general_options->select_all() : []
		];
		
		return new Page_Config( $this->slug(), $options );
	}

	/**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function assets() {
		$this->enqueue_script( 'momentjs', 'admin/lib/moment.min.js' );
		$this->enqueue_script( $this->slug(), 'admin/general-options.js' );

		//Enqueue style
		wp_enqueue_style( 'jet-msg-general-options-admin' );
	}

	/**
	 * Page components templates
	 *
	 * @return [type] [description]
	 */
	public function vue_templates() {
		return array(
			'general-options'
		);
	}

}