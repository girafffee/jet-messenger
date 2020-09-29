<?php

namespace JET_MSG\Api\Telegram\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class New_Comment extends Base_Action {

    public $wp_action_name = 'comment_post';

}