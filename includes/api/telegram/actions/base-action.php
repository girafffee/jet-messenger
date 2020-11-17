<?php

namespace JET_MSG\Api\Telegram\Actions;

use JET_MSG\Api\Telegram\Methods\Send_Message;
use JET_MSG\Factory;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Action {

    public $do_action_on;
    public $action_value;
    public $message;
    public $jet_msg_chat_id;
    public $chat_id;
    public $wp_action_name;
    public $count_args = 3;

    public function __construct( $data ) {
        $this->parse( $data );
        $this->set_chat_id();

        $this->maybe_add_action();
    }

    public function maybe_add_action() {
        if ( $this->chat_id ) {
            add_action( $this->wp_action_name, [ $this, 'set_action' ] );
        }
    }

    protected function parse( $notification ) {
        $set_keys = [ 'do_action_on', 'action_value', 'message', 'jet_msg_chat_id' ];

        foreach ( $set_keys as $key ) {
            if ( isset( $notification[ $key ] ) ) {
                $this->$key = $notification[ $key ];
            }
        }
    }

    public function set_action() {
    
        $call_func = 'call_on_' . $this->do_action_on;
        if( is_callable( [ $this, $call_func ] ) ) {
            add_action( $this->wp_action_name, [ $this, $call_func ], 20, $this->count_args );
        }        
    }

    protected function send() {
        return ( new Send_Message( [
            'chat_id'       => $this->chat_id,
            'text'          => $this->message,
            'parse_mode'    => 'html'
        ] ) )->execute();
    }

    public function set_chat_id() {
        $this->chat_id = $this->get_chat_id();
    }

    public function get_chat_id() {
        if ( ! empty( $this->jet_msg_chat_id ) )
        {
            $chat = jet_msg()->chats->get_chat_by_id( $this->jet_msg_chat_id );

            if ( ! empty( $chat ) && ! empty( $chat[ 'chat_id' ] ) ) {
                return (int) $chat[ 'chat_id' ];
            }
            return false;
        }
        return jet_msg()->telegram_manager->channel_id;
    }

    public function set_dynamic_fields( $data_action ) {
        $dynamic_fields = explode( '%', $this->message );
        $factory = new Factory( 'JET_MSG\\Filters\\' );

        foreach ( $dynamic_fields as $index => $field ) {
            $parsed_field = explode( '|', $field );
            $field_name = $parsed_field[0];
            unset( $parsed_field[0] );

            $filters = $factory->add( $parsed_field );

            if ( $this->isset_value_array_or_object( $data_action, $field_name )
                && in_array( $field_name, $this->allowed_fields() ) )
            {
                $dynamic_fields[ $index ] = $this->get_value_array_or_object( $data_action, $field_name );

                if ( array_key_exists( $field_name, $this->custom_filter_fields() ) ) {

                    $dynamic_fields[ $index ] = $this->custom_filter_fields()[ $field_name ]( $dynamic_fields[ $index ] );
                }

                $dynamic_fields[ $index ] = $this->parse_filters( $filters, $dynamic_fields[ $index ] );
            }
        }
        $this->message = implode( '', $dynamic_fields );
    }

    public function parse_filters( array $filters, $value ) {
        if ( empty( $filters ) ) {
            return $value;
        }

        foreach ( $filters as $filter ) {
            $value = $filter->filter( $value );
        }

        return $value;
    }

    public function allowed_fields() {
        return [];
    }

    public function custom_filter_fields() {
        return [];
    }

    public function isset_value_array_or_object( $data, $field ) {
        if ( is_array( $data ) ) {
            return isset( $data[ $field ] );
        }
        return isset( $data->$field );
    }

    public function get_value_array_or_object( $data, $field ) {
        if ( is_array( $data ) && isset( $data[ $field ] ) ) {
            return $data[ $field ];
        }
        if ( isset( $data->$field ) ) {
            return $data->$field;
        }
    }

    public function get_author_name( $id ) {
        $user = get_userdata( $id );
        $name = [
            $user->first_name,
            $user->last_name,
            '('.$user->user_login.')'
        ];

        return implode( ' ', $name );
    }


}