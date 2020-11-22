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
class Private_Notifications_Model extends Base_Model {

    use Notification_Trait;

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	public function table_name() {
		return 'private_notifications';
	}

    /*public function is_exists()
    {
        return (
            parent::is_exists()
            && $this->column_exists( 'do_action_on', false )
            && $this->column_exists( 'action_value', false )
            && $this->column_exists( 'conditions' )
        );
    }*/

	public function column__action() {
		return [
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
				'relation'	=> [ 'id', 'author_id', 'form_value' ]
			],
			'woo_place_order'			=> [
				'label'		=> __( 'Woo Place Order', 'jet-messenger' ),
				'relation'	=> [ 'id', 'author_id', 'taxonomy' ]
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
            'form_value'        => [
                'label'     => __( 'Form Value', 'jet-messenger' )
            ],
			'taxonomy'			=> [
				'label'		=> __( 'Taxonomy', 'jet-messenger' ),
 			],
			'post_type'			=> [
				'label'		=>  __( 'Post Type', 'jet-messenger' ),
			],
			'custom'			=> [
				'label'		=> __( 'Custom', 'jet-messenger' ),
			],
		];
	}

	

    /**
	 * Returns columns schema
	 * @return [type] [description]
	 */
	public function schema() {
		return [
			'id'                => 'bigint(20) NOT NULL AUTO_INCREMENT',
			'jet_msg_chat_id'	=> 'int(11) NOT NULL',
            'action'			=> "text NOT NULL",
            'conditions'		=> "text",
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
			'primary key' 	=> 'id'
        ];        
	}
	
	public function excluded_columns_for_insert() {
		return [ 'id', 'created_at', 'updated_at' ];
	}

    //SELECT pn.*, ch.* FROM `wp_jet_msg_private_notifications` pn
    //JOIN wp_jet_msg_chats ch ON pn.jet_msg_chat_id = ch.id AND ch.wp_user_id = 1
	public function select_all_for_current_user() {
        $sql = 'SELECT ';
        $sql .= $this->generate_select_values([
            'pn' => [ 'id', 'action', 'conditions', 'bot_id', 'message', 'jet_msg_chat_id' ],
            'ch' => [ 'wp_user_id', 'chat_id' ],
        ]);

        $table = $this->table() . ' as pn';
        $table_join = jet_msg()->chats->table() . ' as ch';
        $current_user_id = get_current_user_id();

        $sql .= " FROM ${table} JOIN ${table_join} ";
        $sql .= "ON pn.jet_msg_chat_id = ch.id AND ch.wp_user_id = ${current_user_id};";

        return $this->parse_notifications( $this->wpdb_results( $sql, ARRAY_A) );
    }

}


