<?php
namespace JET_MSG\Admin;


class General_Notifications_Ajax extends Base_Ajax_Manager {

    public $get_prefix = 'get__';

    public function __construct() {
        $this->model = jet_msg()->general_notifications;

        add_action( 'wp_ajax_jet_msg_save_action', [ $this, 'ajax_save_notification' ] );
        add_action( 'wp_ajax_jet_msg_delete_notification', [ $this, 'ajax_delete_notification' ] );
    }

}