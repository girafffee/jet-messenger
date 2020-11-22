<?php


namespace JET_MSG\Actions;

use JET_MSG\Exceptions\Failed_Send_Exception;
use JET_MSG\Exceptions\Invalid_Condition_Exception;

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

    public function call_action( $jet_engine_form, $success ) {
        if ( ! $success ) return;

        $this->form_object = $jet_engine_form;

        try {
            $this->check_conditions( array(
                'id'            => $jet_engine_form->form,
                'author_id'     => get_current_user_id(),
                'form_value'    => $jet_engine_form->notifcations->data
            ) );

            $this->set_dynamic_fields( $this->form_object->notifcations->data );
            $this->send();
        }
        catch ( Failed_Send_Exception $exception ) {
            //$exception->get_response();
        }
        catch ( Invalid_Condition_Exception $exception ) {
            //var_dump( $exception->get_response() ); die;
        }

    }

    public function action_allowed_fields() {
        return array_keys( $this->form_object->notifcations->data );
    }
}