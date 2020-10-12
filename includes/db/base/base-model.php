<?php

namespace JET_MSG\DB\Base;

/**
 * Database manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! class_exists( 'Jet_MSG_DB' ) ) {

abstract class Base_Model extends Simple_Query_Builder {

    /**
	 * Check if booking DB table already exists
	 *
	 * @var bool
	 */
	public $table_exists = null;

	/**
	 *
	 */
	public $defaults = array();


    protected $prefix = 'jet_msg_';

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	abstract protected function table_name();

    /**
     * You must you this function,
     * to get right name of table
     * @return string table name
     */
    public function table() {
        return $this->wpdb()->prefix . $this->prefix . $this->table_name();
    }
    
    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
    abstract public function schema();
    
    /**
	 * Returns schemas options
     * Such as primary keys, charset
	 * @return [type] [description]
	 */
	abstract public function schema_keys();
	

	/**
	 * Returns an array, where the values
	 * are the names of the columns, which
	 * cannot be inserted
	 * 
	 * @return array [description]
	 */
	abstract public function excluded_columns_for_insert();

    public function get_table_schema() {
        $schema_keys     	= $this->schema_keys();
		$table              = $this->table();
        $columns            = $this->schema();
        $charset_collate    = $this->schema_charset_collate();
        
        $ready_columns = '';
        foreach ( $columns as $column => $desc ) { 
            $ready_columns .= $column . ' ' . $desc . ", \n";
        }

        $keys = [];
        foreach ( $schema_keys as $key => $column_name ) {
            $keys[] = "$key ($column_name)";
        }
        $ready_keys = implode(", \n", $keys);

        $schema = "CREATE TABLE $table (
			$ready_columns
			$ready_keys
		) $charset_collate;";

		return $schema;
    }

    public function schema_charset_collate() {
        return $this->wpdb()->get_charset_collate();
    }
    

	/**
	 * Insert user option
	 *
	 * @param  array  $booking [description]
	 * @return [type]          [description]
	 */
	public function insert( $data = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		$inserted = $this->wpdb()->insert( $this->table(), $data );

		if ( $inserted ) {
			return $this->wpdb()->insert_id;
		} else {
			return false;
		}
	}

	/**
	 * Update user option info
	 *
	 * @param  array  $new_data [description]
	 * @param  array  $where    [description]
	 * @return [type]           [description]
	 */
	public function update( $new_data = array(), $where = array() ) {

		if ( ! empty( $this->defaults ) ) {
			foreach ( $this->defaults as $default_key => $default_value ) {
				if ( ! isset( $data[ $default_key ] ) ) {
					$data[ $default_key ] = $default_value;
				}
			}
		}

		return $this->wpdb()->update( $this->table(), $new_data, $where );
	}

	/**
	 * Delete column
	 * @return [type] [description]
	 */
	public function delete( $where = array() ) {
		return $this->wpdb()->delete( $this->table(), $where );
	}

	/**
	 * Check if booking table alredy exists
	 *
	 * @return boolean [description]
	 */
	public function is_table_exists() {

		if ( null !== $this->table_exists ) {
			return $this->table_exists;
		}

		$table = $this->table();

		if ( $table === $this->wpdb()->get_var( "SHOW TABLES LIKE '$table'" ) ) {
			$this->table_exists = true;
		} else {
			$this->table_exists = false;
		}
      
		return $this->table_exists;
	}

	/**
	 * Try to recreate DB table by request
	 *
	 * @return void
	 */
	public function install_table() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->create_table();
		$this->after_create_table();
    }
    
    /**
	 * Create DB table for apartment units
	 *
	 * @return [type] [description]
	 */
	public function create_table( $delete_if_exists = false ) {

		if ( ! function_exists( 'dbDelta' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		}

		if ( $delete_if_exists && $this->is_table_exists() ) {
			$table = $this->table();
			$this->wpdb()->query( "DROP TABLE $table;" );
		}

		$sql = $this->get_table_schema();

		dbDelta( $sql );
	}

	/**
	 * Returns WPDB instance
	 * @return [type] [description]
	 */
	public function wpdb() {
		global $wpdb;
		return $wpdb;
    }
    
    /**
	 * Insert new columns into existing bookings table
	 *
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function insert_table_columns( $columns = array() ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$table          = $this->table();
		$columns_schema = '';

		foreach ( $columns as $column ) {
			$columns_schema .= $column . ' text,';
		}

		$columns_schema = rtrim( $columns_schema, ',' );

		$sql = "ALTER TABLE $table
			ADD $columns_schema;";

		$this->wpdb()->query( $sql );

	}

	/**
	 * Check if booking DB column is exists
	 *
	 * @return [type] [description]
	 */
	public function column_exists( $column ) {

		$table = $this->table();
		return $this->wpdb()->query( "SHOW COLUMNS FROM `$table` LIKE '$column'" );

	}

	/**
	 * Delete columns into existing bookings table
	 *
	 * @param  [type] $columns [description]
	 * @return [type]          [description]
	 */
	public function delete_table_columns( $columns ) {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$table          = $this->table();
		$columns_schema = '';

		foreach ( $columns as $column ) {
			$columns_schema .= $column . ',';
		}

		$columns_schema = rtrim( $columns_schema, ',' );

		$sql = "ALTER TABLE $table
			DROP COLUMN $columns_schema;";

		$this->wpdb()->query( $sql );

	}

	/**
	 * Returns an array, where the keys 
	 * are the names of the columns, 
	 * which can be inserted in the current table
	 * 
	 * @return array [description]
	 */
	public function get_columns_schema( $include = [] ) {
		$columns = array_keys( $this->schema() );

		return array_flip( 
			array_diff( 
				$columns, array_diff( $this->excluded_columns_for_insert(), $include )
			) 
		);
	}

	public function get_columns_schema_all() {
		return array_flip( array_keys( $this->schema() ) );
	}

	public function after_create_table() {
		// you can change this action
		// in yor table model
	}

	public function select_all() {
		$sql = $this->select()->get_sql();

		return $this->wpdb()->get_results( $sql, ARRAY_A );
	}

	public function find_by() {
		return [ 'id' ];
	}

	
	public function inline_headers_of_column( $column ) {
		$func = 'column__' . $column;
		if ( is_callable( [ $this, $func ] ) ) {

			return ("'" . implode( "','", array_keys( $this->$func() ) ) . "'");
		}

	}

	public function wpdb_results( $sql, $method_result ) {
        return $this->wpdb()->get_results( $sql, $method_result );
    }

    public function get_empty_columns() {
	    $columns = $this->get_columns_schema();

        foreach ( $columns as $key => $option ) {
            $columns[ $key ] = '';
        }
        return $columns;
    }

}

}