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

    public $bot_token = '';
    private $id = '';
    public $channel_id = '';

    public $slug = 'telegram';
    public $notifications;
    public $private_notifications;

    const REST_API_NAMESPACE = 'jet-messenger/v1';

    public function __construct() {
        add_action( 'init', [ $this, 'maybe_init' ] );
        add_action( 'rest_api_init', [ $this, 'add_routes_rest_api' ] );
    }

    public function apiUrl( $method = '' ) {
        return 'https://api.telegram.org/bot' . $this->bot_token . '/' . $method;
    }


    public function get_rest_api_routes() {
        return [
            '/message-endpoint' => [
                'methods'    => 'GET',
                'callback'  => [ $this, 'telegram_message_endpoint' ],
            ]
        ];
    }

    public function maybe_init() {
        if ( jet_msg()->installer->checks_is_exists_secondary()
            && $this->set_bot_data() )
        {
            $this->init();
        }
    }

    public function init() {
        $this->set_notifications();
        $this->set_private_notifications();

        $this->attach_notifications();
        $this->attach_private_notifications();
    }

    public function attach_notifications() {
        $this->attach_notification_actions( $this->notifications );
    }

    public function attach_private_notifications() {
        $this->attach_notification_actions( $this->private_notifications );
    }

    public function attach_notification_actions( $notifications ) {
        if ( empty( $notifications ) ) return;

        $namespace = 'JET_MSG\\Api\\Telegram\\Actions\\';

        foreach ( $notifications as $notif )
        {
            $class_name = $namespace . $this->make_class_name( $notif['action'] );

            if ( class_exists( $class_name ) ) {
                new $class_name( $notif );
            }
        }
    }

    public function make_class_name( $action ) {
        $action_class = explode( '_', $action );

        foreach ($action_class as $key => $value) {
            $action_class[ $key ] = ucfirst( $value );
        }

        return implode( '_', $action_class );
    }

    public function add_routes_rest_api() {
        foreach ( $this->get_rest_api_routes() as $route => $value ) {
            register_rest_route( self::REST_API_NAMESPACE , $route , $value );
        }
    }

    public function telegram_message_endpoint() {
        $this->send_message( [ 'message' => 'Heelllooo!!' ] );
    }


    public function set_notifications(){
        $this->notifications = jet_msg()->general_notifications->get_by_bot_id( $this->id );
        return ( ! empty( $this->notifications ) );
    }

    public function set_private_notifications() {

        $this->private_notifications = jet_msg()->private_notifications->get_by_bot_id( $this->id );
        return ( ! empty( $this->private_notifications ) );
    }

    public function set_bot_data() {
        $bot_data = jet_msg()->general_options->get_bot_by_slug( $this->slug );
        $set_keys = [ 'bot_token', 'id', 'channel_id' ];

        foreach ( $set_keys as $key ) {
            if ( isset( $bot_data[ $key ] ) ) {
                $this->$key = $bot_data[ $key ];
            }
        }

        return ( ! empty( $this->bot_token ) );
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

}

