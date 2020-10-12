<?php

namespace JET_MSG\Api\Telegram\Methods;

use JET_MSG\Admin\Helpers\Curl_Helper;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Get_Updates extends Base_Method
{
    public $method_name = 'getUpdates';

    public function execute() {
        $curl = new Curl_Helper( $this->manager->apiUrl( $this->method_name ) );

        $this->set_response( $curl->fields( $this->params )->execute() );
        return $this;
    }


}