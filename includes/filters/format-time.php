<?php

namespace JET_MSG\Filters;

class Format_Time implements Filter_Interface
{
    public function filter( $value )
    {
        $timestamp = absint( $value );

        if ( ! $timestamp ) {
            return $value;
        }

        return date( 'Y-m-d H:i:s', $timestamp );
    }

}