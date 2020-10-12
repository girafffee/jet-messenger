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
        if ( $post->post_author != $this->action_value ) {
            return;
        }
        $this->set_dynamic_fields( $post );
        $this->send();
    }

    public function call_on_taxonomy( $post ) {
        if ( ! is_object_in_taxonomy( $post, $this->action_value ) ) {
            return;
        }
        $this->set_dynamic_fields( $post );
        $this->send();
    }


    public function call_on_post_type( $post ) {
        if ( $this->action_value != $post->post_type ) {
            return;
        }        
        $this->set_dynamic_fields( $post );
        $this->send();
    }

    public function call_on_relation_parent( $post_id, $post = null, $update = null ) {
        // хранится в wp_options под ключем jet_engine_relations
    }
    
    public function call_on_relation_child( $post_id, $post = null, $update = null ) {
        // хранится в wp_options под ключем jet_engine_relations
    }


    
    
}