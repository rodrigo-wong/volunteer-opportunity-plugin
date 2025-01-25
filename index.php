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

// Drop custom table on plugin deactivation
function plugin_deactivate() {
    global $wpdb;
    $table_name = 'volunteer_opportunities';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_deactivation_hook( __FILE__, 'plugin_deactivate' );

// Render the admin page
function wp_events_admin_page_html(){
    ?>
    <div class="wrap">
        <h2>Volunteer Opportunities</h2>
        <p>Here you can view and manage volunteer opportunities.</p>
    </div>
    <?php
}

// Add Volunteer menu in admin panel
function wp_events_admin() {
    add_menu_page('Volunteer Opportunities', 'Volunteer', 'manage_options', 'volunteer_opportunity', 'wp_events_admin_page_html', '', 20);
}
add_action('admin_menu', 'wp_events_admin');

