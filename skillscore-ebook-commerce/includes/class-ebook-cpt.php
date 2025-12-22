<?php
/**
 * Custom Post Type for Ebooks
 *
 * @package SkillScore_Ebook
 */

if (!defined('ABSPATH')) {
    exit;
}

class SkillScore_Ebook_CPT {

    /**
     * Register the ebook custom post type.
     */
    public function register_post_type() {
        $labels = array(
            'name' => __('Ebooks', 'skillscore-ebook'),
            'singular_name' => __('Ebook', 'skillscore-ebook'),
            'menu_name' => __('Ebooks', 'skillscore-ebook'),
            'add_new' => __('Add New', 'skillscore-ebook'),
            'add_new_item' => __('Add New Ebook', 'skillscore-ebook'),
            'edit_item' => __('Edit Ebook', 'skillscore-ebook'),
            'new_item' => __('New Ebook', 'skillscore-ebook'),
            'view_item' => __('View Ebook', 'skillscore-ebook'),
            'search_items' => __('Search Ebooks', 'skillscore-ebook'),
            'not_found' => __('No ebooks found', 'skillscore-ebook'),
            'not_found_in_trash' => __('No ebooks found in trash', 'skillscore-ebook'),
        );

        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'ebook'),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-book',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'author'),
            'show_in_rest' => true,
        );

        register_post_type('ebook', $args);

        // Register taxonomy for ebook categories
        $category_labels = array(
            'name' => __('Ebook Categories', 'skillscore-ebook'),
            'singular_name' => __('Ebook Category', 'skillscore-ebook'),
        );

        register_taxonomy('ebook_category', 'ebook', array(
            'labels' => $category_labels,
            'hierarchical' => true,
            'show_ui' => true,
            'show_in_rest' => true,
            'rewrite' => array('slug' => 'ebook-category'),
        ));
    }

    /**
     * Add meta boxes.
     */
    public function add_meta_boxes() {
        add_meta_box(
            'ebook_details',
            __('Ebook Details', 'skillscore-ebook'),
            array($this, 'render_details_meta_box'),
            'ebook',
            'normal',
            'high'
        );

        add_meta_box(
            'ebook_file',
            __('Ebook File', 'skillscore-ebook'),
            array($this, 'render_file_meta_box'),
            'ebook',
            'normal',
            'high'
        );

        add_meta_box(
            'ebook_preview_settings',
            __('Preview Settings', 'skillscore-ebook'),
            array($this, 'render_preview_meta_box'),
            'ebook',
            'side',
            'default'
        );

        add_meta_box(
            'ebook_sales_info',
            __('Sales Information', 'skillscore-ebook'),
            array($this, 'render_sales_meta_box'),
            'ebook',
            'side',
            'default'
        );
    }

    /**
     * Render ebook details meta box.
     */
    public function render_details_meta_box($post) {
        wp_nonce_field('ebook_details_nonce', 'ebook_details_nonce_field');

        $price = get_post_meta($post->ID, '_ebook_price', true);
        $quantity = get_post_meta($post->ID, '_ebook_quantity', true);
        $unlimited = get_post_meta($post->ID, '_ebook_unlimited', true);
        $isbn = get_post_meta($post->ID, '_ebook_isbn', true);
        $author = get_post_meta($post->ID, '_ebook_author', true);
        $publisher = get_post_meta($post->ID, '_ebook_publisher', true);
        $pages = get_post_meta($post->ID, '_ebook_pages', true);
        $language = get_post_meta($post->ID, '_ebook_language', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="ebook_price"><?php _e('Price', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="number" step="0.01" id="ebook_price" name="ebook_price"
                           value="<?php echo esc_attr($price); ?>" class="regular-text" required />
                    <p class="description"><?php _e('Enter the price for this ebook.', 'skillscore-ebook'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="ebook_unlimited"><?php _e('Stock', 'skillscore-ebook'); ?></label></th>
                <td>
                    <label>
                        <input type="checkbox" id="ebook_unlimited" name="ebook_unlimited"
                               value="1" <?php checked($unlimited, '1'); ?> />
                        <?php _e('Unlimited Stock', 'skillscore-ebook'); ?>
                    </label>
                </td>
            </tr>
            <tr id="quantity_row" style="<?php echo $unlimited ? 'display:none;' : ''; ?>">
                <th><label for="ebook_quantity"><?php _e('Quantity', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="number" id="ebook_quantity" name="ebook_quantity"
                           value="<?php echo esc_attr($quantity); ?>" class="regular-text" min="0" />
                    <p class="description"><?php _e('Available copies for sale.', 'skillscore-ebook'); ?></p>
                </td>
            </tr>
            <tr>
                <th><label for="ebook_author"><?php _e('Author', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="text" id="ebook_author" name="ebook_author"
                           value="<?php echo esc_attr($author); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="ebook_publisher"><?php _e('Publisher', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="text" id="ebook_publisher" name="ebook_publisher"
                           value="<?php echo esc_attr($publisher); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="ebook_isbn"><?php _e('ISBN', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="text" id="ebook_isbn" name="ebook_isbn"
                           value="<?php echo esc_attr($isbn); ?>" class="regular-text" />
                </td>
            </tr>
            <tr>
                <th><label for="ebook_pages"><?php _e('Pages', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="number" id="ebook_pages" name="ebook_pages"
                           value="<?php echo esc_attr($pages); ?>" class="small-text" min="1" />
                </td>
            </tr>
            <tr>
                <th><label for="ebook_language"><?php _e('Language', 'skillscore-ebook'); ?></label></th>
                <td>
                    <input type="text" id="ebook_language" name="ebook_language"
                           value="<?php echo esc_attr($language); ?>" class="regular-text"
                           placeholder="English" />
                </td>
            </tr>
        </table>
        <script>
        jQuery(document).ready(function($) {
            $('#ebook_unlimited').change(function() {
                if($(this).is(':checked')) {
                    $('#quantity_row').hide();
                } else {
                    $('#quantity_row').show();
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Render file upload meta box.
     */
    public function render_file_meta_box($post) {
        wp_nonce_field('ebook_file_nonce', 'ebook_file_nonce_field');

        $file_path = get_post_meta($post->ID, '_ebook_file_path', true);
        $file_name = get_post_meta($post->ID, '_ebook_file_name', true);
        $file_size = get_post_meta($post->ID, '_ebook_file_size', true);
        $file_type = get_post_meta($post->ID, '_ebook_file_type', true);
        ?>
        <div class="ebook-file-upload">
            <?php if ($file_path): ?>
                <div class="ebook-file-info">
                    <p><strong><?php _e('Current File:', 'skillscore-ebook'); ?></strong></p>
                    <ul>
                        <li><?php _e('Name:', 'skillscore-ebook'); ?> <?php echo esc_html($file_name); ?></li>
                        <li><?php _e('Type:', 'skillscore-ebook'); ?> <?php echo esc_html(strtoupper($file_type)); ?></li>
                        <li><?php _e('Size:', 'skillscore-ebook'); ?> <?php echo esc_html(size_format($file_size)); ?></li>
                    </ul>
                    <button type="button" class="button button-secondary" id="remove-ebook-file">
                        <?php _e('Remove File', 'skillscore-ebook'); ?>
                    </button>
                </div>
            <?php endif; ?>

            <div class="ebook-file-upload-form" style="<?php echo $file_path ? 'display:none;' : ''; ?>">
                <p>
                    <label for="ebook_file"><?php _e('Upload Ebook File (PDF, EPUB, DOCX)', 'skillscore-ebook'); ?></label>
                </p>
                <input type="file" id="ebook_file" name="ebook_file" accept=".pdf,.epub,.docx" />
                <p class="description">
                    <?php _e('Upload a PDF, EPUB, or DOCX file. Maximum file size: 50MB.', 'skillscore-ebook'); ?>
                </p>
            </div>

            <input type="hidden" name="ebook_file_path" id="ebook_file_path" value="<?php echo esc_attr($file_path); ?>" />
            <input type="hidden" name="ebook_file_name" id="ebook_file_name" value="<?php echo esc_attr($file_name); ?>" />
            <input type="hidden" name="ebook_file_size" id="ebook_file_size" value="<?php echo esc_attr($file_size); ?>" />
            <input type="hidden" name="ebook_file_type" id="ebook_file_type" value="<?php echo esc_attr($file_type); ?>" />
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('#remove-ebook-file').click(function() {
                if(confirm('<?php _e('Are you sure you want to remove this file?', 'skillscore-ebook'); ?>')) {
                    $('.ebook-file-info').hide();
                    $('.ebook-file-upload-form').show();
                    $('#ebook_file_path').val('');
                    $('#ebook_file_name').val('');
                    $('#ebook_file_size').val('');
                    $('#ebook_file_type').val('');
                }
            });
        });
        </script>
        <?php
    }

    /**
     * Render preview settings meta box.
     */
    public function render_preview_meta_box($post) {
        wp_nonce_field('ebook_preview_nonce', 'ebook_preview_nonce_field');

        $enable_preview = get_post_meta($post->ID, '_ebook_enable_preview', true);
        $enable_audio = get_post_meta($post->ID, '_ebook_enable_audio', true);
        $preview_pages = get_post_meta($post->ID, '_ebook_preview_pages', true);
        ?>
        <p>
            <label>
                <input type="checkbox" name="ebook_enable_preview" value="1"
                       <?php checked($enable_preview, '1'); ?> />
                <?php _e('Enable Text Preview', 'skillscore-ebook'); ?>
            </label>
        </p>
        <p>
            <label>
                <input type="checkbox" name="ebook_enable_audio" value="1"
                       <?php checked($enable_audio, '1'); ?> />
                <?php _e('Enable Audio Preview', 'skillscore-ebook'); ?>
            </label>
        </p>
        <p>
            <label for="ebook_preview_pages"><?php _e('Preview Pages', 'skillscore-ebook'); ?></label>
            <input type="number" id="ebook_preview_pages" name="ebook_preview_pages"
                   value="<?php echo esc_attr($preview_pages ?: 3); ?>"
                   class="small-text" min="1" max="10" />
            <br><small><?php _e('Number of pages to show in preview (1-10)', 'skillscore-ebook'); ?></small>
        </p>
        <?php
    }

    /**
     * Render sales information meta box.
     */
    public function render_sales_meta_box($post) {
        global $wpdb;
        $orders_table = $wpdb->prefix . 'skillscore_orders';

        $total_sales = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(quantity), 0) FROM $orders_table
             WHERE ebook_id = %d AND payment_status = 'completed'",
            $post->ID
        ));

        $total_revenue = $wpdb->get_var($wpdb->prepare(
            "SELECT COALESCE(SUM(amount), 0) FROM $orders_table
             WHERE ebook_id = %d AND payment_status = 'completed'",
            $post->ID
        ));

        $currency_symbol = get_option('skillscore_ebook_currency_symbol', '$');
        ?>
        <div class="ebook-sales-stats">
            <p><strong><?php _e('Total Sales:', 'skillscore-ebook'); ?></strong> <?php echo intval($total_sales); ?></p>
            <p><strong><?php _e('Total Revenue:', 'skillscore-ebook'); ?></strong>
               <?php echo esc_html($currency_symbol . number_format($total_revenue, 2)); ?></p>
        </div>
        <?php
    }

    /**
     * Save meta box data.
     */
    public function save_meta_boxes($post_id, $post) {
        // Check nonces
        if (!isset($_POST['ebook_details_nonce_field']) ||
            !wp_verify_nonce($_POST['ebook_details_nonce_field'], 'ebook_details_nonce')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save ebook details
        $fields = array(
            'ebook_price' => 'sanitize_text_field',
            'ebook_quantity' => 'absint',
            'ebook_unlimited' => 'sanitize_text_field',
            'ebook_isbn' => 'sanitize_text_field',
            'ebook_author' => 'sanitize_text_field',
            'ebook_publisher' => 'sanitize_text_field',
            'ebook_pages' => 'absint',
            'ebook_language' => 'sanitize_text_field',
        );

        foreach ($fields as $field => $sanitize_callback) {
            if (isset($_POST[$field])) {
                update_post_meta($post_id, '_' . $field, $sanitize_callback($_POST[$field]));
            }
        }

        // Save file information
        if (isset($_POST['ebook_file_path'])) {
            update_post_meta($post_id, '_ebook_file_path', sanitize_text_field($_POST['ebook_file_path']));
            update_post_meta($post_id, '_ebook_file_name', sanitize_text_field($_POST['ebook_file_name']));
            update_post_meta($post_id, '_ebook_file_size', absint($_POST['ebook_file_size']));
            update_post_meta($post_id, '_ebook_file_type', sanitize_text_field($_POST['ebook_file_type']));
        }

        // Save preview settings
        if (isset($_POST['ebook_preview_nonce_field']) &&
            wp_verify_nonce($_POST['ebook_preview_nonce_field'], 'ebook_preview_nonce')) {
            update_post_meta($post_id, '_ebook_enable_preview', isset($_POST['ebook_enable_preview']) ? '1' : '0');
            update_post_meta($post_id, '_ebook_enable_audio', isset($_POST['ebook_enable_audio']) ? '1' : '0');
            update_post_meta($post_id, '_ebook_preview_pages', absint($_POST['ebook_preview_pages']));
        }

        // Handle file upload
        $this->handle_file_upload($post_id);
    }

    /**
     * Handle ebook file upload.
     */
    public function handle_file_upload($post_id = null) {
        if (!$post_id && isset($_POST['post_id'])) {
            $post_id = absint($_POST['post_id']);
        }

        if (!$post_id || !isset($_FILES['ebook_file']) || empty($_FILES['ebook_file']['name'])) {
            return;
        }

        // Check file type
        $allowed_types = array('pdf', 'epub', 'docx');
        $file_type = strtolower(pathinfo($_FILES['ebook_file']['name'], PATHINFO_EXTENSION));

        if (!in_array($file_type, $allowed_types)) {
            return;
        }

        // Check file size (50MB max)
        if ($_FILES['ebook_file']['size'] > 52428800) {
            return;
        }

        // Set up upload directory
        $upload_dir = wp_upload_dir();
        $ebook_dir = $upload_dir['basedir'] . '/skillscore-ebooks';

        if (!file_exists($ebook_dir)) {
            wp_mkdir_p($ebook_dir);
        }

        // Generate unique filename
        $filename = sanitize_file_name($post_id . '-' . time() . '.' . $file_type);
        $file_path = $ebook_dir . '/' . $filename;

        // Move uploaded file
        if (move_uploaded_file($_FILES['ebook_file']['tmp_name'], $file_path)) {
            update_post_meta($post_id, '_ebook_file_path', $filename);
            update_post_meta($post_id, '_ebook_file_name', sanitize_file_name($_FILES['ebook_file']['name']));
            update_post_meta($post_id, '_ebook_file_size', $_FILES['ebook_file']['size']);
            update_post_meta($post_id, '_ebook_file_type', $file_type);
        }
    }
}
