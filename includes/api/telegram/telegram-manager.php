<?php

namespace JET_MSG\Api\Telegram;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Telegram_Manager {

    public static $is_send = false;

    private $token;
    private $id;

    public $slug = 'telegram';
    public $notifications_data;

    public function __construct() {
        add_action( 'init', [ $this, 'init' ] );
    }

    public function apiUrl( $method = '' ) {
        return 'https://api.telegram.org/bot' . $this->token . '/' . $method;
    }

    public function init() {
        if ( ! jet_msg()->installer->checks_is_exists_secondary() 
        || ! $this->set_bot_data() 
        || ! $this->set_notifications() ) {
            return;    
        }
        $this->attach_notifications();
    }

    public function attach_notifications() {

        foreach ( $this->notifications as $notif ) {
            
            $class_name = 'JET_MSG\\Api\\Telegram\\Actions\\' . $this->make_class_name( $notif['action'] );
            new $class_name( $notif );
        }
    }

    public function make_class_name( $action ) {
        $action_class = explode( '_', $action );

        foreach ($action_class as $key => $value) {
            $action_class[ $key ] = ucfirst( $value );
        }

        return implode( '_', $action_class );
    }


    public function set_notifications(){
        $this->notifications = jet_msg()->general_notifications->get_by_bot_id( $this->id );

        return ( ! empty( $this->notifications ) );
    }

    public function set_bot_data() {
        [ 
            'bot_token'     => $this->token, 
            'id'            => $this->id, 
            'channel_id'    => $this->channel_id

        ] = jet_msg()->general_options->get_bot_by_slug( $this->slug );

        return ( ! empty( $this->token ) );
    }

    public function send_message( $options ) {
       
        $ch = curl_init();
        curl_setopt_array(
            $ch,
            array(
                CURLOPT_URL => $this->apiUrl('sendMessage'),
                CURLOPT_POST => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_POSTFIELDS => array(
                    'chat_id'       => isset( $options[ 'id' ] ) ? $options[ 'id' ] : $this->channel_id,
                    'text'          => $options[ 'message' ],
                    'parse_mode'    => 'markdown'
                ),
            )
        );
        $this->result_exec = json_decode( curl_exec($ch) );
        return $this;
    }


    public function get_chat_id() {
        if ( ! $this->result_exec || ! $this->result_exec->ok ) return;

        return (int) $this->result_exec->result->chat->id;
    }
}

