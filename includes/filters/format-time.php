<?php

namespace JET_MSG\Filters;

class Format_Time extends Base_Filter
{
    protected function default_params()
    {
        return array(
            'format' => 'Y-m-d H:i:s'
        );
    }

    public function filter( $value = '' )
    {
        $timestamp = absint( $value );

        if ( ! $timestamp ) {
            return $value;
        }

        return date( $this->params[ 'format' ], $timestamp );
    }

}