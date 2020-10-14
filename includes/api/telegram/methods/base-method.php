<?php


namespace JET_MSG\Api\Telegram\Methods;


use JET_MSG\Admin\Helpers\Curl_Helper;

abstract class Base_Method
{
    public $manager;
    public $params;
    public $response;
    public $found_message;
    public $method_name;
    public $dynamic_token_isset = true;

    public function __construct( $options = [], $token = false ) {
        $this->set_manager( $token );
        $this->params   = $options;
    }

    public function execute() {
        if ( ! $this->can_execute() ) return $this;

        $curl = new Curl_Helper( $this->manager->apiUrl( $this->method_name ) );

        $this->set_response( $curl->fields( $this->params )->execute() );
        return $this;
    }

    public function is_ok() {
        return ( $this->response && $this->response->ok );
    }

    /**
     * @param mixed $response
     */
    public function set_response( $response ) {
        $this->response = json_decode( $response );
    }

    public function set_manager( $token = false ) {
        $this->manager = jet_msg()->telegram_manager;

        if( $token === '' ) {
            $this->dynamic_token_isset = false;
        }
        else if ( $token !== false ) {
            $this->manager->bot_token = $token;
        }
    }

    public function can_execute() {
        return $this->dynamic_token_isset;
    }

    public function get_result( $index = 0 ) {
        if( is_array( $this->response->result ) && isset( $this->response->result[ $index ] ) )
            return $this->response->result[ $index ];

        return $this->response->result;
    }

    public function compare_type_message( $type, $index) {
        if ( isset( $this->get_result( $index )->message ) ){
            return ($this->get_result($index)->message->chat->type === $type);
        }
        return false;
    }

    public function is_private_message( $index = 0 ) {
        return $this->compare_type_message( 'private', $index );
    }

    public function get_chat_id( $index = 0 ) {
        if( $this->is_ok() ) {
            return (int) $this->get_result( $index )->chat->id;
        }
    }

    public function get_chat_id_obj( $result_obj ) {
        return $result_obj->message->chat->id;
    }

    public function compare_username( $username = '', $index = 0 ) {
        if ( ! isset( $this->get_result( $index )->message ) ) {
            return false;
        }

        $from_username = $this->get_result( $index )->message->from->username;
        $chat_username = $this->get_result( $index )->message->chat->username;

        if ( $from_username === $chat_username ) {
            return ( $username === $chat_username );
        }
    }

    public function compare_message( $text, $index = 0 ) {
        if ( ! isset( $this->get_result( $index )->message ) ) {
            return false;
        }

        return ( $this->get_result( $index )->message->text === $text );
    }

    public function find_message( $options ) {
        foreach ( $this->response->result as $index => $item ) {
            foreach ( $options as $function => $value ) {
                if ( ! is_callable( [ $this, $function ] ) ) continue;

                if ( ! $this->get_result( $index ) instanceof \stdClass ) continue;

                if ( ! $this->$function( $value, $index ) ) {
                    unset( $this->response->result[ $index ] );
                }
            }
        }

        return array_pop( $this->response->result );
    }

}