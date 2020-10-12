<?php
namespace JET_MSG\DB\Models;

use JET_MSG\DB\Base\Base_Model;

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
class Chats_Model extends Base_Model {

    public $sync_code;
    public $id;

	public function table_name() {
		return 'chats';
    }
    
    public function column__chat_type() {
		return [
			'private' 				=> [
				'label'		=> __( 'User', 'jet-messenger' ),
			],
			'channel'			=> [
				'label'		=> __( 'Channel', 'jet-messenger' ),
			],
			'group'			=> [
				'label'		=> __( 'Group', 'jet-messenger' ),
 			],
		];
	}
	

    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
	public function schema() {
		return [
			'id'                	=> 'bigint(20) NOT NULL AUTO_INCREMENT',
			'wp_user_id'			=> 'bigint(20)',
			'chat_id'			    => 'varchar(100)',
            'chat_name'			    => 'varchar(100)',
			'chat_type'             => "ENUM(". $this->inline_headers_of_column( 'chat_type' ) .") NOT NULL DEFAULT 'channel'",
            'bot_slug'              => 'varchar(100) NOT NULL',
			'status'            	=> "ENUM('enabled','disabled','pending') NOT NULL DEFAULT 'disabled'",
            'sync_code'             => 'bigint(20)',
			'created_at'			=> 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at'			=> 'TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL'
        ];
    }

    public function generate_sync_code() {
	    return time();
    }

    public function get_chat_by_wp_user_id( $id ) {
	    $select_fields = array_diff( array_keys( $this->schema() ), $this->excluded_columns_for_update() );
	    $select_fields = implode( ', ', $select_fields );

        $sql = $this->select( $select_fields )
            ->where_equally( [ 'wp_user_id' => $id, 'chat_type' => 'private' ] )
            ->get_sql();

        return $this->wpdb()->get_row( $sql, ARRAY_A );
    }

    public function get_chat_by_id( $id ) {
        $this->id = $id;
        $where_condition = [ 'id' => $this->id ];

        $sql = $this->select()
            ->where_equally( $where_condition )
            ->get_sql();

        return $this->wpdb()->get_row( $sql, ARRAY_A );
    }

    public function get_sync_code( $chat_id ) {
	    $this->id = $chat_id;
	    $where_condition = [ 'id' => $this->id ];

        $sql = $this->select('sync_code' )
                    ->where_equally( $where_condition )
                    ->get_sql();

        if ( $sync_code = $this->wpdb()->get_var( $sql ) ) {
            return $sync_code;
        }

        $this->sync_code = $this->generate_sync_code();
        $success = $this->update( [ 'sync_code' => $this->sync_code, 'status' => 'pending' ], $where_condition );

        if ( ! $success ) return;

        $this->after_set_sync_code();

        return $this->sync_code;
    }

    public function after_set_sync_code() {
	    do_action( 'jet_msg/after_set_sync_code', $this->id, $this->sync_code );
    }

    public function is_synced( $chat_id ) {
        $chat = $this->get_chat_by_id( $chat_id );

        if ( ! isset( $chat['status'] ) ) {
            return false;
        }

        if ( $chat['status'] === 'enabled' && $chat['sync_code'] == '' ) {
            return $chat;
        }
    }
    
    
    public function schema_keys() {
        return [
			'primary key' 	=> 'id',
        ];        
	}

	public function excluded_columns_for_insert() {
		return [ 'id', 'created_at', 'updated_at' ];
	}

    public function excluded_columns_for_update() {
        return [ 'created_at', 'updated_at' ];
    }
	
}

	
