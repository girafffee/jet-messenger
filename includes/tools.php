<?php


namespace JET_MSG;


class Tools
{

    public static function make_class_name( $action ) {
        $action_class = explode( '-', $action );

        foreach ($action_class as $key => $value) {
            $action_class[ $key ] = ucfirst( $value );
        }

        return implode( '_', $action_class );
    }

}