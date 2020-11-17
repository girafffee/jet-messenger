<?php

namespace JET_MSG\Filters;

interface Filter_Interface
{
    /**
     * Should return the filtered value
     *
     * @param $value
     * @return mixed
     */
    public function filter( $value );

}