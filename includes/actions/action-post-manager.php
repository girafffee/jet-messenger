<?php

namespace JET_MSG\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Action_Post_Manager extends Base_Action {

    public $wp_action_name;

    public $is_send = false;


    public function action_allowed_fields() {
        return [
            'ID',
            'post_author',
            'post_date',
            'post_title',
            'post_excerpt',
            'post_modified',
            'post_parent',
            'guid'
        ];
    }

    public function custom_filter_fields() {
        return [
            'post_author'   => [ $this, 'get_author_name' ],
        ];
    }


}