<?php


namespace JET_MSG\Admin;


use JET_MSG\Api\Telegram\Methods\Get_Updates;
use JET_MSG\Api\Telegram\Methods\Send_Message;

class Chats_Ajax extends Base_Ajax_Manager
{

    public $get_prefix = 'get__';

    public $chat;
    public $bot;
    public $update_obj;


    public function __construct() {
        $this->model = jet_msg()->chats;
        $this->set_messages();

        add_action( 'wp_ajax_jet_msg_save_chat', [ $this, 'ajax_save_chat' ] );
        add_action( 'wp_ajax_jet_msg_sync_chat', [ $this, 'ajax_sync_chat' ] );
        add_action( 'wp_ajax_jet_msg_is_synced_chat', [ $this, 'ajax_is_synced_chat' ] );
    }

    public function set_messages() {
        $this->messages = [
            'insert' => [
                'success'   => __( 'Chat inserted!', 'jet-messenger' ),
                'fail'      => __( 'Insert failed.', 'jet-messenger' )
            ],
            'update' => [
                'success'   => __( 'Chat updated!', 'jet-messenger' ),
                'fail'      => __( 'Update failed.', 'jet-messenger' )
            ],
            'delete' => [
                'success'   => __( 'Chat removed!', 'jet-messenger' ),
                'fail'      => __( 'Remove failed.', 'jet-messenger' )
            ]
        ];
    }



    public function ajax_is_synced_chat() {
        if( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) ) {
            wp_send_json_error( [
                'message' => __( "You can't do this, before you save the name of the chat", 'jet-messenger' ),
            ] );
            return;
        }
        $this->init_syncing( $_POST[ 'id' ], $_POST[ 'bot_slug' ] );

        if ( ! empty( $this->chat[ 'chat_id' ] ) ) {
            $this->success_synced();
            return;
        }
        $this->get_updates_and_save();

        if ( ! $this->get( 'chat_id' ) ) {
            return;
        }

        if ( $this->model->update( $this->fields, [ 'id' => $_POST['id'] ] ) ) {
            $this->maybe_set_last_updated_id();
            $this->success_synced();
        }

    }

    public function success_synced() {
        wp_send_json_success(array(
            'message' =>  __("Success synced", 'jet-messenger') . ' @' . $this->chat['chat_name'],
        ));
    }

    public function init_syncing( $id, $slug ) {
        $this->chat = $this->model->get_chat_by_id( $id );
        $this->bot  = jet_msg()->general_options->get_bot_by_slug( $slug );
    }

    public function get_updates_and_save() {
        $offset = isset( $this->bot[ 'last_updated_id' ] ) ? $this->bot[ 'last_updated_id' ] : 0;

        $this->update_obj = ( new Get_Updates( [
            'offset' => $offset
        ] ) )->execute();

        $this->maybe_set_chat_id()->maybe_set_sync( 'enabled' )->maybe_send_message();
    }

    public function maybe_set_chat_id() {

        $result = $this->update_obj->find_message( [
            'compare_type_message'  => 'private',
            'compare_username'      => $this->chat[ 'chat_name' ],
            'compare_message'       => $this->chat[ 'sync_code' ]
        ] );

        if ( $result instanceof \stdClass) {
            $this->set( 'chat_id', $this->update_obj->get_chat_id_obj( $result ) );
        }

        return $this;
    }


    public function maybe_set_sync( $status ) {
        if ( ! empty( $this->get( 'chat_id' ) ) ) {
            $this->set( 'status', $status );
            $this->set( 'sync_code' );
        }
        return $this;
    }

    public function maybe_send_message() {
        if ( ! empty( $this->get( 'chat_id' ) ) ) {
            $this->send_message_on_sync();
        }
    }

    public function send_message_on_sync() {
        $text = __( 'Your account has been successfully connected to the ', 'jet-messenger' ) . get_bloginfo( 'name' );

        $message = new Send_Message( [
            'chat_id'       => $this->get( 'chat_id' ),
            'text'          => $text,
            'parse_mode'    => 'markdown'
        ] );
        $message->execute();
    }

    public function maybe_set_last_updated_id( ) {

    }

    public function ajax_sync_chat() {
        if( empty( $_POST['id'] ) || ! is_numeric( $_POST['id'] ) ) {
            wp_send_json_error( [
                'message' => __( "You can't do this, before you save the name of the chat", 'jet-messenger' ),
            ] );
            return;
        }

        wp_send_json_success( array(
            'sync_code'        => $this->model->get_sync_code( $_POST['id'] )
        ) );
    }

    public function ajax_save_chat() {
        if( empty( $_POST['data'] ) ) {
            wp_send_json_error( [
                'message' => __( 'Your name is empty', 'jet-messenger' ),
            ] );
            return;
        }

        $this->mode = $_POST['mode'];
        $this->parse_data( $_POST['data'] );

        if ( $this->insert_with_ajax() ) {
            return;
        }

        $this->maybe_unsync();
        if ( ! $this->update_with_ajax() ) {
            wp_send_json_error( array(
                'message' => __( 'Something was wrong...', 'jet-messenger' ),
            ));
        }
    }

    public function maybe_unsync() {
        if ( ! $this->get( 'chat_name' ) ) {
            $this->set( 'chat_id' );
            $this->set( 'sync_code' );
            $this->set( 'status', 'disabled' );
        }
    }

    public function get__wp_user_id() {
        return get_current_user_id();
    }

    public function get__chat_type() {
        return 'private';
    }

    public function get__status() {
        return 'disabled';
    }

    public function get__bot_slug() {
        return 'telegram';
    }

}