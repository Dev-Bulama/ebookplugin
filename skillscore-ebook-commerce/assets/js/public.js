/**
 * SkillScore Ebook Commerce - Public JavaScript
 *
 * @package SkillScore_Ebook
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Handle purchase form submission
         */
        $('#ebook-purchase-form').on('submit', function(e) {
            e.preventDefault();

            var $form = $(this);
            var $button = $form.find('button[type="submit"]');
            var originalText = $button.html();

            // Validate form
            if (!$form[0].checkValidity()) {
                $form[0].reportValidity();
                return;
            }

            // Disable button and show loading
            $button.prop('disabled', true).html(
                '<svg class="animate-spin h-5 w-5 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                '<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>' +
                '<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                '</svg> Processing...'
            );

            // Prepare data
            var formData = {
                action: 'skillscore_initiate_payment',
                nonce: skillscoreEbook.nonce,
                ebook_id: $form.find('input[name="ebook_id"]').val(),
                quantity: $form.find('input[name="quantity"]').val() || 1,
                user_name: $form.find('input[name="user_name"]').val(),
                user_email: $form.find('input[name="user_email"]').val(),
                gateway: $form.find('input[name="gateway"]:checked').val()
            };

            // Send AJAX request
            $.ajax({
                url: skillscoreEbook.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success && response.data.redirect_url) {
                        // Redirect to payment gateway
                        window.location.href = response.data.redirect_url;
                    } else {
                        showMessage('error', response.data.message || 'Payment initiation failed.');
                        $button.prop('disabled', false).html(originalText);
                    }
                },
                error: function(xhr, status, error) {
                    showMessage('error', 'An error occurred. Please try again.');
                    $button.prop('disabled', false).html(originalText);
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
         * Update quantity price display
         */
        $('input[name="quantity"]').on('input', function() {
            var quantity = parseInt($(this).val()) || 1;
            var basePrice = parseFloat($(this).data('price')) || 0;
            var totalPrice = quantity * basePrice;

            if ($('.total-price-display').length) {
                $('.total-price-display').text(skillscoreEbook.currencySymbol + totalPrice.toFixed(2));
            }
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
