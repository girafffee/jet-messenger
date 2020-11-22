<?php


namespace JET_MSG\Exceptions;


class Failed_Send_Exception extends Handler_Exception
{

    public function get_response()
    {
        error_log( '### Telegram Send Message Failed' );
        error_log( var_export( parent::get_response(), true ) );
    }

}