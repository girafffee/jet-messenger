<?php

namespace JET_MSG\DB;

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

    public function clear() {
        $this->select = '';
        $this->whereConditions = [];
        $this->selectColumns = [];
    }

}