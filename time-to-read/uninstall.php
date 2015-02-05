<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

// Main Plugin Class
require plugin_dir_path( __FILE__ ) . 'includes/class-plugin-name.php';

// Do our uninstall housekeeping
TimeToRead::uninstall();