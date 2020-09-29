<?php

namespace JET_MSG\Api\Telegram\Actions;

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

    public function call_on_id( $post_ID, $post, $update ) {
        $this->current_post = $post;
     
        if ( wp_is_post_revision( $post_id ) 
        || $this->current_post->post_status != 'publish' 
        || $this->current_post->ID != $this->action_value  || ! $update) {
            return;
        }

        $this->set_dynamic_fields();
        $this->send( [ 'message' => $this->message ] );
        
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