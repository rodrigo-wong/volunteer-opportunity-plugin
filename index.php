<?php
/*
Plugin Name: Volunteer Opportunity Plugin
Description: A plugin to list volunteer opportunities.
Version: 1.0
Author: Your Name
*/

// Create custom table on plugin activation
function plugin_activate()
{
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
register_activation_hook(__FILE__, 'plugin_activate');

// Drop custom table on plugin deactivation
function plugin_deactivate()
{
    global $wpdb;
    $table_name = 'volunteer_opportunities';
    $sql = "DROP TABLE IF EXISTS $table_name";
    $wpdb->query($sql);
}
register_deactivation_hook(__FILE__, 'plugin_deactivate');

// Render the admin page
function wp_events_admin_page_html()
{
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

        // Insert new opportunity
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
    // Handle deletion of a volunteer opportunity
    if (isset($_GET['delete']) && isset($_GET['id'])) {
        $wpdb->delete($table_name, ['id' => intval($_GET['id'])]);
    }

    // Handle updating a volunteer opportunity
    if (isset($_GET['edit']) && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        $row = $wpdb->get_row("SELECT * FROM $table_name WHERE id = $id");

        if (isset($_POST['update'])) {
            $position = sanitize_text_field($_POST['position']);
            $organization = sanitize_text_field($_POST['organization']);
            $type = sanitize_text_field($_POST['type']);
            $email = sanitize_email($_POST['email']);
            $description = sanitize_textarea_field($_POST['description']);
            $location = sanitize_text_field($_POST['location']);
            $hours = intval($_POST['hours']);
            $skills_required = sanitize_text_field($_POST['skills_required']);

            $wpdb->update(
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
                ],
                ['id' => $id]
            );

            // Clear $row to reset the form
            unset($row);
        }
    }


    // Display the admin form for adding/updating opportunities
?>
    <div class="wrap">
        <h1>Volunteer Opportunities</h1>
        <form method="post">
            <label>Position: </label><input type="text" name="position" value="<?php echo isset($row) ? esc_html($row->position) : ''; ?>" required><br>
            <label>Organization: </label><input type="text" name="organization" value="<?php echo isset($row) ? esc_html($row->organization) : ''; ?>" required><br>
            <label>Type: </label>
            <select name="type">
                <option value="one-time" <?php echo (isset($row) && $row->type == 'one-time') ? 'selected' : ''; ?>>One-Time</option>
                <option value="recurring" <?php echo (isset($row) && $row->type == 'recurring') ? 'selected' : ''; ?>>Recurring</option>
                <option value="seasonal" <?php echo (isset($row) && $row->type == 'seasonal') ? 'selected' : ''; ?>>Seasonal</option>
            </select><br>
            <label>Email: </label><input type="email" name="email" value="<?php echo isset($row) ? esc_html($row->email) : ''; ?>" required><br>
            <label>Description: </label><textarea name="description" required><?php echo isset($row) ? esc_html($row->description) : ''; ?></textarea><br>
            <label>Location: </label><input type="text" name="location" value="<?php echo isset($row) ? esc_html($row->location) : ''; ?>" required><br>
            <label>Hours: </label><input type="number" name="hours" value="<?php echo isset($row) ? esc_html($row->hours) : ''; ?>" required><br>
            <label>Skills Required: </label><input type="text" name="skills_required" value="<?php echo isset($row) ? esc_html($row->skills_required) : ''; ?>" required><br>
            <?php if (isset($row)) { ?>
                <input type="submit" name="update" value="Update Opportunity">
            <?php } else { ?>
                <input type="submit" name="submit" value="Add Opportunity">
            <?php } ?>
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
                    <th>Actions</th>
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
                        <td>
                            <a href="?page=volunteer_opportunity&edit=true&id=<?php echo $row->id; ?>">Edit</a> |
                            <a href="?page=volunteer_opportunity&delete=true&id=<?php echo $row->id; ?>" onclick="return confirm('Are you sure you want to delete this opportunity?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
}
// Add Volunteer menu in admin panel
function wp_events_admin()
{
    add_menu_page('Volunteer Opportunities', 'Volunteer', 'manage_options', 'volunteer_opportunity', 'wp_events_admin_page_html', '', 20);
}
add_action('admin_menu', 'wp_events_admin');
