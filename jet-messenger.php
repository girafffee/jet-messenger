<?php
/**
 * Plugin Name: JetMessenger
 * Plugin URI:  https://crocoblock.com/plugins/
 * Description: The best solution for message users
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: jet-messenger
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die();
}

add_action( 'plugins_loaded', 'jet_msg_init' );

function jet_msg_init() {
    define( 'JET_MSG__FILE__', __FILE__ );
    require __DIR__ . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'plugin.php';
}


function jet_msg() {
	return JET_MSG\Plugin::get_instance();
}




