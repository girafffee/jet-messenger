<?php
namespace JET_MSG\Admin;

use JET_MSG\Plugin;

/**
 * Dashboard interface manager
 */
class Dashboard {

	private $pages        = array();
	private $current_page = false;

	/**
	 * [__construct description]
	 * @param array $pages [description]
	 */
	public function __construct( $pages = array() ) {

		foreach ( $pages as $page ) {
			$this->pages[ $page->slug() ] = $page;
		}

		add_action( 'admin_menu', array( $this, 'register_pages' ) );

		if ( $this->is_dashboard_page() ) {
			$this->set_current_page();
			add_action( 'admin_enqueue_scripts', array( $this, 'assets' ) );
		}
	}
	
	/**
	 * Check if is dashboard page
	 *
	 * @return boolean [description]
	 */
	public function is_dashboard_page() {

		$page = ! empty( $_GET['page'] ) ? esc_attr( $_GET['page'] ) : false;

		if ( ! $page ) {
			return false;
		} else {
			return isset( $this->pages[ $page ] );
		}

	}

	public function set_current_page() {
		$this->current_page = $this->pages[ esc_attr( $_GET['page'] ) ];
	}

	/**
	 * Dashboard assets
	 *
	 * @param  [type] $hook [description]
	 * @return [type]       [description]
	 */
	public function assets( ) {

		if ( ! function_exists( 'jet_engine' ) ) {
			return;
		}

		$ui_data = jet_engine()->framework->get_included_module_data( 'cherry-x-vue-ui.php' );
		$ui      = new \CX_Vue_UI( $ui_data );	

		$ui->enqueue_assets();

		$this->current_page->assets();

		$config = $this->current_page->page_config();

		if ( $config->is_set() ) {
			wp_localize_script( $config->get( 'handle' ), 'JetMSGConfig', $config->get( 'config' ) );
		}

		add_action( 'admin_footer', array( $this, 'render_vue_templates' ) );

	}

	public function get_url_of( $page_slug ) {
		if ( ! isset( $this->pages[ $page_slug ] ) ) {
			return;
		}

		return $this->pages[ $page_slug ]->get_url();
	}

	/**
	 * Render vue templates set for current apge
	 *
	 * @return [type] [description]
	 */
	public function render_vue_templates() {
		$this->current_page->render_vue_templates();
	}

	/**
	 * Check if passed page is currently dispalyed
	 *
	 * @return boolean [description]
	 */
	public function is_page_now( $page ) {

		if ( ! $this->is_dashboard_page() ) {
			return false;
		}

		return ( $page->slug() === $this->current_page->slug() );

	}

    /**
	 * Register appointments
	 * @return [type] [description]
	 */
	public function register_pages() {

		$parent = false;

		foreach ( $this->pages as $page ) {

			if ( $page->is_hidden() ) {
				continue;
			}

			if ( ! $parent ) {

				$parent = $page->slug();

				add_menu_page(
					$page->title(),
					$page->title(),
					'manage_options',
					$page->slug(),
					array( $page, 'render' ),
					$page->get_icon()
				);

			} else {

				add_submenu_page(
					$parent,
					$page->title(),
					$page->title(),
					'manage_options',
					$page->slug(),
					array( $page, 'render' )
				);

			}
		}

	}



}