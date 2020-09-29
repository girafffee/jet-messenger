<?php
namespace JET_MSG;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Plugin {
        
    /**
     * Instance.
     *
     * Holds the plugin instance.
     *
     * @since 1.0.0
     * @access public
     * @static
     *
     * @var Plugin
     */
    public static $instance = null;
    public $dashboard;
    public $plugin;

    private $version = '1.0.0';
    
    private $plugin_path;
    private $plugin_basename;
    private $plugin_url;

    
    public function __construct() {
        $this->init();

        if( ! $this->dependence() ) {
            return;
        }
        $this->register_autoloader();

        add_action( 'after_setup_theme', [ $this, 'init_components' ], 0 );    
    }


    public function init() {
        $this->plugin_basename = plugin_basename( JET_MSG__FILE__ );
        $this->plugin_path = plugin_dir_path( JET_MSG__FILE__ );
        $this->plugin_url = plugins_url( '/' , JET_MSG__FILE__ );  
    }

    public function init_components() {
       
        add_action( 'admin_enqueue_scripts', [ $this, 'register_assets' ], 0 );
        
        $this->general_options          = new DB\General_Options_Model();
        $this->general_notifications    = new DB\General_Notifications_Model();
        $this->user_options             = new DB\Users_Notifications_Model();
        $this->ajax_options             = new Admin\General_Options_Ajax();
        $this->ajax_notifications       = new Admin\General_Notifications_Ajax();

        // Init DB installer
        $this->installer = new Jet_Messenger_DB_Install(
            $this->general_options,
            $this->general_notifications,
            $this->user_options
        );
        
        $this->telegram_manager = new Api\Telegram\Telegram_Manager();

        if ( is_admin() ) {

            $this->dashboard = new Admin\Dashboard( [
                new Admin\Pages\General_Options_Page(),
                new Admin\Pages\General_Notifications_Page(),
                new Admin\Pages\Users_Notifications_Page()
            ] );
        
        }
    }

    /**
	 * Page specific assets
	 *
	 * @return [type] [description]
	 */
	public function register_assets() {
		$this->register_style( 'jet-msg-general-options-admin', 'admin/general-options.css' );
        $this->register_style( 'jet-msg-general-notifications-admin', 'admin/general-notifications.css' );
        wp_register_script( 'jet-msg-general-notifications-marked', 'https://unpkg.com/marked@0.3.6', [ 'wp-api-fetch' ], $this->version());
    }
    
    /**
	 * Register script
	 *
	 * @param  [type] $handle    [description]
	 * @param  [type] $file_path [description]
	 * @return [type]            [description]
	 */
	public function register_script( $handle = null, $file_path = null ) {
		return wp_register_script(
			$handle,
			$this->plugin_url() . 'assets/js/' . $file_path,
			array( 'wp-api-fetch' ),
			$this->version(),
			true
		);
	}

	/**
	 * Register style
	 *
	 * @param  [type] $handle    [description]
	 * @param  [type] $file_path [description]
	 * @return [type]            [description]
	 */
	public function register_style( $handle = null, $file_path = null ) {
		if ( ! wp_register_style(
			$handle,
            $this->plugin_url() . 'assets/css/' . $file_path,
			array(),
            $this->version()
		) ) {
            error_log( printf('Cannot register the [%s] style', $handle) );
        }
	}

    private function dependence() {
        if ( ! function_exists( 'jet_engine' ) ) {

            add_action( 'admin_notices', function() {
                $class = 'notice notice-error';
                $message = __( '<b>WARNING!</b> <b>JetMessenger</b> plugin requires <b>JetEngine</b> plugin to work properly!', 'jet-messenger' );
                printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
            } );

            return false;
        }
        return true;
    }
    

    public function version(){
        return $this->version;
    }

    public function plugin_path( $path = "" ) {
        return $this->plugin_path . $path;
    }

    public function plugin_basename() {
        return $this->plugin_basename;
    }

    public function plugin_url() {
        return $this->plugin_url;
    }

    /**
     * Register autoloader.
     */
    public function register_autoloader() {
        require $this->plugin_path . 'includes/autoloader.php';
        Autoloader::save_path( $this->plugin_path );
        Autoloader::run();
    }   

    /**
     * Returns the instance.
     *
     * @since  1.0.0
     * @access public
     * @return Jet_Messenger
     */
    public static function get_instance() {
        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

}

jet_msg();


