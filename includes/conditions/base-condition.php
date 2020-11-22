<?php


namespace JET_MSG\Conditions;


abstract class Base_Condition
{
    protected $action_value;
    protected $operator;
    protected $values;

    abstract public function check();

    public function __construct( $data ) {
        $this->action_value = $data['condition']->action_value;
        $this->operator     = $data['condition']->operator;
        $this->values       = $data['values'];
    }

    protected function compare_value( $value_to_compare ) {
        switch ( $this->operator ) {
            case '=':
                return ( $value_to_compare == $this->action_value );
            case '!=':
                return ( $value_to_compare != $this->action_value );
            case '>':
                return ( $value_to_compare > $this->action_value );
            case '>=':
                return ( $value_to_compare >= $this->action_value );
            case '<':
                return ( $value_to_compare < $this->action_value );
            case '<=':
                return ( $value_to_compare <= $this->action_value );
            case 'LIKE':
                return ( false !== stripos( $value_to_compare, $this->action_value ) );
            case 'NOT LIKE':
                return ( false === stripos( $value_to_compare, $this->action_value ) );
            case 'IN':
                $values = array_map( 'trim',
                    explode( ',', $this->action_value )
                );
                return in_array( $value_to_compare, $values );
            case 'NOT IN':
                $values = array_map( 'trim',
                    explode( ',', $this->action_value )
                );
                return ( ! in_array( $value_to_compare, $values ) );
        }
    }

}