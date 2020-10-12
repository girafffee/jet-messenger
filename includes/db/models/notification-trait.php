<?php


namespace JET_MSG\DB\Models;


trait Notification_Trait
{
    public function get_by_bot_id( $id ) {
        $sql = $this->select()
            ->where_equally( [ 'bot_id' => $id ] )
            ->get_sql();

        return $this->wpdb()->get_results( $sql, ARRAY_A );
    }
}