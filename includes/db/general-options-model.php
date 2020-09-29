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
class General_Options_Model extends Base {

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	public $columns;

	public $defaults = [
		'status' => 'enabled'
	];

	public function get_active_bots() {
		$sql = $this->select( 'id, bot_name' )
				->where_equally( [ 'status' => 'enabled' ] )
				->where_not_equally( [ 'bot_token' => '' ] )
				->get_sql();
		
		return $this->wpdb()->get_results( $sql, ARRAY_A );		
	}

	public function get_bot_by_slug( $slug ) {
		$sql = $this->select()
				->where_equally( [ 'status' => 'enabled', 'bot_slug' => $slug ] )
				->where_not_equally( [ 'bot_token' => '' ] )
				->get_sql();

		return $this->wpdb()->get_row( $sql, ARRAY_A );
	}

	public function after_create_table() {
		$after_create_insert = [
			['bot_slug' => 'telegram', 'bot_name' => 'Telegram Bot'],
			['bot_slug' => 'viber', 'bot_name' => 'Viber Bot'],
			['bot_slug' => 'whatsapp', 'bot_name' => 'Whatsapp Bot']
		];

		foreach($after_create_insert as $row) {
			$this->insert( $row );  
		}
	}

	
	public function table_name() {
		return 'general_options';
	}

    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
	public function schema() {
		return [
			'id'                	=> 'bigint(20) NOT NULL AUTO_INCREMENT',
			'bot_token'      		=> 'text',
            'bot_slug'    			=> 'varchar(100) NOT NULL',
			'bot_name'    			=> 'varchar(100) NOT NULL',
			'channel_id'			=> 'varchar(100)',
			'channel_name'			=> 'varchar(100)',
            'bot_creator_user_id' 	=> 'bigint(20) DEFAULT NULL',
			'status'            	=> "ENUM('enabled','disabled','pending') NOT NULL DEFAULT 'disabled'",
			'created_at'			=> 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at'			=> 'TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL'
        ];
    }
    
    
    public function schema_keys() {
        return [
			'primary key' 	=> 'id',
			'unique'		=> 'bot_slug'
        ];        
	}

	public function excluded_columns_for_insert() {
		return [ 'id', 'created_at', 'updated_at', 'bot_slug' ];
	}
	
}

	
