<?php
/**
 * Shortcodes Handler
 *
 * @package SkillScore_Ebook
 */

if (!defined('ABSPATH')) {
    exit;
}

class SkillScore_Ebook_Shortcodes {

    /**
     * Register shortcodes.
     */
    public function register_shortcodes() {
        add_shortcode('skillscore_ebooks', array($this, 'ebooks_list_shortcode'));
        add_shortcode('skillscore_ebook', array($this, 'single_ebook_shortcode'));
    }

    /**
     * Ebooks list shortcode.
     *
     * Usage: [skillscore_ebooks limit="12" category="fiction" orderby="date"]
     */
    public function ebooks_list_shortcode($atts) {
        $atts = shortcode_atts(array(
            'limit' => 12,
            'category' => '',
            'orderby' => 'date',
            'order' => 'DESC',
            'columns' => 3,
        ), $atts, 'skillscore_ebooks');

        $args = array(
            'post_type' => 'ebook',
            'posts_per_page' => intval($atts['limit']),
            'orderby' => sanitize_text_field($atts['orderby']),
            'order' => sanitize_text_field($atts['order']),
            'post_status' => 'publish',
        );

        if (!empty($atts['category'])) {
            $args['tax_query'] = array(
                array(
                    'taxonomy' => 'ebook_category',
                    'field' => 'slug',
                    'terms' => sanitize_text_field($atts['category']),
                ),
            );
        }

        $query = new WP_Query($args);

        ob_start();

        if ($query->have_posts()) {
            echo '<div class="skillscore-ebooks-grid grid grid-cols-1 md:grid-cols-' . intval($atts['columns']) . ' gap-6">';

            while ($query->have_posts()) {
                $query->the_post();
                $this->render_ebook_card(get_the_ID());
            }

            echo '</div>';
            wp_reset_postdata();
        } else {
            echo '<p class="text-gray-600">' . __('No ebooks found.', 'skillscore-ebook') . '</p>';
        }

        return ob_get_clean();
    }

    /**
     * Single ebook shortcode.
     *
     * Usage: [skillscore_ebook id="123"]
     */
    public function single_ebook_shortcode($atts) {
        $atts = shortcode_atts(array(
            'id' => 0,
        ), $atts, 'skillscore_ebook');

        $ebook_id = intval($atts['id']);

        if (!$ebook_id || get_post_type($ebook_id) !== 'ebook') {
            return '<p class="text-red-600">' . __('Invalid ebook ID.', 'skillscore-ebook') . '</p>';
        }

        ob_start();
        $this->render_single_ebook($ebook_id);
        return ob_get_clean();
    }

    /**
     * Render ebook card.
     */
    private function render_ebook_card($ebook_id) {
        $price = get_post_meta($ebook_id, '_ebook_price', true);
        $author = get_post_meta($ebook_id, '_ebook_author', true);
        $quantity = get_post_meta($ebook_id, '_ebook_quantity', true);
        $unlimited = get_post_meta($ebook_id, '_ebook_unlimited', true);
        $currency_symbol = get_option('skillscore_ebook_currency_symbol', '$');

        $in_stock = $unlimited || ($quantity && $quantity > 0);

        include SKILLSCORE_EBOOK_PLUGIN_DIR . 'templates/ebook-card.php';
    }

    /**
     * Render single ebook view.
     */
    private function render_single_ebook($ebook_id) {
        $price = get_post_meta($ebook_id, '_ebook_price', true);
        $author = get_post_meta($ebook_id, '_ebook_author', true);
        $publisher = get_post_meta($ebook_id, '_ebook_publisher', true);
        $isbn = get_post_meta($ebook_id, '_ebook_isbn', true);
        $pages = get_post_meta($ebook_id, '_ebook_pages', true);
        $language = get_post_meta($ebook_id, '_ebook_language', true);
        $quantity = get_post_meta($ebook_id, '_ebook_quantity', true);
        $unlimited = get_post_meta($ebook_id, '_ebook_unlimited', true);
        $enable_preview = get_post_meta($ebook_id, '_ebook_enable_preview', true);
        $enable_audio = get_post_meta($ebook_id, '_ebook_enable_audio', true);
        $currency_symbol = get_option('skillscore_ebook_currency_symbol', '$');
        $enable_quantity_selector = get_option('skillscore_ebook_enable_quantity_selector', true);

        $in_stock = $unlimited || ($quantity && $quantity > 0);

        include SKILLSCORE_EBOOK_PLUGIN_DIR . 'templates/ebook-single.php';
    }
}
