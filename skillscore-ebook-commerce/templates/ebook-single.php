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
$download_token  = isset($_GET['download_token']) ? sanitize_text_field($_GET['download_token']) : '';
$order_format_success = isset($_GET['order_format']) ? sanitize_text_field($_GET['order_format']) : 'ebook';
$order_ref_success    = isset($_GET['order_ref']) ? sanitize_text_field($_GET['order_ref']) : '';

// Get additional meta
$terms = get_the_terms($ebook_id, 'ebook_category');
$category = ($terms && !is_wp_error($terms)) ? $terms[0]->name : '';

// Checkout feature flags
$feat_format_selector  = (bool) get_option('skillscore_ebook_enable_format_selector');
$feat_phone_field      = (bool) get_option('skillscore_ebook_enable_phone_field');
$feat_shipping_fields  = (bool) get_option('skillscore_ebook_enable_shipping_fields');
$feat_bulk_option      = (bool) get_option('skillscore_ebook_enable_bulk_option');
$feat_bulk_min_qty     = intval(get_option('skillscore_ebook_bulk_min_quantity', 10));
$feat_order_bump       = (bool) get_option('skillscore_ebook_enable_order_bump');
$feat_bump_name        = get_option('skillscore_ebook_order_bump_name', '90-Day No Excuse Journal');
$feat_bump_price       = floatval(get_option('skillscore_ebook_order_bump_price', 0));
?>

<div class="skillscore-ebook-single fade-in">

    <?php if ($payment_success && $download_token): ?>
        <!-- eBook Success Message -->
        <div class="success-message">
            <div class="success-message-header">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <h3 class="success-message-title"><?php _e('YOUR ORDER IS CONFIRMED', 'skillscore-ebook'); ?></h3>
            </div>
            <p><?php _e('You now have a copy of a book that was not written to comfort you. It was written to confront what has kept people and nations weak for too long.', 'skillscore-ebook'); ?></p>
            <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 1.5rem;"><?php _e('A confirmation email has been sent with your access details. Read slowly, read honestly — do not race through it.', 'skillscore-ebook'); ?></p>
            <?php
            $download_handler = new SkillScore_Ebook_Download_Handler();
            $download_link = $download_handler->get_download_link($download_token);
            ?>
            <a href="<?php echo esc_url($download_link); ?>" class="btn-primary" style="display: inline-flex; align-items: center;">
                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                </svg>
                <?php _e('GET INSTANT ACCESS', 'skillscore-ebook'); ?>
            </a>
        </div>
    <?php elseif ($payment_success && $order_format_success === 'paperback'): ?>
        <!-- Paperback Order Confirmed -->
        <div class="success-message">
            <div class="success-message-header">
                <svg fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <h3 class="success-message-title"><?php _e('YOUR ORDER IS CONFIRMED', 'skillscore-ebook'); ?></h3>
            </div>
            <p><?php _e('You now have a copy of a book that was not written to comfort you. It was written to confront what has kept people and nations weak for too long.', 'skillscore-ebook'); ?></p>
            <p style="color: #9ca3af; font-size: 0.875rem;"><?php _e('A confirmation email has been sent. Your paperback copy will be shipped to the address you provided. Please allow standard delivery time.', 'skillscore-ebook'); ?></p>
            <?php if ($order_ref_success): ?>
                <p style="color: #6b7280; font-size: 0.8rem; margin-top: 1rem;"><?php _e('Order Reference:', 'skillscore-ebook'); ?> <strong><?php echo esc_html($order_ref_success); ?></strong></p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="ebook-single-grid">

        <!-- Left Column: Cover Image -->
        <div>
            <div class="single-cover-sticky">
                <?php if (has_post_thumbnail($ebook_id)): ?>
                    <?php echo get_the_post_thumbnail($ebook_id, 'large', array('class' => 'single-cover-image glow-box')); ?>
                <?php else: ?>
                    <div class="single-cover-placeholder glow-box">
                        <svg style="width: 96px; height: 96px; color: var(--rich-black);" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                    </div>
                <?php endif; ?>

                <!-- Price Display -->
                <div style="background: var(--neon-yellow); color: var(--rich-black); text-align: center; padding: 1.5rem; margin: 1.5rem 0; clip-path: polygon(12px 0, 100% 0, 100% calc(100% - 12px), calc(100% - 12px) 100%, 0 100%, 0 12px);">
                    <div style="font-size: 0.875rem; font-weight: 600; margin-bottom: 0.25rem; text-transform: uppercase; letter-spacing: 0.05em;">Price</div>
                    <div style="font-size: 2.5rem; font-weight: 800; line-height: 1;"><?php echo esc_html($currency_symbol . number_format($price, 2)); ?></div>
                </div>

                <!-- Purchase Buttons -->
                <?php if ($in_stock): ?>
                    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem;">
                        <button onclick="document.getElementById('ebook-purchase-form').scrollIntoView({behavior: 'smooth'})"
                                class="btn-primary" style="flex: 1;">
                            <?php _e('BUY NOW', 'skillscore-ebook'); ?>
                        </button>
                    </div>
                <?php else: ?>
                    <div style="background: #ef4444; color: white; text-align: center; padding: 1rem; border-radius: 8px; font-weight: 700; margin-bottom: 1.5rem;">
                        <?php _e('OUT OF STOCK', 'skillscore-ebook'); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Details -->
        <div>
            <!-- Title & Author -->
            <div style="margin-bottom: 2rem;">
                <h1 class="single-title glow-text">
                    <?php echo esc_html(strtoupper(get_the_title($ebook_id))); ?>
                </h1>
                <?php if (!empty($author)): ?>
                    <p class="single-author">
                        <?php _e('by', 'skillscore-ebook'); ?> <?php echo esc_html($author); ?>
                    </p>
                <?php endif; ?>
                <?php if ($category): ?>
                    <p class="single-category"><?php echo esc_html($category); ?></p>
                <?php endif; ?>
            </div>

            <!-- Meta Information -->
            <div class="single-meta">
                <div class="ebook-rating">
                    <div class="stars">
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star">★</span>
                        <span class="star empty">★</span>
                    </div>
                    <span style="font-size: 1.125rem; font-weight: 600;">4.0</span>
                </div>
                <?php if (!empty($pages)): ?>
                    <span class="single-meta-divider">|</span>
                    <span class="single-meta-item"><?php echo esc_html($pages); ?> <?php _e('pages', 'skillscore-ebook'); ?></span>
                <?php endif; ?>
                <span class="single-meta-divider">|</span>
                <span class="single-meta-item"><?php _e('Published', 'skillscore-ebook'); ?> <?php echo get_the_date('Y', $ebook_id); ?></span>
            </div>

            <!-- Description -->
            <div style="border-top: 2px solid var(--light-gray); border-bottom: 2px solid var(--light-gray); padding: 1.5rem 0; margin: 2rem 0;">
                <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem;"><?php _e('ABOUT THIS BOOK', 'skillscore-ebook'); ?></h3>
                <div style="color: #d1d5db; line-height: 1.75;">
                    <?php
                    $content = get_post_field('post_content', $ebook_id);
                    if ($content) {
                        echo wpautop(wp_trim_words($content, 50));
                    } else {
                        echo wpautop(get_the_excerpt($ebook_id));
                    }
                    ?>
                </div>
            </div>

            <!-- Audio Preview -->
            <?php if ($enable_audio): ?>
                <div class="audio-preview-section">
                    <div class="audio-preview-header">
                        <h3 class="audio-preview-title">
                            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/>
                            </svg>
                            <span><?php _e('AUDIO PREVIEW', 'skillscore-ebook'); ?></span>
                        </h3>
                        <span class="audio-preview-chapter"><?php _e('Sample Audio', 'skillscore-ebook'); ?></span>
                    </div>

                    <div class="audio-player">
                        <div class="audio-controls">
                            <button id="load-audio-preview" data-ebook-id="<?php echo esc_attr($ebook_id); ?>" class="audio-play-btn">
                                <div class="audio-play-icon">
                                    <svg fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M8 5v14l11-7z"></path>
                                    </svg>
                                </div>
                                <div class="audio-info-text">
                                    <div class="audio-title"><?php _e('Sample Audio', 'skillscore-ebook'); ?></div>
                                    <div class="audio-duration">3:45</div>
                                </div>
                            </button>

                            <div class="audio-time-display">
                                <span id="currentTime">0:00</span>
                                <span style="color: #6b7280;">/</span>
                                <span id="duration">3:45</span>
                            </div>
                        </div>

                        <div class="audio-progress" id="audio-progress-bar">
                            <div class="audio-progress-bar" id="progressBar"></div>
                        </div>

                        <div id="audio-preview-player" class="hidden">
                            <audio controls style="width: 100%; margin-top: 1rem;">
                                <source id="audio-preview-source" type="audio/mpeg">
                            </audio>
                        </div>

                        <div class="audio-footer">
                            <span>🎧 <?php _e('Listen to a sample of the audiobook version', 'skillscore-ebook'); ?></span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Text Preview -->
            <?php if ($enable_preview): ?>
                <div class="text-preview-section">
                    <h3 class="text-preview-title">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span><?php _e('TEXT PREVIEW', 'skillscore-ebook'); ?></span>
                    </h3>

                    <div class="preview-text">
                        <h4><?php _e('Preview', 'skillscore-ebook'); ?></h4>
                        <?php
                        $preview_content = get_the_excerpt($ebook_id);
                        if (!$preview_content) {
                            $preview_content = get_post_field('post_content', $ebook_id);
                        }
                        echo wpautop(wp_trim_words($preview_content, 150));
                        ?>
                        <p style="font-size: 0.875rem; color: #6b7280; font-style: italic; margin-top: 1.5rem;">
                            <?php _e('Purchase to read the complete ebook...', 'skillscore-ebook'); ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Key Features -->
            <div class="features-grid">
                <div class="feature-box">
                    <div class="feature-icon">📚</div>
                    <div class="feature-title"><?php _e('Instant Access', 'skillscore-ebook'); ?></div>
                    <div class="feature-description"><?php _e('Download and start reading immediately', 'skillscore-ebook'); ?></div>
                </div>

                <div class="feature-box">
                    <div class="feature-icon">📱</div>
                    <div class="feature-title"><?php _e('Multi-Device', 'skillscore-ebook'); ?></div>
                    <div class="feature-description"><?php _e('Read on phone, tablet, or computer', 'skillscore-ebook'); ?></div>
                </div>

                <?php if ($enable_audio): ?>
                <div class="feature-box">
                    <div class="feature-icon">🎧</div>
                    <div class="feature-title"><?php _e('Audio Included', 'skillscore-ebook'); ?></div>
                    <div class="feature-description"><?php _e('Audiobook version included free', 'skillscore-ebook'); ?></div>
                </div>
                <?php endif; ?>

                <div class="feature-box">
                    <div class="feature-icon">💾</div>
                    <div class="feature-title"><?php _e('Secure Download', 'skillscore-ebook'); ?></div>
                    <div class="feature-description"><?php _e('Yours forever, secure delivery', 'skillscore-ebook'); ?></div>
                </div>
            </div>

            <!-- Purchase Form -->
            <?php if ($in_stock): ?>
                <div style="background: var(--dark-gray); padding: 1.5rem; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px); border: 2px solid var(--light-gray); margin-top: 2rem;">
                    <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--neon-yellow);">
                        <?php _e('COMPLETE YOUR ORDER', 'skillscore-ebook'); ?>
                    </h3>
                    <p style="color: #9ca3af; font-size: 0.875rem; margin-bottom: 1.5rem; line-height: 1.5;">
                        <?php _e('You are not buying comfort; you are buying confrontation, clarity, and a book strong enough to force an honest reckoning.', 'skillscore-ebook'); ?>
                    </p>

                    <form id="ebook-purchase-form">
                        <input type="hidden" name="ebook_id" value="<?php echo esc_attr($ebook_id); ?>">
                        <!-- These hidden fields are updated by JS when format/type toggles change -->
                        <input type="hidden" name="order_format" id="hidden-order-format" value="ebook">
                        <input type="hidden" name="order_type" id="hidden-order-type" value="individual">

                        <?php /* ── PURCHASE TYPE TOGGLE (Bulk Option) ── */ ?>
                        <?php if ($feat_bulk_option): ?>
                        <div class="sse-checkout-section" style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--light-gray);">
                            <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em; color: var(--neon-yellow);">
                                <?php _e('Purchase Type', 'skillscore-ebook'); ?>
                            </label>
                            <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                <label class="sse-type-option sse-type-option--active" id="type-individual-label" style="flex: 1; min-width: 160px; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; border: 2px solid var(--neon-yellow); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="_order_type_ui" value="individual" checked style="accent-color: var(--neon-yellow);">
                                    <span style="font-weight: 600; font-size: 0.9rem;"><?php _e('Individual Purchase', 'skillscore-ebook'); ?></span>
                                </label>
                                <label class="sse-type-option" id="type-bulk-label" style="flex: 1; min-width: 160px; display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1rem; border: 2px solid var(--light-gray); border-radius: 8px; cursor: pointer;">
                                    <input type="radio" name="_order_type_ui" value="bulk" style="accent-color: var(--neon-yellow);">
                                    <span style="font-weight: 600; font-size: 0.9rem;"><?php printf(__('Bulk Order (%d+ copies)', 'skillscore-ebook'), $feat_bulk_min_qty); ?></span>
                                </label>
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php /* ── INDIVIDUAL PURCHASE SECTION ── */ ?>
                        <div id="individual-purchase-section">

                            <?php /* Format Selector */ ?>
                            <?php if ($feat_format_selector): ?>
                            <div class="sse-checkout-section" style="margin-bottom: 1.25rem;">
                                <label style="display: block; font-weight: 700; font-size: 0.8rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.08em;">
                                    <?php _e('Select Format', 'skillscore-ebook'); ?>
                                </label>
                                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
                                    <label class="sse-format-option sse-format-option--active" id="format-ebook-label" style="flex: 1; min-width: 140px; display: flex; align-items: flex-start; gap: 0.5rem; padding: 0.75rem 1rem; border: 2px solid var(--neon-yellow); border-radius: 8px; cursor: pointer;">
                                        <input type="radio" name="_order_format_ui" value="ebook" checked style="accent-color: var(--neon-yellow); margin-top: 2px;">
                                        <span>
                                            <strong style="display: block; font-size: 0.9rem;"><?php _e('eBook', 'skillscore-ebook'); ?></strong>
                                            <small style="color: #9ca3af;"><?php _e('Instant download', 'skillscore-ebook'); ?></small>
                                        </span>
                                    </label>
                                    <label class="sse-format-option" id="format-paperback-label" style="flex: 1; min-width: 140px; display: flex; align-items: flex-start; gap: 0.5rem; padding: 0.75rem 1rem; border: 2px solid var(--light-gray); border-radius: 8px; cursor: pointer;">
                                        <input type="radio" name="_order_format_ui" value="paperback" style="accent-color: var(--neon-yellow); margin-top: 2px;">
                                        <span>
                                            <strong style="display: block; font-size: 0.9rem;"><?php _e('Paperback', 'skillscore-ebook'); ?></strong>
                                            <small style="color: #9ca3af;"><?php _e('Physical copy', 'skillscore-ebook'); ?></small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php /* Quantity Selector */ ?>
                            <?php if ($enable_quantity_selector): ?>
                                <div style="margin-bottom: 1rem;">
                                    <label class="sse-field-label"><?php _e('Quantity', 'skillscore-ebook'); ?></label>
                                    <input type="number" name="quantity" id="sse-quantity" value="1" min="1"
                                           <?php if (!$unlimited): ?>max="<?php echo esc_attr($quantity); ?>"<?php endif; ?>
                                           data-price="<?php echo esc_attr($price); ?>"
                                           class="sse-field-input">
                                    <p id="sse-total-display" style="margin-top: 0.4rem; font-size: 0.875rem; color: var(--neon-yellow); font-weight: 700; display: none;"></p>
                                </div>
                            <?php else: ?>
                                <input type="hidden" name="quantity" value="1">
                            <?php endif; ?>

                            <?php /* Customer Name */ ?>
                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label"><?php _e('Your Name', 'skillscore-ebook'); ?></label>
                                <input type="text" name="user_name" required class="sse-field-input"
                                       placeholder="<?php esc_attr_e('Full name', 'skillscore-ebook'); ?>">
                            </div>

                            <?php /* Customer Email */ ?>
                            <div style="margin-bottom: 0.5rem;">
                                <label class="sse-field-label"><?php _e('Your Email', 'skillscore-ebook'); ?></label>
                                <input type="email" name="user_email" required class="sse-field-input"
                                       placeholder="<?php esc_attr_e('your@email.com', 'skillscore-ebook'); ?>">
                            </div>
                            <p class="sse-microcopy"><?php _e('Your email will be used to send your confirmation and/or access details.', 'skillscore-ebook'); ?></p>

                            <?php /* Phone Field */ ?>
                            <?php if ($feat_phone_field): ?>
                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label"><?php _e('Phone Number', 'skillscore-ebook'); ?></label>
                                <input type="tel" name="user_phone" class="sse-field-input"
                                       placeholder="<?php esc_attr_e('+1 (555) 000-0000', 'skillscore-ebook'); ?>">
                            </div>
                            <?php endif; ?>

                            <?php /* Shipping Fields (shown when Paperback selected) */ ?>
                            <?php if ($feat_shipping_fields): ?>
                            <div id="shipping-fields-group" style="display: none; margin-bottom: 1rem; padding: 1rem; background: var(--rich-black); border: 1px solid var(--light-gray); border-radius: 8px;">
                                <p class="sse-microcopy" style="margin-bottom: 1rem; color: #f59e0b;">
                                    <?php _e('Please enter your shipping details carefully to avoid delivery delays.', 'skillscore-ebook'); ?>
                                </p>
                                <div style="margin-bottom: 0.75rem;">
                                    <label class="sse-field-label"><?php _e('Street Address', 'skillscore-ebook'); ?></label>
                                    <input type="text" name="shipping_address" class="sse-field-input sse-shipping-field"
                                           placeholder="<?php esc_attr_e('123 Main Street', 'skillscore-ebook'); ?>">
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 0.75rem;">
                                    <div>
                                        <label class="sse-field-label"><?php _e('City', 'skillscore-ebook'); ?></label>
                                        <input type="text" name="shipping_city" class="sse-field-input sse-shipping-field"
                                               placeholder="<?php esc_attr_e('City', 'skillscore-ebook'); ?>">
                                    </div>
                                    <div>
                                        <label class="sse-field-label"><?php _e('State / Province', 'skillscore-ebook'); ?></label>
                                        <input type="text" name="shipping_state" class="sse-field-input sse-shipping-field"
                                               placeholder="<?php esc_attr_e('State', 'skillscore-ebook'); ?>">
                                    </div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                                    <div>
                                        <label class="sse-field-label"><?php _e('Country', 'skillscore-ebook'); ?></label>
                                        <input type="text" name="shipping_country" class="sse-field-input sse-shipping-field"
                                               placeholder="<?php esc_attr_e('Country', 'skillscore-ebook'); ?>">
                                    </div>
                                    <div>
                                        <label class="sse-field-label"><?php _e('ZIP / Postal Code', 'skillscore-ebook'); ?></label>
                                        <input type="text" name="shipping_zip" class="sse-field-input sse-shipping-field"
                                               placeholder="<?php esc_attr_e('Postal code', 'skillscore-ebook'); ?>">
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php /* Payment Gateway Selection */ ?>
                            <div style="margin-bottom: 0.5rem;">
                                <label class="sse-field-label"><?php _e('Select Payment Method', 'skillscore-ebook'); ?></label>
                                <div class="payment-methods" id="gateway-options">
                                    <?php if (get_option('skillscore_ebook_enable_paystack')): ?>
                                        <label class="payment-method-option">
                                            <input type="radio" name="gateway" value="paystack" class="sse-gateway-radio" required>
                                            <span style="font-weight: 600;">Paystack</span>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (get_option('skillscore_ebook_enable_flutterwave')): ?>
                                        <label class="payment-method-option">
                                            <input type="radio" name="gateway" value="flutterwave" class="sse-gateway-radio" required>
                                            <span style="font-weight: 600;">Flutterwave</span>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (get_option('skillscore_ebook_enable_stripe')): ?>
                                        <label class="payment-method-option">
                                            <input type="radio" name="gateway" value="stripe" class="sse-gateway-radio" required>
                                            <span style="font-weight: 600;">Stripe</span>
                                        </label>
                                    <?php endif; ?>

                                    <?php if (get_option('skillscore_ebook_enable_paypal')): ?>
                                        <label class="payment-method-option">
                                            <input type="radio" name="gateway" value="paypal" class="sse-gateway-radio" required>
                                            <span style="font-weight: 600;">PayPal</span>
                                        </label>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <p class="sse-microcopy" style="margin-bottom: 1.5rem;"><?php _e('Your order is protected through secure checkout.', 'skillscore-ebook'); ?></p>

                            <?php /* Order Bump / Add-on */ ?>
                            <?php if ($feat_order_bump): ?>
                            <div class="sse-order-bump" style="margin-bottom: 1.5rem; padding: 1rem; border: 2px dashed var(--neon-yellow); border-radius: 8px; background: rgba(255,229,0,0.05);">
                                <label style="display: flex; align-items: flex-start; gap: 0.75rem; cursor: pointer;">
                                    <input type="checkbox" name="order_bump" id="order-bump-checkbox" value="1"
                                           style="width: 20px; height: 20px; accent-color: var(--neon-yellow); margin-top: 2px; flex-shrink: 0;">
                                    <span>
                                        <strong style="display: block; font-size: 0.95rem; margin-bottom: 0.25rem; color: var(--neon-yellow);">
                                            <?php printf(
                                                __('Add the %s', 'skillscore-ebook'),
                                                esc_html($feat_bump_name)
                                            ); ?>
                                            <?php if ($feat_bump_price > 0): ?>
                                                <span id="bump-price-display"> — <?php echo esc_html($currency_symbol . number_format($feat_bump_price, 2)); ?></span>
                                            <?php else: ?>
                                                <span style="font-size: 0.8rem; font-weight: 400; color: #9ca3af;"> — <?php _e('FREE with this order', 'skillscore-ebook'); ?></span>
                                            <?php endif; ?>
                                        </strong>
                                        <span style="font-size: 0.85rem; color: #9ca3af; line-height: 1.5;">
                                            <?php _e('Take the book deeper with the companion guide designed for personal reflection, reading groups, leadership cohorts, and serious discussion.', 'skillscore-ebook'); ?>
                                        </span>
                                    </span>
                                </label>
                            </div>
                            <?php endif; ?>

                            <?php /* Submit Button */ ?>
                            <button type="submit" id="individual-submit-btn" class="btn-primary"
                                    style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"/>
                                </svg>
                                <span id="submit-btn-text"><?php _e('GET INSTANT ACCESS', 'skillscore-ebook'); ?></span>
                            </button>
                            <p class="sse-microcopy" style="text-align: center; margin-top: 0.75rem;">
                                <?php _e('You are one step away from owning a book that refuses to flatter weakness.', 'skillscore-ebook'); ?>
                            </p>

                        </div><!-- /#individual-purchase-section -->

                        <?php /* ── BULK INQUIRY SECTION ── */ ?>
                        <?php if ($feat_bulk_option): ?>
                        <div id="bulk-inquiry-section" style="display: none;">
                            <p style="font-size: 0.9rem; color: #9ca3af; margin-bottom: 1.5rem; font-style: italic; border-left: 3px solid var(--neon-yellow); padding-left: 0.75rem;">
                                <?php _e('Bring this book into your institution, cohort, summit, or community.', 'skillscore-ebook'); ?>
                            </p>

                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label"><?php _e('Your Name', 'skillscore-ebook'); ?></label>
                                <input type="text" name="bulk_user_name" class="sse-field-input bulk-required"
                                       placeholder="<?php esc_attr_e('Full name', 'skillscore-ebook'); ?>">
                            </div>

                            <div style="margin-bottom: 0.5rem;">
                                <label class="sse-field-label"><?php _e('Your Email', 'skillscore-ebook'); ?></label>
                                <input type="email" name="bulk_user_email" class="sse-field-input bulk-required"
                                       placeholder="<?php esc_attr_e('your@email.com', 'skillscore-ebook'); ?>">
                            </div>
                            <p class="sse-microcopy" style="margin-bottom: 1rem;"><?php _e('Your email will be used to send your confirmation and/or access details.', 'skillscore-ebook'); ?></p>

                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label"><?php _e('Organization / Institution', 'skillscore-ebook'); ?></label>
                                <input type="text" name="organization" class="sse-field-input bulk-required"
                                       placeholder="<?php esc_attr_e('Organization name', 'skillscore-ebook'); ?>">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label"><?php _e('Phone Number', 'skillscore-ebook'); ?></label>
                                <input type="tel" name="user_phone" class="sse-field-input"
                                       placeholder="<?php esc_attr_e('+1 (555) 000-0000', 'skillscore-ebook'); ?>">
                            </div>

                            <div style="margin-bottom: 1rem;">
                                <label class="sse-field-label">
                                    <?php printf(__('Estimated Quantity (min %d)', 'skillscore-ebook'), $feat_bulk_min_qty); ?>
                                </label>
                                <input type="number" name="bulk_quantity" class="sse-field-input bulk-required"
                                       min="<?php echo esc_attr($feat_bulk_min_qty); ?>"
                                       placeholder="<?php esc_attr_e('Number of copies', 'skillscore-ebook'); ?>">
                            </div>

                            <div style="margin-bottom: 1.5rem;">
                                <label class="sse-field-label"><?php _e('Inquiry / Message', 'skillscore-ebook'); ?></label>
                                <textarea name="bulk_message" rows="4" class="sse-field-input"
                                          style="resize: vertical;"
                                          placeholder="<?php esc_attr_e('Tell us about your use case — event, program, organisation type, timeline, etc.', 'skillscore-ebook'); ?>"></textarea>
                            </div>

                            <button type="submit" id="bulk-submit-btn" class="btn-primary"
                                    style="width: 100%; display: flex; align-items: center; justify-content: center;">
                                <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"/>
                                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"/>
                                </svg>
                                <?php _e('SUBMIT BULK INQUIRY', 'skillscore-ebook'); ?>
                            </button>
                            <p class="sse-microcopy" style="text-align: center; margin-top: 0.75rem;">
                                <?php _e('No payment is processed at this stage. Our team will follow up with pricing and details.', 'skillscore-ebook'); ?>
                            </p>
                        </div><!-- /#bulk-inquiry-section -->
                        <?php endif; ?>

                    </form>
                </div>
            <?php endif; ?>

            <!-- Ebook Details -->
            <?php if (!empty($publisher) || !empty($isbn) || !empty($pages) || !empty($language)): ?>
                <div style="background: #000000; border: 2px solid var(--neon-yellow); padding: 1.5rem; margin-top: 2rem; clip-path: polygon(10px 0, 100% 0, 100% calc(100% - 10px), calc(100% - 10px) 100%, 0 100%, 0 10px);">
                    <h3 style="font-weight: 700; font-size: 1.25rem; margin-bottom: 1rem;"><?php _e('DETAILS', 'skillscore-ebook'); ?></h3>
                    <dl style="display: grid; gap: 0.75rem;">
                        <?php if (!empty($publisher)): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <dt style="color: #9ca3af;"><?php _e('Publisher:', 'skillscore-ebook'); ?></dt>
                                <dd style="font-weight: 600;"><?php echo esc_html($publisher); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($isbn)): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <dt style="color: #9ca3af;"><?php _e('ISBN:', 'skillscore-ebook'); ?></dt>
                                <dd style="font-family: monospace;"><?php echo esc_html($isbn); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($pages)): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <dt style="color: #9ca3af;"><?php _e('Pages:', 'skillscore-ebook'); ?></dt>
                                <dd style="font-weight: 600;"><?php echo esc_html($pages); ?></dd>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($language)): ?>
                            <div style="display: flex; justify-content: space-between;">
                                <dt style="color: #9ca3af;"><?php _e('Language:', 'skillscore-ebook'); ?></dt>
                                <dd style="font-weight: 600;"><?php echo esc_html($language); ?></dd>
                            </div>
                        <?php endif; ?>
                    </dl>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
