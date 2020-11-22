<?php


namespace JET_MSG\DB\Models;


trait Notification_Trait
{

    public function get_by_bot_id( $id ) {
        $sql = $this->select()
            ->where_equally( [ 'bot_id' => $id ] )
            ->get_sql();

        return $this->parse_notifications( $this->wpdb()->get_results( $sql, ARRAY_A ) );
    }

    public function get_operators_options() {
        return array(
            '='           => __( 'Equal', 'jet-messenger' ),
            '!='          => __( 'Not equal', 'jet-messenger' ),
            '>'           => __( 'Greater than', 'jet-messenger' ),
            '>='          => __( 'Greater or equal', 'jet-messenger' ),
            '<'           => __( 'Less than', 'jet-messenger' ),
            '<='          => __( 'Equal or less', 'jet-messenger' ),
            'LIKE'        => __( 'Like', 'jet-messenger' ),
            'NOT LIKE'    => __( 'Not like', 'jet-messenger' ),
            'IN'          => __( 'In', 'jet-messenger' ),
            'NOT IN'      => __( 'Not in', 'jet-messenger' ),
            /*'BETWEEN'     => __( 'Between', 'jet-messenger' ),
            'NOT BETWEEN' => __( 'Not between', 'jet-messenger' ),*/
        );
    }

    public function parse_notifications( $notifications ) {
        if ( empty( $notifications ) ) {
            return array();
        }

        foreach ( $notifications as $index => $notif ) {
            if ( empty( $notif['conditions'] ) ) {
                continue;
            }
            $notifications[ $index ]['conditions'] = json_decode( $notif['conditions'] );
        }

        return $notifications;
    }
}