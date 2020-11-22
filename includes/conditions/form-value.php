<?php


namespace JET_MSG\Conditions;


class Form_Value extends Base_Condition
{
    private $field_name;

    public function __construct( $data )
    {
        parent::__construct( $data );

        $this->field_name = $data['condition']->field_name;
    }

    public function check()
    {
        if ( ! isset( $this->values[ $this->field_name ] ) ) {
            return false;
        }
        $field_value = $this->values[ $this->field_name ];

        $check_array = false;
        if ( is_array( $field_value ) ) {
            foreach ( $field_value as $value ) {
                if ( $this->compare_value( $value ) ) {
                    $check_array = true;
                }
            }
            return $check_array;
        }
        return $this->compare_value( $field_value );
    }

}