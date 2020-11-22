<?php

namespace JET_MSG\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Update_Post extends Action_Post_Manager {

    public $wp_action_name = 'save_post';

    // public function __construct( $data ) {
    //     $this->parse( $data );

    //     [ $this, 'set_action' ](); 
    // }

    public function call_on_id(  ) {

        
        //remove_action( $this->wp_action_name, [ $this, __FUNCTION__ ], -999 );
    }

    public function call_on_author_id() {

    }

    public function call_on_taxonomy() {

    }

    public function call_on_post_type() {

    }

    public function call_on_relation_parent() {

    }
    
    public function call_on_relation_child() {

    }

}