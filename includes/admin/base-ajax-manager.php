<?php

namespace JET_MSG\Admin;

abstract class Base_Ajax_Manager {

    public $model;

    public $conditions;
    public $fields;
    public $errors = [];
    public $messages = [
        'insert' => [
            'success'   => 'Inserted!',
            'fail'      => 'Insert failed.'
        ],
        'update' => [
            'success'   => 'Updated!',
            'fail'      => 'Update failed.'
        ],
        'delete' => [
            'success'   => 'Removed!',
            'fail'      => 'Remove failed.'
        ]
    ];

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
                if ( $this->validate( $post_data[ $column ], $column ) ) {

                    $this->conditions[ $column ] = $post_data[ $column ];
                } else {
                    $this->errors[] = $column;
                }
            }
            else {
                if ( $this->validate( $post_data[ $column ], $column ) ) {

                    $this->fields[ $column ] = $post_data[ $column ];
                } else {
                    $this->errors[] = $column;
                }
            }

            if ( ! empty( $this->fields[ $column ] ) && is_callable( [ $this, 'on_update_'.$column ] ) ) {
                $func = 'on_update_'.$column;
                $this->$func();
            }
        }
    }

    public function set( $name_column, $value = null ) {
        if ( in_array( $name_column, array_keys( $this->model->schema() ) ) ) {
            $this->fields[ $name_column ] = $value;
        }
        return $this;
    }
    public function get( $name_column ) {
        if ( in_array( $name_column, array_keys( $this->model->schema() ) )
            && ! empty( $this->fields[ $name_column ] ) )
        {
            return $this->fields[ $name_column ];
        }
        return '';
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
        return true;
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

    public function ajax_delete_data() {
        if( ! isset( $_POST['data'] ) ) {
            wp_send_json_error();
            return;
        }

        $this->mode = $_POST['mode'];
        $this->parse_data( $_POST['data'] );

        if( $this->model->delete( $this->get_condition() ) ) {

            wp_send_json_success( array(
                'message'   => $this->messages[ 'delete' ][ 'success' ]
            ) );
            return;
        }

        wp_send_json_error( array(
            'message' => $this->messages[ 'delete' ][ 'fail' ],
        ));
    }

    public function ajax_save_data() {

        if( ! isset( $_POST['data'] ) ) {
            wp_send_json_error();
            return;
        }
        $this->mode = $_POST['mode'];
        $this->parse_data( $_POST['data'] );

        if ( $this->insert_with_ajax() ) {
            return;
        }

        if ( ! $this->update_with_ajax() ) {
            wp_send_json_error( array(
                'message' => __( 'Something was wrong...', 'jet-messenger' ),
            ));
        }
    }

    public function insert_with_ajax() {

        if( empty( $this->get_condition() ) &&
            ( $inserted_id = $this->model->insert( $this->fields ) ) ) {

            wp_send_json_success( array(
                'message'   => $this->messages[ 'insert' ][ 'success' ],
                'id'        => $inserted_id
            ) );
            return true;
        }
    }

    public function update_with_ajax() {
        $sql = $this->model->select('COUNT(*)')->where_equally( $this->get_condition() )->get_sql();

        if ( $this->model->wpdb()->get_var( $sql ) ) {

            $success = $this->model->update( $this->fields, $this->get_condition() );

            if ( $success ) {
                wp_send_json_success( array(
                    'message' => $this->messages[ 'update' ][ 'success' ],
                ) );
                return true;
            }
            else {
                wp_send_json_error( [
                    'message' => $this->messages[ 'update' ][ 'fail' ],
                ] );
                return false;
            }
        }
        return false;
    }

}