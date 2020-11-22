<?php

namespace JET_MSG;

class Factory
{
    private $namespace;
    private $delimiter_words;

    public function __construct( $namespace, $delimiter_words = '-' ) {
        $this->namespace = $namespace;
        $this->delimiter_words = $delimiter_words;
    }

    public function create_many( array $classes = array(), $params = array() ) {
        if ( empty( $classes ) ) {
            return $classes;
        }
        foreach ( $classes as $index => $name ) {
            $classes[ $index ] = $this->create_one( $name, $params );
        }

        return $classes;
    }

    public function create_one( $name, $params = array() ) {
        if ( empty( $this->namespace ) ) {
            return new \stdClass();
        }
        $class_name = $this->namespace . Tools::make_class_name( $name, $this->delimiter_words );

        return new $class_name( $params );
    }


}