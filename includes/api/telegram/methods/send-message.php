<?php

namespace JET_MSG\Api\Telegram\Methods;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Send_Message extends Base_Method
{
    public $method_name = 'sendMessage';

}