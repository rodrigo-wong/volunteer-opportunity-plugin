<?php
/*
Plugin Name: Volunteer Opportunity Plugin
Description: A plugin to list volunteer opportunities.
Version: 1.0
Author: Your Name
*/

// Create custom table on plugin activation
function plugin_activate() {
    global $wpdb;
    $table_name = 'volunteer_opportunities';

    $sql = "CREATE TABLE $table_name (
        id INT NOT NULL AUTO_INCREMENT,
        position VARCHAR(255) NOT NULL,
        organization VARCHAR(255) NOT NULL,
        type VARCHAR(50) NOT NULL,
        email VARCHAR(255) NOT NULL,
        description TEXT NOT NULL,
        location VARCHAR(255) NOT NULL,
        hours INT NOT NULL,
        skills_required TEXT NOT NULL,
        PRIMARY KEY (id)
    );";
    $wpdb->query($sql);
}
register_activation_hook( __FILE__, 'plugin_activate' );
