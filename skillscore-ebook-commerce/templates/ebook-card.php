<?php
/**
 * Template: Ebook Card
 *
 * @package SkillScore_Ebook
 */

if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="ebook-card bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
    <!-- Ebook Cover -->
    <div class="ebook-cover relative">
        <?php if (has_post_thumbnail($ebook_id)): ?>
            <a href="<?php echo esc_url(get_permalink($ebook_id)); ?>">
                <?php echo get_the_post_thumbnail($ebook_id, 'medium', array('class' => 'w-full h-64 object-cover')); ?>
            </a>
        <?php else: ?>
            <a href="<?php echo esc_url(get_permalink($ebook_id)); ?>">
                <div class="w-full h-64 bg-gradient-to-br from-yellow-400 to-yellow-600 flex items-center justify-center">
                    <svg class="w-24 h-24 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                    </svg>
                </div>
            </a>
        <?php endif; ?>

        <!-- Stock Badge -->
        <?php if (!$in_stock): ?>
            <div class="absolute top-2 right-2 bg-red-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                <?php _e('Out of Stock', 'skillscore-ebook'); ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Ebook Details -->
    <div class="p-6">
        <!-- Title -->
        <h3 class="text-xl font-bold text-gray-900 mb-2 hover:text-yellow-600 transition-colors">
            <a href="<?php echo esc_url(get_permalink($ebook_id)); ?>">
                <?php echo esc_html(get_the_title($ebook_id)); ?>
            </a>
        </h3>

        <!-- Author -->
        <?php if (!empty($author)): ?>
            <p class="text-sm text-gray-600 mb-3">
                <?php _e('by', 'skillscore-ebook'); ?> <span class="font-medium"><?php echo esc_html($author); ?></span>
            </p>
        <?php endif; ?>

        <!-- Excerpt -->
        <div class="text-gray-700 text-sm mb-4 line-clamp-3">
            <?php echo wp_trim_words(get_the_excerpt($ebook_id), 15); ?>
        </div>

        <!-- Price and Action -->
        <div class="flex items-center justify-between">
            <div class="text-2xl font-bold text-yellow-600">
                <?php echo esc_html($currency_symbol . number_format($price, 2)); ?>
            </div>
            <a href="<?php echo esc_url(get_permalink($ebook_id)); ?>"
               class="<?php echo $in_stock ? 'bg-yellow-500 hover:bg-yellow-600' : 'bg-gray-400 cursor-not-allowed'; ?> text-white px-6 py-2 rounded-lg font-semibold transition-colors duration-200">
                <?php echo $in_stock ? __('View Details', 'skillscore-ebook') : __('Unavailable', 'skillscore-ebook'); ?>
            </a>
        </div>
    </div>
</div>
