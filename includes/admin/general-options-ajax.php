<?php
namespace JET_MSG\Admin;

use JET_MSG\Api\Telegram\Methods\Get_Me;
use JET_MSG\Api\Telegram\Methods\Get_Updates;
use JET_MSG\Api\Telegram\Methods\Send_Message;


class General_Options_Ajax extends Base_Ajax_Manager {

    public $get_prefix = 'get__';

    public function __construct() {
        $this->model = jet_msg()->general_options;

        add_action( 'wp_ajax_jet_msg_save_token', [ $this, 'ajax_save_token' ] );
		add_action( 'wp_ajax_jet_msg_get_all_bots', [ $this, 'get_all_bots'] );
		add_action( 'wp_ajax_jet_msg_db_install', [ $this, 'ajax_install'] );
    }

    public function ajax_save_token() {
        if( ! isset( $_POST[ 'data' ] ) ) {
            wp_send_json_error();
            return;
        }

        $this->parse_data( $_POST[ 'data' ] );

        if( ! empty( $this->errors ) ) {
            wp_send_json_error( [
                'message' => __( 'Validation failed!', 'jet-messenger' ),
            ] );
            return;
        }

        $this->maybe_set_bot_info();
        if ( ! empty( $this->errors ) ) {
            wp_send_json_error( [
                'message' => __( 'Not a valid token!', 'jet-messenger' ),
            ] );
            return;
        }

        $this->set_last_updated_id();

        if( jet_msg()->general_options->update( $this->fields, $this->get_condition() ) ) {
            wp_send_json_success(array(
                'message' => __('Options saved!', 'jet-messenger'),
            ));
        }
    }

    public function maybe_set_bot_info() {
        if ( empty( $this->get( 'bot_token' ) ) ) {
            $this->set( 'bot_name' );
            $this->set( 'bot_label' );
            return;
        }

        $bot = ( new Get_Me( [], $this->get( 'bot_token' ) ) )->execute();

        if ( $bot->is_ok() && $bot->get_result()->is_bot ) {
            $this->set( 'bot_name', $bot->get_result()->username );
            $this->set( 'bot_label', $bot->get_result()->first_name );
        }
        else {
            $this->errors[] = 'bot_token';
        }
    }

    public function set_last_updated_id() {
        if ( empty( $this->get( 'bot_token' ) ) ) {
            $this->set( 'last_updated_id' );
            return;
        }

        $update = ( new Get_Updates( [], $this->get( 'bot_token' ) ) )->execute();

        if ( $update->is_ok() ) {
            $id = $this->get_last_updated_id( $update->response->result );
            $this->set( 'last_updated_id', $id );
        }
        else {
            $this->errors[] = 'last_updated_id';
        }
    }

    public function get_last_updated_id( $result ) {
        if ( empty( $result ) ) {
            return 0;
        }
        if ( is_array( $result ) ) {
            return (int) array_pop( $result )->update_id;
        }
        return (int) $result->update_id;
    }

    public function get__bot_creator_user_id() {
        return get_current_user_id();
    }

    public function on_update_channel_name( $value ) {
        $text = __( 'This channel has been successfully connected to the ', 'jet-messenger' ) . get_bloginfo( 'name' );

        $message = new Send_Message( [
            'chat_id'       => '@' . $this->fields[ 'channel_name' ],
            'text'          => $text,
            'parse_mode'    => 'markdown'
        ], $this->get( 'bot_token' ) );

        $this->set( 'channel_id', $message->execute()->get_chat_id() );

        return $value;
    }

    public function get_all_bots() {
        if ( ! jet_msg()->installer->checks_is_exists_secondary() ) {
            wp_send_json_error();
            return;
        }

        $bots = jet_msg()->general_options->select_all();

        if ( ! $bots ) {
            wp_send_json_error();
            return;
        }

        wp_send_json_success( array(
            'botsInfo' => $bots
        ) );
    }

    public function ajax_install() {
        jet_msg()->installer->install_models();

        wp_send_json_success( array(
            'message' => __( 'Success!', 'jet-messenger' ),
        ) );
    }

    


}