<?php

namespace JET_MSG;

class Factory
{
    private $namespace;

    public function __construct( $namespace ) {
        $this->namespace = $namespace;
    }

    public function add( array $classes = array() ) {
        if ( empty( $classes ) ) {
            return $classes;
        }

        foreach ( $classes as $index => $name ) {
            $class_name = $this->namespace . Tools::make_class_name( $name );

            $classes[ $index ] = new $class_name();
        }

        return $classes;
    }


}