<?php
/**
 * Template: Single Ebook View
 *
 * @package SkillScore_Ebook
 */

if (!defined('ABSPATH')) {
    exit;
}

// Check for payment success
$payment_success = isset($_GET['payment_success']) && $_GET['payment_success'] === '1';
$download_token = isset($_GET['download_token']) ? sanitize_text_field($_GET['download_token']) : '';
?>

<div class="skillscore-ebook-single max-w-6xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">

    <?php if ($payment_success && $download_token): ?>
        <!-- Success Message -->
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 mb-6" role="alert">
            <div class="flex items-center mb-4">
                <svg class="w-6 h-6 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <h3 class="text-lg font-bold"><?php _e('Payment Successful!', 'skillscore-ebook'); ?></h3>
            </div>
            <p class="mb-4"><?php _e('Your payment has been processed successfully. You can now download your ebook.', 'skillscore-ebook'); ?></p>
            <?php
            $download_handler = new SkillScore_Ebook_Download_Handler();
            $download_link = $download_handler->get_download_link($download_token);
            ?>
            <a href="<?php echo esc_url($download_link); ?>"
               class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <?php _e('Download Now', 'skillscore-ebook'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8 p-8">

        <!-- Left Column: Image and Preview -->
        <div class="space-y-6">
            <!-- Cover Image -->
            <div class="ebook-cover-large">
                <?php if (has_post_thumbnail($ebook_id)): ?>
                    <?php echo get_the_post_thumbnail($ebook_id, 'large', array('class' => 'w-full rounded-lg shadow-md')); ?>
                <?php else: ?>
                    <div class="w-full h-96 bg-gradient-to-br from-yellow-400 to-yellow-600 rounded-lg shadow-md flex items-center justify-center">
                        <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Audio Preview -->
            <?php if ($enable_audio): ?>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.984 5.984 0 01-1.757 4.243 1 1 0 01-1.415-1.415A3.984 3.984 0 0013 10a3.983 3.983 0 00-1.172-2.828 1 1 0 010-1.415z" clip-rule="evenodd"/>
                        </svg>
                        <?php _e('Audio Preview', 'skillscore-ebook'); ?>
                    </h4>
                    <div id="audio-preview-player" class="hidden">
                        <audio controls class="w-full">
                            <source id="audio-preview-source" type="audio/mpeg">
                        </audio>
                    </div>
                    <button id="load-audio-preview"
                            data-ebook-id="<?php echo esc_attr($ebook_id); ?>"
                            class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200">
                        <?php _e('Load Audio Preview', 'skillscore-ebook'); ?>
                    </button>
                    <p class="text-xs text-gray-500 mt-2">
                        <?php _e('Listen to a sample of this ebook', 'skillscore-ebook'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Details and Purchase -->
        <div class="space-y-6">
            <!-- Title -->
            <div>
                <h1 class="text-4xl font-bold text-gray-900 mb-2">
                    <?php echo esc_html(get_the_title($ebook_id)); ?>
                </h1>
                <?php if (!empty($author)): ?>
                    <p class="text-lg text-gray-600">
                        <?php _e('by', 'skillscore-ebook'); ?> <span class="font-semibold text-gray-900"><?php echo esc_html($author); ?></span>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Price -->
            <div class="bg-yellow-50 border-2 border-yellow-400 rounded-lg p-6">
                <div class="text-4xl font-bold text-yellow-600 mb-2">
                    <?php echo esc_html($currency_symbol . number_format($price, 2)); ?>
                </div>
                <?php if ($in_stock): ?>
                    <p class="text-green-600 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <?php _e('In Stock', 'skillscore-ebook'); ?>
                    </p>
                <?php else: ?>
                    <p class="text-red-600 font-semibold flex items-center">
                        <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <?php _e('Out of Stock', 'skillscore-ebook'); ?>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Purchase Form -->
            <?php if ($in_stock): ?>
                <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
                    <form id="ebook-purchase-form" class="space-y-4">
                        <input type="hidden" name="ebook_id" value="<?php echo esc_attr($ebook_id); ?>">

                        <!-- Quantity Selector -->
                        <?php if ($enable_quantity_selector): ?>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <?php _e('Quantity', 'skillscore-ebook'); ?>
                                </label>
                                <input type="number" name="quantity" value="1" min="1"
                                       <?php if (!$unlimited): ?>max="<?php echo esc_attr($quantity); ?>"<?php endif; ?>
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                            </div>
                        <?php else: ?>
                            <input type="hidden" name="quantity" value="1">
                        <?php endif; ?>

                        <!-- Customer Information -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php _e('Your Name', 'skillscore-ebook'); ?>
                            </label>
                            <input type="text" name="user_name" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <?php _e('Your Email', 'skillscore-ebook'); ?>
                            </label>
                            <input type="email" name="user_email" required
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500 focus:border-transparent">
                        </div>

                        <!-- Payment Gateway Selection -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <?php _e('Select Payment Method', 'skillscore-ebook'); ?>
                            </label>
                            <div class="space-y-2">
                                <?php if (get_option('skillscore_ebook_enable_paystack')): ?>
                                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors">
                                        <input type="radio" name="gateway" value="paystack" required class="mr-3">
                                        <span class="font-medium">Paystack</span>
                                    </label>
                                <?php endif; ?>

                                <?php if (get_option('skillscore_ebook_enable_flutterwave')): ?>
                                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors">
                                        <input type="radio" name="gateway" value="flutterwave" required class="mr-3">
                                        <span class="font-medium">Flutterwave</span>
                                    </label>
                                <?php endif; ?>

                                <?php if (get_option('skillscore_ebook_enable_stripe')): ?>
                                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors">
                                        <input type="radio" name="gateway" value="stripe" required class="mr-3">
                                        <span class="font-medium">Stripe</span>
                                    </label>
                                <?php endif; ?>

                                <?php if (get_option('skillscore_ebook_enable_paypal')): ?>
                                    <label class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-yellow-500 transition-colors">
                                        <input type="radio" name="gateway" value="paypal" required class="mr-3">
                                        <span class="font-medium">PayPal</span>
                                    </label>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Purchase Button -->
                        <button type="submit"
                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-4 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                            </svg>
                            <?php _e('Purchase Now', 'skillscore-ebook'); ?>
                        </button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Ebook Details -->
            <div class="bg-white border border-gray-200 rounded-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4"><?php _e('Details', 'skillscore-ebook'); ?></h3>
                <dl class="space-y-3">
                    <?php if (!empty($publisher)): ?>
                        <div class="flex justify-between">
                            <dt class="text-gray-600"><?php _e('Publisher:', 'skillscore-ebook'); ?></dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($publisher); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($isbn)): ?>
                        <div class="flex justify-between">
                            <dt class="text-gray-600"><?php _e('ISBN:', 'skillscore-ebook'); ?></dt>
                            <dd class="font-mono text-gray-900"><?php echo esc_html($isbn); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($pages)): ?>
                        <div class="flex justify-between">
                            <dt class="text-gray-600"><?php _e('Pages:', 'skillscore-ebook'); ?></dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($pages); ?></dd>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($language)): ?>
                        <div class="flex justify-between">
                            <dt class="text-gray-600"><?php _e('Language:', 'skillscore-ebook'); ?></dt>
                            <dd class="font-semibold text-gray-900"><?php echo esc_html($language); ?></dd>
                        </div>
                    <?php endif; ?>
                </dl>
            </div>
        </div>
    </div>

    <!-- Description -->
    <div class="p-8 border-t border-gray-200">
        <h2 class="text-2xl font-bold text-gray-900 mb-4"><?php _e('Description', 'skillscore-ebook'); ?></h2>
        <div class="prose max-w-none text-gray-700">
            <?php echo wpautop(get_post_field('post_content', $ebook_id)); ?>
        </div>
    </div>
</div>
