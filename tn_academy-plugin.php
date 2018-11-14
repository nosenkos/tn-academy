<?php
/**
 * @package  TN Academy Plugin
 */
/*
Plugin Name: TN Academy Plugin
Plugin URI: http://travelnews.se
Description: TravelNews Academy Plugin
Version: 1.0.0
Author: Serhii Nosenko
Author URI: https://www.linkedin.com/in/sergey-nosenko-4b8a37127/
License: GPLv2 or later
Text Domain: tn_academy-plugin
*/

// If this file is called firectly, abort!!!
defined( 'ABSPATH' ) or die( 'Hey, what are you doing here? You silly human!' );

// Require once the Composer Autoload
if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

/**
 * The code that runs during plugin activation
 */
function activate_tn_academy_plugin() {
	Inc\Base\Activate::activate();
}
register_activation_hook( __FILE__, 'activate_tn_academy_plugin' );

/**
 * The code that runs during plugin deactivation
 */
function deactivate_tn_academy_plugin() {
	Inc\Base\Deactivate::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_tn_academy_plugin' );

/**
 * Initialize all the core classes of the plugin
 */
if ( class_exists( 'Inc\\Init' ) ) {
	Inc\Init::registerServices();
}