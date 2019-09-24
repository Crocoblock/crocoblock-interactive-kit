<?php
/**
 * Plugin Name: Crocoblock Interactive Kit
 * Plugin URI:  https://crocoblock.com/
 * Description: Popups library importer
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * Text Domain: crocoblock-interactive-kit
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

add_action( 'plugins_loaded', 'croco_ik_init' );

/**
 * Initializes plugin on plugins_loaded hook
 *
 * @return void
 */
function croco_ik_init() {

	define( 'CROCO_IK_VERSION', '1.0.0' );

	define( 'CROCO_IK__FILE__', __FILE__ );
	define( 'CROCO_IK_PLUGIN_BASE', plugin_basename( CROCO_IK__FILE__ ) );
	define( 'CROCO_IK_PATH', plugin_dir_path( CROCO_IK__FILE__ ) );
	define( 'CROCO_IK_URL', plugins_url( '/', CROCO_IK__FILE__ ) );

	require CROCO_IK_PATH . 'includes/plugin.php';

}

/**
 * Returns Plugin class instance
 *
 * @return Croco_IK\Plugin
 */
function croco_ik() {
	return Croco_IK\Plugin::instance();
}

register_activation_hook( __FILE__, 'croco_ik_activation' );

/**
 * Callback for plugin activation hook
 *
 * @return void
 */
function croco_ik_activation() {
	set_transient( 'croco_ik_redirect', true, MINUTE_IN_SECONDS );
}
