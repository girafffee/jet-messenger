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
class General_Notifications_Model extends Base {

	public function after_create_table() {

	}

	public function get_by_bot_id( $id ) {
		$sql = $this->select()
				->where_equally( [ 'bot_id' => $id ] )
				->get_sql();

		return $this->wpdb()->get_results( $sql, ARRAY_A );	
	}

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	public function table_name() {
		return 'general_notifications';
	}

	public function column__action() {
		return [
			/**
			 * Turned it off because 
			 * I could not fix sending two messages 
			 * at once when the post update hook
			 */
			// 'update_post' 				=> [
			// 	'label'		=> __( 'Update Post', 'jet-messenger' ),
			// 	'relation'	=> [ 'id', 'author_id', 'taxonomy', 'post_type', 'relation_parent', 'relation_child' ]
			// ],
			'new_post' 					=> [
				'label'		=> __( 'New Post', 'jet-messenger' ),
				'relation'	=> [ 'author_id', 'taxonomy', 'post_type', 'relation_parent', 'relation_child' ]
			],
			'new_comment'				=> [
				'label'		=> __( 'New Comment', 'jet-messenger' ),
				'relation'	=> [ 'author_id', 'taxonomy', 'post_type', 'relation_parent', 'relation_child' ]
 			],
			'jet_engine_form_submit'	=> [
				'label'		=>  __( 'JetEngine Submit Form', 'jet-messenger' ),
				'relation'	=> [ 'id', 'author_id' ]
			],
			'woo_place_order'			=> [
				'label'		=> __( 'Woo Place Order', 'jet-messenger' ),
				'relation'	=> [ 'id', 'author_id' ]
			],
			'datetime'					=> [
				'label'		=> __( 'DateTime', 'jet-messenger' ),
				'relation'	=> [ 'custom' ]
			] 
		];
	}

	public function column__do_action_on() {
		return [
			'id' 				=> [
				'label'		=> __( 'ID', 'jet-messenger' ),
			],
			'author_id'			=> [
				'label'		=> __( 'Author', 'jet-messenger' ),
			],
			'taxonomy'			=> [
				'label'		=> __( 'Taxonomy', 'jet-messenger' ),
 			],
			'post_type'			=> [
				'label'		=>  __( 'Post Type', 'jet-messenger' ),
			],
			'relation_parent'	=> [
				'label'		=> __( 'Relation Parent', 'jet-messenger' ),
			],
			'relation_child'	=> [
				'label'		=> __( 'Relation Child', 'jet-messenger' ),
			],
			'custom'			=> [
				'label'		=> __( 'Custom', 'jet-messenger' ),
			],
		];
	}

	public function inline_headers_of_column( $column ) {
		$func = 'column__' . $column;
		if ( is_callable( [ $this, $func ] ) ) {

			return ("'" . implode( "','", array_keys( $this->$func() ) ) . "'");
		}

	}

    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
	public function schema() {
		return [
			'id'                => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'action'			=> "ENUM(". $this->inline_headers_of_column( 'action' ) .") NOT NULL DEFAULT 'update_post'",
			'do_action_on'		=> "ENUM(". $this->inline_headers_of_column( 'do_action_on' ) .") NOT NULL DEFAULT 'custom'",
			'action_value'		=> 'varchar(100)',
			'bot_id'			=> 'int(3)',
			'message'			=> 'text',
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
            'primary key' => 'id'
        ];        
	}
	
	public function excluded_columns_for_insert() {
		return [ 'id', 'created_at', 'updated_at' ];
	}
}
