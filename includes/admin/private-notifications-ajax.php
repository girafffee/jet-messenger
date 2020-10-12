<?php
namespace JET_MSG\Admin;


class Private_Notifications_Ajax extends Base_Ajax_Manager {

    public $get_prefix = 'get__';

    public function __construct() {
        $this->model = jet_msg()->private_notifications;

        add_action( 'wp_ajax_jet_msg_save_private_notification', [ $this, 'ajax_save_notification' ] );
        add_action( 'wp_ajax_jet_msg_delete_private_notification', [ $this, 'ajax_delete_notification' ] );
    }

}