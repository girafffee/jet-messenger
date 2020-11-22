<?php


namespace JET_MSG\Conditions;


class Id extends Base_Condition
{

    public function check()
    {
        return $this->compare_value( $this->values );
    }
}