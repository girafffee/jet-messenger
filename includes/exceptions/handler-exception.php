<?php


namespace JET_MSG\Exceptions;


class Handler_Exception extends \Exception
{
    private $response;

    public function __construct( $response = '', $message = "" )
    {
        $this->response = $response;
        parent::__construct( 'default', 0, null);
    }

    public function get_response() {
        return $this->response;
    }

}