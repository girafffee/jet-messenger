<?php
namespace JET_MSG\Admin;


class Private_Notifications_Ajax extends Base_Ajax_Manager {

    public $get_prefix = 'get__';

    public function __construct() {
        $this->model = jet_msg()->private_notifications;
        $this->set_messages();

        add_action( 'wp_ajax_jet_msg_save_private_notification', [ $this, 'ajax_save_data' ] );
        add_action( 'wp_ajax_jet_msg_delete_private_notification', [ $this, 'ajax_delete_data' ] );
    }

    public function set_messages() {
        $this->messages = [
            'insert' => [
                'success'   => __( 'Notification inserted!', 'jet-messenger' ),
                'fail'      => __( 'Insert failed.', 'jet-messenger' )
            ],
            'update' => [
                'success'   => __( 'Notification updated!', 'jet-messenger' ),
                'fail'      => __( 'Update failed.', 'jet-messenger' )
            ],
            'delete' => [
                'success'   => __( 'Notification has been removed.', 'jet-messenger' ),
                'fail'      => __( 'Remove failed.', 'jet-messenger' )
            ]
        ];
    }


}

