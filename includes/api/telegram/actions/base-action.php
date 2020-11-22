<?php

namespace JET_MSG\Api\Telegram\Actions;

use JET_MSG\Api\Telegram\Methods\Send_Message;
use JET_MSG\Conditions\Base_Condition;
use JET_MSG\Exceptions\Failed_Send_Exception;
use JET_MSG\Exceptions\Handler_Exception;
use JET_MSG\Exceptions\Invalid_Condition;
use JET_MSG\Exceptions\Invalid_Condition_Exception;
use JET_MSG\Factory;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Action {

    public $message;
    public $jet_msg_chat_id;
    public $chat_id;
    public $wp_action_name;
    public $conditions;
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
        $set_keys = [ 'conditions', 'message', 'jet_msg_chat_id' ];

        foreach ( $set_keys as $key ) {
            if ( isset( $notification[ $key ] ) ) {
                $this->$key = $notification[ $key ];
            }
        }
    }

    public function set_action() {
        if( ! is_callable( [ $this, 'call_action' ] ) ) {
            return;
        }
        add_action($this->wp_action_name, [ $this, 'call_action' ], 20, $this->count_args);
    }

    private function prev_check_conditions() {
        if ( empty( $this->conditions ) || ! is_array( $this->conditions ) ) {
            throw new Invalid_Condition_Exception( 'Empty conditions' );
        }
    }


    protected function check_conditions( $values ) {
        $this->prev_check_conditions();

        $factory = new Factory( 'JET_MSG\\Conditions\\', '_' );

        foreach ( $this->conditions as $condition ) {
            /**
             * If value for this action type does not set
             * in `call_action`
             */
            if ( ! isset( $values[ $condition->action_type ] ) ) {
                throw new Invalid_Condition_Exception();
            }
            $value = $values[ $condition->action_type ];

            $checker = $factory->create_one( $condition->action_type, array(
                'condition' => $condition,
                'values'    => $value
            ) );

            if ( ! $checker instanceof Base_Condition || ! $checker->check() ) {
                throw new Invalid_Condition_Exception( 'Failed check' );
            }

        }

    }

    protected function send() {
        $method = ( new Send_Message( [
            'chat_id'       => $this->chat_id,
            'text'          => $this->message,
            'parse_mode'    => 'html'
        ] ) )->execute();

        if ( $method->response->ok !== 1 ) {
            throw new Failed_Send_Exception( $method->response );
        }
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

            $filters = $factory->create_many( $parsed_field );

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
        $this->message = strip_tags( implode( '', $dynamic_fields ) );
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