<?php
namespace JET_MSG\Admin\Helpers;

/**
 * Base dashboard page
 */
class Page_Config {

	private $handle = null;
	private $config = array();

	/**
	 * Setup props
	 */
	public function __construct( $handle = null, $config = array() ) {
		$this->handle = $handle;
		$this->config = apply_filters( 'jet-msg/admin/helpers/page-config/config', $config );
	}

	/**
	 * Check if config is not empty
	 *
	 * @return [type] [description]
	 */
	public function is_set() {
		return ( ! empty( $this->handle ) && ! empty( $this->config ) );
	}

	/**
	 * Get cofig prop
	 *
	 * @return [type] [description]
	 */
	public function get( $prop ) {
		return isset( $this->$prop ) ? $this->$prop : false;
	}

}
