<?php

namespace JET_MSG\Filters;

abstract class Base_Filter
{
    protected $params = array();

    public function __construct( $params = array() ) {
        $this->set_params( $params );
    }

    private function set_params( $params ) {
        $this->params = array_merge( $this->default_params(), $params );
    }

    abstract protected function default_params();

    /**
     * Should return the filtered value
     *
     * @param $value
     * @return mixed
     */
    abstract public function filter( $value = '' );

}