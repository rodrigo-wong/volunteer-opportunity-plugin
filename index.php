<?php
/*
Plugin Name: Volunteer Opportunity Plugin
Description: A plugin to list volunteer opportunities.
Version: 1.0
Author: Rodrigo Wong Mac #00087648
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
        // Set the $row value to trigger editing mode in the form
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
    <style>
        .volunteer-form {
            width: 50%;
            margin: 20px 0;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            width: 100%;
        }

        .form-group label {
            width: 20%;
            font-weight: bold;
            text-align: left;
            margin-right: 10px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 80%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .volunteer-form input[type="submit"] {
            width: 100%;
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 5px 0;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            text-align: center;
        }

        .volunteer-form input[type="submit"]:hover {
            background-color: #0056b3;
        }
    </style>

    <div class="wrap">
        <h1>Volunteer Opportunities</h1>

        <form class="volunteer-form" method="post">
            <div class="form-group">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" value="<?php echo isset($row) ? esc_html($row->position) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="organization">Organization:</label>
                <input type="text" id="organization" name="organization" value="<?php echo isset($row) ? esc_html($row->organization) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="type">Type:</label>
                <select id="type" name="type">
                    <option value="one-time" <?php echo (isset($row) && $row->type == 'one-time') ? 'selected' : ''; ?>>One-Time</option>
                    <option value="recurring" <?php echo (isset($row) && $row->type == 'recurring') ? 'selected' : ''; ?>>Recurring</option>
                    <option value="seasonal" <?php echo (isset($row) && $row->type == 'seasonal') ? 'selected' : ''; ?>>Seasonal</option>
                </select>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo isset($row) ? esc_html($row->email) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required><?php echo isset($row) ? esc_html($row->description) : ''; ?></textarea>
            </div>

            <div class="form-group">
                <label for="location">Location:</label>
                <input type="text" id="location" name="location" value="<?php echo isset($row) ? esc_html($row->location) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="hours">Hours:</label>
                <input type="number" id="hours" name="hours" value="<?php echo isset($row) ? esc_html($row->hours) : ''; ?>" required>
            </div>

            <div class="form-group">
                <label for="skills_required">Skills Required:</label>
                <input type="text" id="skills_required" name="skills_required" value="<?php echo isset($row) ? esc_html($row->skills_required) : ''; ?>" required>
            </div>

            <div>
                <?php if (isset($row)) { ?>
                    <input type="submit" name="update" value="Update Opportunity">
                <?php } else { ?>
                    <input type="submit" name="submit" value="Add Opportunity">
                <?php } ?>
            </div>
        </form>
        <?php
        // Display the list of volunteer opportunities
        $results = $wpdb->get_results("SELECT * FROM $table_name");
        ?>
        <table class="widefat striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Position</th>
                    <th>Organization</th>
                    <th>Type</th>
                    <th>Email</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Hours</th>
                    <th>Skills Required</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row) { ?>
                    <tr>
                        <td><?php echo esc_html($row->id); ?></td>
                        <td><?php echo esc_html($row->position); ?></td>
                        <td><?php echo esc_html($row->organization); ?></td>
                        <td><?php echo esc_html($row->type); ?></td>
                        <td><?php echo esc_html($row->email); ?></td>
                        <td><?php echo esc_html($row->description); ?></td>
                        <td><?php echo esc_html($row->location); ?></td>
                        <td><?php echo esc_html($row->hours); ?></td>
                        <td><?php echo esc_html($row->skills_required); ?></td>
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

function display_volunteer_opportunities($atts = [], $content = null)
{
    global $wpdb;
    $table_name = 'volunteer_opportunities';

    // Extract shortcode parameters
    $atts = shortcode_atts(
        [
            'hours' => null,
            'type' => null,
        ],
        $atts,
        'volunteer'
    );

    $conditions = [];
    if (!is_null($atts['hours'])) {
        $conditions[] = $wpdb->prepare('hours < %d', intval($atts['hours']));
    }
    if (!is_null($atts['type'])) {
        $conditions[] = $wpdb->prepare('type = %s', sanitize_text_field($atts['type']));
    }

    // Build SQL query with conditions
    $where_clause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
    $results = $wpdb->get_results("SELECT * FROM $table_name $where_clause");

    // Add custom styles for the table
    $output = '<style>
        .volunteer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .volunteer-table th,
        .volunteer-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .volunteer-table th {
            background-color: #f4f4f4;
            font-weight: bold;
        }
        .volunteer-table tr.green {
            background-color: #eaffea;
        }
        .volunteer-table tr.yellow {
            background-color: #fffbe0;
        }
        .volunteer-table tr.red {
            background-color: #ffe0e0;
        }
        .volunteer-table td strong {
            font-style: italic;
        }
    </style>';

    // Generate the table HTML
    $output .= '<table class="volunteer-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Position</th>
                <th>Organization</th>
                <th>Type</th>
                <th>Email</th>
                <th>Description</th>
                <th>Location</th>
                <th>Hours</th>
                <th>Skills Required</th>
            </tr>
        </thead>
        <tbody>';

    foreach ($results as $row) {
        // Determine row background color based on hours
        $row_class = '';
        if ($row->hours < 10) {
            $row_class = 'green';
        } elseif ($row->hours <= 100) {
            $row_class = 'yellow';
        } else {
            $row_class = 'red';
        }


        // Append rows to the output
        $output .= '<tr class="' . esc_attr($row_class) . '">';
        $output .= '<td>' . esc_html($row->id) . '</td>';
        $output .= '<td><strong>' . esc_html($row->position) . '</strong></td>';
        $output .= '<td>' . esc_html($row->organization) . '</td>';
        $output .= '<td>' . esc_html($row->type) . '</td>';
        $output .= '<td>' . esc_html($row->email) . '</td>';
        $output .= '<td>' . esc_html($row->description) . '</td>';
        $output .= '<td>' . esc_html($row->location) . '</td>';
        $output .= '<td>' . esc_html($row->hours) . '</td>';
        $output .= '<td>' . esc_html($row->skills_required) . '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table>';

    // Return the final HTML output
    return $output;
}
add_shortcode('volunteer', 'display_volunteer_opportunities');
