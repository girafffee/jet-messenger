<?php


namespace JET_MSG\Actions;


class Woo_Place_Order extends Base_Action
{
    public $wp_action_name = 'woocommerce_new_order';
    public $count_args = 1;
    public $order_base;
    public $checkout_data;
    public $products_data;
    public $cart;
    public $all_data;

    public function call_on_author_id( $order_id ) {
        $this->order_base = ( new \WC_Order( $order_id ) )->get_data();
        $this->set_data();

        if ( get_current_user_id() == $this->action_value ) {
            $this->set_dynamic_fields( $this->all_data );
            $this->send();
        }

    }

    public function call_on_taxonomy( $order_id ) {
        $this->set_data();

        if ( $this->is_cart_has_term() ) {
            $this->set_dynamic_fields( $this->all_data );
            $this->send();
        }

    }

    public function is_cart_has_term() {
        foreach ( $this->cart as $item ) {
            if ( has_term( $this->action_value, 'product_cat', $item[ 'data' ]->get_id() )
                ||
                has_term( $this->action_value, 'product_tag', $item[ 'data' ]->get_id() ) )
            {
                return true;
            }
        }
        return false;
    }


    public function set_data() {
        $this->set_checkout_data();
        $this->set_cart_data();

        $this->all_data = array_merge( $this->checkout_data, $this->products_data );
    }

    public function set_checkout_data() {
        $billing_data = $this->make_array_from( 'billing' );
        $shipping_data = $this->make_array_from( 'shipping' );

        $this->checkout_data = array_merge( $billing_data, $shipping_data );
    }

    public function set_cart_data() {
        $this->cart = WC()->cart->get_cart();

        $counter = 1;
        foreach ( $this->cart as $item ) {
            $this->set_product_data( $item[ 'data' ]->get_data(), $counter++ );
        }
    }

    public function set_product_data( $product_data, $index ) {
        foreach ( $product_data as $name => $value ) {
            $macros_name = 'prod-' . $index . '|' . $name;
            $this->products_data[ $macros_name ] = $value;
        }
    }

    public function action_allowed_fields() {
        return array_keys( $this->all_data );
    }

    public function make_array_from( $details_name ) {
        if ( ! isset( $this->order_base[ $details_name ] ) ) return [];

        $data = [];
        foreach ( $this->order_base[ $details_name ] as $name => $field ) {
            $macros_name = $details_name . '_' . $name;
            $data[ $macros_name ] = $field;
        }
        return $data;
    }


}