<?php

namespace JET_MSG\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class New_Comment extends Base_Action {

    public $wp_action_name = 'comment_post';
    public $current_comment;

    public function call_on_author_id( $comment_id, $comment_approved, $commentdata ) {
        if ( $comment_approved === 'spam' ) return;

        if ( $commentdata[ 'user_id' ] == $this->action_value ) {
            $this->set_dynamic_fields( $commentdata );
            $this->send();
        }
    }

    public function call_on_taxonomy( $comment_id, $comment_approved, $commentdata ) {
        $post = get_post( $commentdata[ 'comment_post_ID' ] );

        if ( is_object_in_taxonomy( $post, $this->action_value ) ) {
            $this->set_dynamic_fields( $commentdata );
            $this->send();
        }
    }

    public function call_on_post_type( $comment_id, $comment_approved, $commentdata ) {
        $post = get_post( $commentdata[ 'comment_post_ID' ] );

        if ( $this->action_value == $post->post_type ) {
            $this->set_dynamic_fields( $commentdata );
            $this->send();
        }
    }

    public function action_allowed_fields() {
        return [
            'comment_post_ID',
            'comment_author',
            'comment_author_email',
            'comment_author_url',
            'comment_content',
            'user_id',
            'comment_author_IP',
            'comment_agent',
            'comment_date'
        ];
    }

    public function custom_filter_fields() {
        return [
            'user_id'   => [ $this, 'get_author_name' ],
        ];
    }

}