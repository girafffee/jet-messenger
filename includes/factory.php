<?php

namespace JET_MSG;

class Factory
{
    private $namespace;

    public function __construct( $namespace ) {
        $this->namespace = $namespace;
    }

    public function add( array $filters = array() ) {
        if ( empty( $filters ) ) {
            return $filters;
        }

        foreach ( $filters as $index => $filter ) {
            $class_name = $this->namespace . Tools::make_class_name( $filter );

            $filters[ $index ] = new $class_name();
        }

        return $filters;
    }


}