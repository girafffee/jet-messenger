<?php


namespace JET_MSG\Api\Telegram\Actions;

/**
 * Telegram manager
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

class Jet_Engine_Form_Submit extends Base_Action
{
    public $wp_action_name = 'jet-engine/forms/handler/after-send';
    public $count_args = 2;

    public $form_object;

    public function call_on_id( $jet_engine_form, $success ) {
        if ( ! $success ) return;

        $this->form_object = $jet_engine_form;

        if ( $this->form_object->form == $this->action_value )
        {
            $this->set_dynamic_fields( $this->form_object->notifcations->data );
            $this->send();
        }
    }

    public function call_on_author_id( $jet_engine_form, $success ) {
        if ( ! $success ) return;

        $this->form_object = $jet_engine_form;

        if ( get_current_user_id() == $this->action_value )
        {
            $this->set_dynamic_fields( $this->form_object->notifcations->data );
            $this->send();
        }
    }

    public function allowed_fields() {
        return array_keys( $this->form_object->notifcations->data );
    }
}