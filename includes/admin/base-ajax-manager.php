<?php

namespace JET_MSG\Admin;

abstract class Base_Ajax_Manager {

    public $model;

    public $conditions;
    public $fields;
    public $errors;

    public $mode;
    const JET_MSG_INSERT_MODE = 'insert_mode';
    const JET_MSG_UPDATE_MODE = 'update_mode';

    public function parse_data( $post_data ) {
        /** */
        $columns_data = $this->get_empty_columns();

        foreach ( $columns_data as $column => $value ) {

            if ( ! isset( $post_data[ $column ] ) || empty( $post_data[ $column ] )
            ) {
                $func = $this->get_prefix . $column;
                
                if ( is_callable( [ $this, $func ] ) ) {
                    $this->fields[ $column ] = $this->$func();
                } 
                else {
                    $this->fields[ $column ] = $this->get_default( $column );
                }

            } 
            else if ( in_array( $column, $this->model->find_by() ) ) {
                $this->conditions[ $column ] = $this->validate( $post_data[ $column ], $column );
            }
            else {
                $this->fields[ $column ] = $this->validate( $post_data[ $column ], $column );
            }

            if ( ! empty( $this->fields[ $column ] ) && is_callable( [ $this, 'on_update_'.$column ] ) ) {
                [ $this, 'on_update_'.$column ]();
            }
        }
    }

    public function validate( $value, $column ) {
        
        if ( is_callable( [ $this->model, 'filter' ] ) ) {
            return $this->model->filter( $value, $column );
        }
        else {
            return $this->filter( $value, $column );
        }
    }

    public function filter( $value, $column ) {
        return $value;
    }

    public function get_default( $option = "" ) {
        if ( ! $option) {
            return;
        }

        if ( isset( $this->model->defaults[ $option ] ) ) {
            return $this->model->defaults[ $option ];
        }
    }

    public function get_empty_columns() {
        if ( $this->mode === self::JET_MSG_INSERT_MODE ) {
            $columns = $this->model->get_columns_schema();
        }
        else {
            $columns = $this->model->get_columns_schema( ['id'] );
        }
        
        foreach ( $columns as $key => $option ) {
            $columns[ $key ] = '';
        }
        return $columns;
    }

    public function get_condition( $post_data = [], $included = [] ) {
        /** */
        if ( ! empty( $this->conditions ) ) {
            return array_merge( $this->conditions, $included );
        }

        $keys = array_merge( $this->model->find_by(), $included );

        $finded_keys = array_intersect( array_keys( $post_data ), $keys );

        $conditions = [];
        foreach ( $finded_keys as $key ) {
            
            $conditions[ $key ] = $post_data[ $key ]; 
        }

        return $conditions;
    }
}