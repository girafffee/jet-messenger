<?php
namespace JET_MSG\DB;

/**
 * Database manager class
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Define DB class
 */
class Users_Notifications_Model extends Base {

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	public function table_name() {
		return 'users_notifications';
	}

    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
	public function schema() {
		return [
			'id'                => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'wp_user_id'		=> 'int(11) NOT NULL',
            'messenger_slug'    => 'varchar(100) NOT NULL',
            'messenger_user_id' => 'bigint(20) DEFAULT NULL',
            'status'            => "ENUM('enabled','disabled','pending') NOT NULL DEFAULT 'disabled'",
			'tracked_actions'   => 'text',
			'created_at'		=> 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at'		=> 'TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL'
        ];
    }
    
    /**
	 * Returns schemas options
     * Such as primary keys, charset
	 * @return [type] [description]
	 */
    public function schema_keys() {
        return [
            'primary key' => 'id',
            'index' => 'wp_user_id'
        ];        
	}
	
	public function excluded_columns_for_insert() {
		return [ 'id', 'created_at', 'updated_at' ];
	}
}
