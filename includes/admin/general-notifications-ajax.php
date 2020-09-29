<?php
namespace JET_MSG\Admin;

use JET_MSG\Plugin;


class General_Notifications_Ajax extends Base_Ajax_Manager {

    public $get_prefix = 'get__';

    public function __construct() {
        $this->model = jet_msg()->general_notifications;

        add_action( 'wp_ajax_jet_msg_save_action', [ $this, 'ajax_save_action' ] );
        add_action( 'wp_ajax_jet_msg_delete_notification', [ $this, 'ajax_delete_notification' ] );
    }

    public function ajax_delete_notification() {
        if( ! isset( $_POST['data'] ) ) {
            wp_send_json_error();
            return;
        }

        $this->mode = $_POST['mode'];
        $this->parse_data( $_POST['data'] );

        if( $this->model->delete( $this->get_condition() ) ) {

            wp_send_json_success( array(
                'message'   => __( 'Notification has been removed.', 'jet-messenger' )
            ) );
            return;
        }

        wp_send_json_error( array(
            'message' => __( 'Something was wrong...', 'jet-messenger' ),
        ));
    }

    public function ajax_save_action() {
        if( ! isset( $_POST['data'] ) ) {
            wp_send_json_error();
            return;
        }
        $message = 'Action saved!';

        $this->mode = $_POST['mode'];
        $this->parse_data( $_POST['data'] );

        if( empty( $this->get_condition() ) &&
         ( $inserted_id = $this->model->insert( $this->fields ) ) ) {
            
            wp_send_json_success( array(
                'message'   => __( $message, 'jet-messenger' ),
                'id'        => $inserted_id
            ) );
            return;
        }

        $sql = $this->model->select('COUNT(*)')->where_equally( $this->get_condition() )->get_sql();   

        if ( $this->model->wpdb()->get_var( $sql ) ) {

            $success = $this->model->update( $this->fields, $this->get_condition() );

            if ( $success ) {
                wp_send_json_success( array(
                    'message' => __( $message, 'jet-messenger' ),
                ) );
                return;
            }
            else {
                wp_send_json_error();
                return;
            }
        }
        
        wp_send_json_error( array(
            'message' => __( 'Something was wrong...', 'jet-messenger' ),
        ));
    }

    

}