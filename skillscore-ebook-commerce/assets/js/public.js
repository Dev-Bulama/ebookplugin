/**
 * SkillScore Ebook Commerce - Public JavaScript
 *
 * @package SkillScore_Ebook
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /* ──────────────────────────────────────────
         * FORMAT SELECTOR — update hidden field + UI + button text
         * ────────────────────────────────────────── */
        $('input[name="_order_format_ui"]').on('change', function() {
            var format = $(this).val();
            $('#hidden-order-format').val(format);

            // Toggle format option borders
            $('.sse-format-option').css('border-color', 'var(--light-gray)');
            $('input[name="_order_format_ui"]:checked').closest('.sse-format-option').css('border-color', 'var(--neon-yellow)');

            // Show / hide shipping fields
            if (skillscoreEbook.enableShippingFields) {
                if (format === 'paperback') {
                    $('#shipping-fields-group').slideDown(200);
                    $('#shipping-fields-group .sse-shipping-field').prop('required', true);
                } else {
                    $('#shipping-fields-group').slideUp(200);
                    $('#shipping-fields-group .sse-shipping-field').prop('required', false);
                }
            }

            // Update submit button text
            updateSubmitButtonText();
        });

        function updateSubmitButtonText() {
            var format   = $('#hidden-order-format').val() || 'ebook';
            var btnText  = format === 'paperback' ? 'COMPLETE MY ORDER' : 'GET INSTANT ACCESS';
            $('#submit-btn-text').text(btnText);
        }

        // Initialise button text on page load
        updateSubmitButtonText();

        /* ──────────────────────────────────────────
         * PURCHASE TYPE TOGGLE — individual vs bulk
         * ────────────────────────────────────────── */
        $('input[name="_order_type_ui"]').on('change', function() {
            var type = $(this).val();
            $('#hidden-order-type').val(type);

            // Toggle type option borders
            $('.sse-type-option').css('border-color', 'var(--light-gray)');
            $('input[name="_order_type_ui"]:checked').closest('.sse-type-option').css('border-color', 'var(--neon-yellow)');

            if (type === 'bulk') {
                $('#individual-purchase-section').slideUp(200, function() {
                    $('#bulk-inquiry-section').slideDown(200);
                });
                // Remove required from individual-only fields so form can submit
                $('#individual-purchase-section input[required], #individual-purchase-section textarea[required]')
                    .prop('required', false).addClass('sse-was-required');
                // Make bulk fields required
                $('#bulk-inquiry-section .bulk-required').prop('required', true);
            } else {
                $('#bulk-inquiry-section').slideUp(200, function() {
                    $('#individual-purchase-section').slideDown(200);
                });
                // Restore required on individual fields
                $('#individual-purchase-section .sse-was-required')
                    .prop('required', true).removeClass('sse-was-required');
                // Remove required from bulk fields
                $('#bulk-inquiry-section .bulk-required').prop('required', false);
            }
        });

        /* ──────────────────────────────────────────
         * ORDER BUMP — update price display
         * ────────────────────────────────────────── */
        $('#order-bump-checkbox').on('change', function() {
            if (!skillscoreEbook.enableOrderBump || !skillscoreEbook.orderBumpPrice) return;

            var basePrice  = parseFloat($('#sse-quantity').data('price')) || 0;
            var qty        = parseInt($('#sse-quantity').val()) || 1;
            var bumpPrice  = parseFloat(skillscoreEbook.orderBumpPrice) || 0;
            var total      = (basePrice * qty) + ($(this).is(':checked') ? bumpPrice : 0);

            var display = $('#sse-total-display');
            display.text('Total: ' + skillscoreEbook.currencySymbol + total.toFixed(2)).show();
        });

        /* ──────────────────────────────────────────
         * QUANTITY CHANGE — update total display
         * ────────────────────────────────────────── */
        $('#sse-quantity, input[name="quantity"]').on('input change', function() {
            var qty       = parseInt($(this).val()) || 1;
            var basePrice = parseFloat($(this).data('price')) || 0;
            var bumpAdded = $('#order-bump-checkbox').is(':checked');
            var bumpPrice = skillscoreEbook.enableOrderBump ? (parseFloat(skillscoreEbook.orderBumpPrice) || 0) : 0;
            var total     = (basePrice * qty) + (bumpAdded ? bumpPrice : 0);

            if (basePrice > 0) {
                $('#sse-total-display').text('Total: ' + skillscoreEbook.currencySymbol + total.toFixed(2)).show();
            }

            // Legacy total-price-display support
            if ($('.total-price-display').length) {
                $('.total-price-display').text(skillscoreEbook.currencySymbol + total.toFixed(2));
            }
        });

        /* ──────────────────────────────────────────
         * FORM SUBMISSION — individual + bulk paths
         * ────────────────────────────────────────── */
        $('#ebook-purchase-form').on('submit', function(e) {
            e.preventDefault();

            var $form     = $(this);
            var orderType = $('#hidden-order-type').val() || 'individual';

            // Choose the correct active submit button
            var $button = orderType === 'bulk'
                ? $('#bulk-submit-btn')
                : $('#individual-submit-btn');

            var originalHtml = $button.html();

            // HTML5 validation
            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            var loadingHtml = '<svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                '</svg> Processing...';

            $button.prop('disabled', true).html(loadingHtml);

            // Build form data from the active section
            var formData = {
                action:     'skillscore_initiate_payment',
                nonce:      skillscoreEbook.nonce,
                ebook_id:   $form.find('input[name="ebook_id"]').val(),
                order_type: orderType,
            };

            if (orderType === 'bulk') {
                // Bulk inquiry fields — use the bulk section's name/email
                formData.user_name       = $form.find('input[name="bulk_user_name"]').val();
                formData.user_email      = $form.find('input[name="bulk_user_email"]').val();
                formData.organization    = $form.find('input[name="organization"]').val();
                formData.user_phone      = $form.find('input[name="user_phone"]').last().val();
                formData.bulk_quantity   = $form.find('input[name="bulk_quantity"]').val();
                formData.bulk_message    = $form.find('textarea[name="bulk_message"]').val();
                formData.shipping_address = $form.find('input[name="shipping_address"]').val();
                formData.shipping_city   = $form.find('input[name="shipping_city"]').val();
                formData.shipping_state  = $form.find('input[name="shipping_state"]').val();
                formData.shipping_country = $form.find('input[name="shipping_country"]').val();
                formData.shipping_zip    = $form.find('input[name="shipping_zip"]').val();
            } else {
                // Individual purchase fields
                formData.quantity     = $form.find('input[name="quantity"]').val() || 1;
                formData.user_name    = $form.find('input[name="user_name"]').first().val();
                formData.user_email   = $form.find('input[name="user_email"]').first().val();
                formData.user_phone   = $form.find('input[name="user_phone"]').first().val();
                formData.gateway      = $form.find('input[name="gateway"]:checked').val();
                formData.order_format = $('#hidden-order-format').val() || 'ebook';
                formData.order_bump   = $('#order-bump-checkbox').is(':checked') ? 1 : 0;
                formData.shipping_address = $form.find('input[name="shipping_address"]').val();
                formData.shipping_city    = $form.find('input[name="shipping_city"]').val();
                formData.shipping_state   = $form.find('input[name="shipping_state"]').val();
                formData.shipping_country = $form.find('input[name="shipping_country"]').val();
                formData.shipping_zip     = $form.find('input[name="shipping_zip"]').val();
            }

            $.ajax({
                url:  skillscoreEbook.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        if (response.data.bulk_inquiry) {
                            // Bulk inquiry submitted — show inline success
                            showMessage('success', response.data.message || 'Your inquiry has been received.');
                            $('#bulk-inquiry-section').slideUp(300);
                            $button.prop('disabled', false).html(originalHtml);
                        } else if (response.data.redirect_url) {
                            // Individual payment — redirect to gateway
                            window.location.href = response.data.redirect_url;
                        } else {
                            showMessage('error', response.data.message || 'An unexpected error occurred.');
                            $button.prop('disabled', false).html(originalHtml);
                        }
                    } else {
                        showMessage('error', response.data.message || 'Payment initiation failed.');
                        $button.prop('disabled', false).html(originalHtml);
                    }
                },
                error: function() {
                    showMessage('error', 'An error occurred. Please try again.');
                    $button.prop('disabled', false).html(originalHtml);
                }
            });
        });

        /**
         * Load audio preview
         */
        $('#load-audio-preview').on('click', function(e) {
            e.preventDefault();

            var $button = $(this);
            var ebookId = $button.data('ebook-id');
            var originalText = $button.html();

            // Disable button and show loading
            $button.prop('disabled', true).html('Loading...');

            $.ajax({
                url: skillscoreEbook.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'skillscore_get_audio_preview',
                    nonce: skillscoreEbook.nonce,
                    ebook_id: ebookId
                },
                success: function(response) {
                    if (response.success && response.data.audio_url) {
                        if (response.data.audio_url === 'browser_tts') {
                            // Use browser TTS (Web Speech API)
                            useBrowserTTS(ebookId);
                        } else {
                            // Load audio file
                            $('#audio-preview-source').attr('src', response.data.audio_url);
                            $('#audio-preview-player').removeClass('hidden');
                            $('#audio-preview-player audio')[0].load();
                            $button.hide();
                        }
                    } else {
                        showMessage('error', response.data.message || 'Failed to load audio preview.');
                        $button.prop('disabled', false).html(originalText);
                    }
                },
                error: function() {
                    showMessage('error', 'An error occurred while loading audio preview.');
                    $button.prop('disabled', false).html(originalText);
                }
            });
        });

        /**
         * Browser-based TTS using Web Speech API
         */
        function useBrowserTTS(ebookId) {
            if ('speechSynthesis' in window) {
                // Get preview text from meta or excerpt
                var previewText = $('.ebook-description').first().text().substring(0, 500);

                if (previewText) {
                    var utterance = new SpeechSynthesisUtterance(previewText);
                    utterance.rate = 0.9;
                    utterance.pitch = 1.0;

                    // Show custom player controls
                    var playerHtml = '<div class="browser-tts-player">' +
                        '<button id="tts-play" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded mr-2">Play</button>' +
                        '<button id="tts-pause" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded mr-2" disabled>Pause</button>' +
                        '<button id="tts-stop" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" disabled>Stop</button>' +
                        '</div>';

                    $('#audio-preview-player').html(playerHtml).removeClass('hidden');
                    $('#load-audio-preview').hide();

                    // Play button
                    $('#tts-play').on('click', function() {
                        window.speechSynthesis.speak(utterance);
                        $(this).prop('disabled', true);
                        $('#tts-pause, #tts-stop').prop('disabled', false);
                    });

                    // Pause button
                    $('#tts-pause').on('click', function() {
                        window.speechSynthesis.pause();
                    });

                    // Stop button
                    $('#tts-stop').on('click', function() {
                        window.speechSynthesis.cancel();
                        $('#tts-play').prop('disabled', false);
                        $('#tts-pause, #tts-stop').prop('disabled', true);
                    });

                    // Reset buttons when speech ends
                    utterance.onend = function() {
                        $('#tts-play').prop('disabled', false);
                        $('#tts-pause, #tts-stop').prop('disabled', true);
                    };
                } else {
                    showMessage('error', 'No preview text available.');
                }
            } else {
                showMessage('error', 'Your browser does not support text-to-speech.');
            }
        }

        /**
         * Show message to user
         */
        function showMessage(type, message) {
            var messageClass = type === 'error' ? 'bg-red-100 border-red-500 text-red-700' : 'bg-green-100 border-green-500 text-green-700';
            var messageHtml = '<div class="' + messageClass + ' border-l-4 p-4 mb-4 rounded" role="alert">' +
                '<p>' + message + '</p>' +
                '</div>';

            // Insert message at the top of the ebook single view
            $('.skillscore-ebook-single').prepend(messageHtml);

            // Auto-remove after 5 seconds
            setTimeout(function() {
                $('.skillscore-ebook-single > div[role="alert"]').first().fadeOut(function() {
                    $(this).remove();
                });
            }, 5000);
        }

        /**
         * Smooth scroll to purchase form
         */
        $('a[href="#purchase"]').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('#ebook-purchase-form').offset().top - 100
            }, 500);
        });


        /**
         * Copy download link functionality
         */
        $('.copy-download-link').on('click', function(e) {
            e.preventDefault();
            var link = $(this).data('link');

            if (navigator.clipboard) {
                navigator.clipboard.writeText(link).then(function() {
                    showMessage('success', 'Download link copied to clipboard!');
                });
            } else {
                // Fallback for older browsers
                var $temp = $('<input>');
                $('body').append($temp);
                $temp.val(link).select();
                document.execCommand('copy');
                $temp.remove();
                showMessage('success', 'Download link copied to clipboard!');
            }
        });
    });

})(jQuery);
