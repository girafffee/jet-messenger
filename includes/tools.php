<?php


namespace JET_MSG;


class Tools
{

    public static function make_class_name( $action ) {
        $delimiters = array( '_', '-' );
        $delimiter = '_';

        foreach ( $delimiters as $del ) {
            if ( false !== strpos( $action, $del ) ) {
                $delimiter = $del;
                break;
            }
        }

        $action_class = explode( $delimiter, $action );

        foreach ($action_class as $key => $value) {
            $action_class[ $key ] = ucfirst( $value );
        }

        return implode( '_', $action_class );
    }

}