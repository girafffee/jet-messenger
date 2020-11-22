<?php


namespace JET_MSG\Filters;


class Do_Shortcode extends Base_Filter
{

    protected function default_params()
    {
        return array(
            'code'     => '',
        );
    }

    /**
     * @inheritDoc
     */
    public function filter( $value = '' )
    {
        add_shortcode( 'bartag', function ( $atts ){
            // белый список параметров и значения по умолчанию
            $atts = shortcode_atts( array(
                'foo' => 'no foo',
                'baz' => 'default baz'
            ), $atts );

            return "foo = {$atts['foo']}";
        } );

        return do_shortcode( str_replace( '*', '"', $this->params[ 'code' ] ) );
    }
}