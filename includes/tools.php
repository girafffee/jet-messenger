<?php


namespace JET_MSG;


class Tools
{

    public static function make_class_name( $action, $delimiter ) {
        $action_class = explode( $delimiter, $action );

        foreach ($action_class as $key => $value) {
            $action_class[ $key ] = ucfirst( $value );
        }

        return implode( '_', $action_class );
    }

}