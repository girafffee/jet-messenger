<?php

namespace JET_MSG\Api\Telegram\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

abstract class Action_Post_Manager extends Base_Action {

    public $wp_action_name;

    public $is_send = false;


    public function set_dynamic_fields() {
        $dynamic_fields = explode( '%', $this->message );

        foreach ( $dynamic_fields as $index => $field ) {
            if ( isset( $this->current_post->$field ) && in_array( $field, $this->allowed_fields() ) ) {
                $dynamic_fields[ $index ] = $this->current_post->$field;

                if ( array_key_exists( $field, $this->custom_filter_fields() ) ) {
                
                    $dynamic_fields[ $index ] = $this->custom_filter_fields()[ $field ]( $dynamic_fields[ $index ] );
                }
            }   
        }
        $this->message = implode( '', $dynamic_fields );
    }

    public function allowed_fields() {
        return [
            'ID', 
            'post_author', 
            'post_date', 
            'post_title',
            'post_excerpt', 
            'post_modified',  
            'post_parent', 
            'guid'
        ];
    }

    public function custom_filter_fields() {
        return [
            'post_author'   => [ $this, 'get_post_author_name' ],
        ];
    }

    public function get_post_author_name( $id ) {
        $user       = get_userdata( $id );
        $username   = $user->user_login;
        $first_name = $user->first_name;
        $last_name  = $user->last_name;

        return "$first_name $last_name ($username)";
    }
}