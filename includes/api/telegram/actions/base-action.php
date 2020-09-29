<?php

namespace JET_MSG\Api\Telegram\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Base_Action {

    public function __construct( $data ) {
        $this->parse( $data );

        add_action( $this->wp_action_name, [ $this, 'set_action' ] ); 
    }

    protected function parse( $notification ) {
        [
            'do_action_on'  => $this->do_action_on,
            'action_value'  => $this->action_value,
            'message'       => $this->message
        ] = $notification;
    }

    public function set_action() {
    
        $call_func = 'call_on_' . $this->do_action_on;
        if( is_callable( [ $this, $call_func ] ) ) {
            add_action( $this->wp_action_name, [ $this, $call_func ], 20, 3 );
        }        
    }

    protected function send( $options ) {
        jet_msg()->telegram_manager->send_message( $options );
    }
}