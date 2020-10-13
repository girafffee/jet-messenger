<?php
namespace JET_MSG\Admin;

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

        $this->set( 'last_updated_id', $this->last_updated_id( $this->get( 'bot_token' ) ) );
        if( jet_msg()->general_options->update( $this->fields, $this->get_condition() ) ) {
            wp_send_json_success(array(
                'message' => __('Options saved!', 'jet-messenger'),
            ));
        }
    }

    public function get__bot_creator_user_id() {
        return get_current_user_id();
    }

    public function last_updated_id( $token ) {
        $update = ( new Get_Updates( [], $token ) )->execute();

        return $this->set_last_updated_id( $update->response->result );
    }



    public function set_last_updated_id( $result ) {
        if ( empty( $result ) ) {
            return 0;
        }
        if ( is_array( $result ) ) {
            return (int) array_pop( $result )->update_id;
        }
        return (int) $result->update_id;
    }

    public function on_update_channel_name() {
        $text = __( 'This channel has been successfully connected to the ', 'jet-messenger' ) . get_bloginfo( 'name' );
        $message = new Send_Message( [
            'chat_id'       => '@' . $this->fields[ 'channel_name' ],
            'text'          => $text,
            'parse_mode'    => 'markdown'
        ] );

        $this->fields[ 'channel_id' ] = $message->execute()->get_chat_id();
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