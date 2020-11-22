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
class General_Notifications_Model extends Base_Model {

    use Notification_Trait;

    /**
	 * Returns table name
	 * @return [type] [description]
	 */
	public function table_name() {
		return 'general_notifications';
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
			'action'			=> "text NOT NULL",
			'conditions'		=> "text",
			'bot_id'			=> 'int(3)',
			'message'			=> 'text',
			'created_at'		=> 'TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP',
			'updated_at'		=> 'TIMESTAMP on update CURRENT_TIMESTAMP NOT NULL'
        ];
    }

    public function select_all()
    {
        return $this->parse_notifications( parent::select_all() );
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
