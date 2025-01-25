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

function wp_events_admin_page_html() {
    global $wpdb;
    $table_name = 'volunteer_opportunities';

    // Handle form submission for adding a new volunteer opportunity
    if (isset($_POST['submit'])) {
        $position = sanitize_text_field($_POST['position']);
        $organization = sanitize_text_field($_POST['organization']);
        $type = sanitize_text_field($_POST['type']);
        $email = sanitize_email($_POST['email']);
        $description = sanitize_textarea_field($_POST['description']);
        $location = sanitize_text_field($_POST['location']);
        $hours = intval($_POST['hours']);
        $skills_required = sanitize_text_field($_POST['skills_required']);

        // Insert new row into the database
        $wpdb->insert(
            $table_name,
            [
                'position' => $position,
                'organization' => $organization,
                'type' => $type,
                'email' => $email,
                'description' => $description,
                'location' => $location,
                'hours' => $hours,
                'skills_required' => $skills_required,
            ]
        );
    }

    // Display the admin form for adding opportunities
    ?>
    <div class="wrap">
        <h1>Volunteer Opportunities</h1>
        <form method="post">
            <label>Position: </label><input type="text" name="position" required><br>
            <label>Organization: </label><input type="text" name="organization" required><br>
            <label>Type: </label>
            <select name="type">
                <option value="one-time">One-Time</option>
                <option value="recurring">Recurring</option>
                <option value="seasonal">Seasonal</option>
            </select><br>
            <label>Email: </label><input type="email" name="email" required><br>
            <label>Description: </label><textarea name="description" required></textarea><br>
            <label>Location: </label><input type="text" name="location" required><br>
            <label>Hours: </label><input type="number" name="hours" required><br>
            <label>Skills Required: </label><input type="text" name="skills_required" required><br>
            <input type="submit" name="submit" value="Add Opportunity">
        </form>

        <?php
        // Display the list of volunteer opportunities
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>Position</th>
                    <th>Organization</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Hours</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><?php echo esc_html($row->position); ?></td>
                        <td><?php echo esc_html($row->organization); ?></td>
                        <td><?php echo esc_html($row->type); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td><?php echo esc_html($row->hours); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php
}

// Add Volunteer menu in admin panel
function wp_events_admin() {
    add_menu_page('Volunteer Opportunities', 'Volunteer', 'manage_options', 'volunteer_opportunity', 'wp_events_admin_page_html', '', 20);
}
add_action('admin_menu', 'wp_events_admin');



