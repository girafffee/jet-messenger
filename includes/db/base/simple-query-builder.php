<?php

namespace JET_MSG\DB\Base;

/**
 * MySql Query Builder
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Simple_Query_Builder {
    
    public $selectColumns;
    public $select;

    public $whereConditions = [];

    /**
     * 
     */
    public function select( $array_selected = '*' ) {
        $this->select = 'SELECT ';

        if( ! is_array($array_selected) ) {
            $this->select .= $array_selected . ' ';
            return $this;
        }

        $selected = array();
        foreach ($array_selected as $name => $item){
            $selected[] = $item . " as " . "'$name'";
        }
        $this->select .= implode(", ", $selected) . ' ';

        return $this;
    }

    /**
     * 
     */
    public function where_equally( $conditions ) {
      
        $this->where_base( $conditions );
        return $this;
    }

    /**
     * 
     */
    public function where_not_equally( $conditions ) {
        
        $this->where_base( $conditions, 'NOT ' );
        return $this;
    }

    public function where_base( $conditions, $prefix = '' ) {
        foreach ( $conditions as $column => $value ) {
            //if ( ! isset( $conditions[ $column ] ) ) continue;
            $this->whereConditions[] = $prefix . $column . "='" . $value ."' ";
        }
    }

    public function get_sql() {
        $sql = $this->select . ' FROM ' . $this->table();
        
        $where = '';
        if ( count( $this->whereConditions ) > 0 ) {
            $where = ' WHERE ' . implode( ' AND ', $this->whereConditions );
        }

        $sql .= $where . '; ';

        $this->clear();
        return $sql;
    }

    public function generate_select_values( $tables ) {
        $sql_fields = [];

        foreach ( $tables as $alias => $values ) {
            $alias = is_numeric( $alias ) ? $this->table() : $alias;

            if ( is_array( $values ) ) {
                foreach ( $values as $value ) {
                    $sql_fields[] = "$alias.$value";
                }
            }
            else {
                $sql_fields[] = "$alias.$values";
            }
        }

        return implode(', ', $sql_fields);
    }

    public function clear() {
        $this->select = '';
        $this->whereConditions = [];
        $this->selectColumns = [];
    }

}