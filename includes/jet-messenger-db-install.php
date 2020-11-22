<?php
namespace JET_MSG;

/**
 * Plugin setup class
 */
class Jet_Messenger_DB_Install {

    public $models      = [];
    public $is_exists   = [];
    
    private $is_check_completed = false;

    public function __construct( ...$models ) {

        $this->models( $models );

        add_action( 'init', [ $this, 'install_or_inform' ] );
    }

    public function install_or_inform() {
        if ( $this->checks_is_exists() ) return;

        if ( ! empty( $_GET['jet-messenger-db-install'] ) ) {
            $this->install_models();

            wp_redirect( admin_url( 'plugins.php' ) , 301);
        }

        if ( isset( jet_msg()->dashboard ) && ! jet_msg()->dashboard->is_dashboard_page() ) {
            $this->output_message();
        }
    }

    public function output_message() {
                
        add_action( 'admin_notices', function() {
            $class = 'notice notice-info';
            $message = __( '<b>WARNING!</b> <b>JetMessenger</b> plugin requires update Database to work properly!
            <a href="'. esc_url( add_query_arg( [ 'jet-messenger-db-install' => true ] ) ) .'"><button class="button button-primary">Update</button></a>', 'jet-messenger' );
            printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), wp_kses_post( $message ) );
        } );
        
    }

    public function checks_is_exists() {
        $result = true;

        foreach ( $this->models as $model ) {
            if ( ! $model->is_exists() ) {
                $result = false;
                continue;
            }
            $this->is_exists[ $model->table() ] = true;
        }
        $this->is_check_completed = true;

        return $result;
    }

    /**
     * You can use this check-function only after checks_is_exists()
     * used in ajax-requests
     */
    public function checks_is_exists_secondary() {
        if ( ! $this->is_check_completed ) { 
            return false; 
        }

        foreach ( $this->models as $model ) {
            if ( ! $this->is_exists[ $model->table() ] ) {
                return false;
            }   
        }
        return true;
    }

    public function models( $models ) {
        foreach ( $models as $model ) {
            if ( $model instanceof DB\Base\Base_Model ) {
                $this->models[] = $model;
                $this->is_exists[ $model->table() ] = false;
            }
        }
    }

    public function install_models() {
        foreach ( $this->models as $model ) {
            if ( ! $this->is_exists[ $model->table() ] ) {
                $model->install_table();
            }
        } 
    }
}