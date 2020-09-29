<?php

namespace JET_MSG\Api\Telegram\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class New_Post extends Action_Post_Manager {

    public $wp_action_name = 'auto-draft_to_publish';

    public function call_on_author_id( $post ) {
        $this->current_post = $post;

        if ( $this->current_post->post_author != $this->action_value ) {
            return;
        }
        $this->set_dynamic_fields();
        $this->send( [ 'message' => $this->message ] );
    }

    public function call_on_taxonomy( $post ) {
        $this->current_post = $post;

        if ( ! is_object_in_taxonomy( $this->current_post, $this->action_value ) ) {
            return;
        }
        $this->set_dynamic_fields();
        $this->send( [ 'message' => $this->message ] );
    }


    public function call_on_post_type( $post ) {

        $this->current_post = $post;
        if ( $this->action_value != $this->current_post->post_type) {
            return;
        }        
        $this->set_dynamic_fields();
        $this->send( [ 'message' => $this->message ] );        
    }

    public function call_on_relation_parent( $post_id, $post = null, $update = null ) {
        // хранится в wp_options под ключем jet_engine_relations
    }
    
    public function call_on_relation_child( $post_id, $post = null, $update = null ) {
        // хранится в wp_options под ключем jet_engine_relations
    }


    
    
}