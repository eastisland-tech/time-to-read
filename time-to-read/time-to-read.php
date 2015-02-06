<?php
/**
 * Plugin Name:  Time To Read
 * Plugin URI:   https://github.com/eastisland-tech/time-to-read
 * Description:  A simple plugin that provides an estimated time to read for each post.
 * Version:      1.0.0
 * Author:       Colin Morgan
 * Author URI:   http://eastisland-tech.com
 * License:      MIT
*/

defined( 'ABSPATH' ) or die( 'Nope!' );

// Main Plugin Class
require plugin_dir_path( __FILE__ ) . 'includes/class-time-to-read.php';


/**
* Calls the plugin's static activate method.
* This function is destined to be hooked into WordPress.
*
*/
function activate_time_to_read() {
	Time_To_Read::activate();
}

/**
* Calls the plugin's static deactivate method.
* This function is destined to be hooked into WordPress.
*
*/
function deactivate_time_to_read() {
	Time_To_Read::deactivate();
}

register_activation_hook( __FILE__, 'activate_time_to_read' );
register_deactivation_hook( __FILE__, 'deactivate_time_to_read' );

/**
* This actually runs the plugin.
*
*/
function run_time_to_read() {
	$plugin = new Time_To_Read();
	$plugin->run();
}

// Here we go
run_time_to_read();