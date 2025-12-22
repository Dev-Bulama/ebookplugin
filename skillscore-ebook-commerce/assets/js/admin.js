/**
 * SkillScore Ebook Commerce - Admin JavaScript
 *
 * @package SkillScore_Ebook
 */

(function($) {
    'use strict';

    $(document).ready(function() {

        /**
         * Toggle quantity field based on unlimited stock checkbox
         */
        $('#ebook_unlimited').on('change', function() {
            if ($(this).is(':checked')) {
                $('#quantity_row').slideUp();
            } else {
                $('#quantity_row').slideDown();
            }
        });

        /**
         * Remove ebook file
         */
        $('#remove-ebook-file').on('click', function() {
            if (confirm('Are you sure you want to remove this file?')) {
                $('.ebook-file-info').hide();
                $('.ebook-file-upload-form').show();
                $('#ebook_file_path').val('');
                $('#ebook_file_name').val('');
                $('#ebook_file_size').val('');
                $('#ebook_file_type').val('');
            }
        });

        /**
         * Confirm before revoking download access
         */
        $('.revoke-download').on('click', function(e) {
            if (!confirm('Are you sure you want to revoke download access? This action cannot be undone.')) {
                e.preventDefault();
            }
        });

        /**
         * Settings tab persistence
         */
        if (window.location.hash) {
            $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
            $('.nav-tab-wrapper .nav-tab[href="' + window.location.hash + '"]').addClass('nav-tab-active');
        }

        $('.nav-tab').on('click', function() {
            var target = $(this).attr('href');
            window.location.hash = target;
        });

        /**
         * File upload validation
         */
        $('input[type="file"][name="ebook_file"]').on('change', function() {
            var file = this.files[0];
            if (file) {
                // Check file size (50MB max)
                if (file.size > 52428800) {
                    alert('File size must not exceed 50MB.');
                    $(this).val('');
                    return;
                }

                // Check file type
                var allowedTypes = ['pdf', 'epub', 'docx'];
                var extension = file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(extension)) {
                    alert('Only PDF, EPUB, and DOCX files are allowed.');
                    $(this).val('');
                    return;
                }
            }
        });

        /**
         * Voice sample upload validation
         */
        $('input[type="file"][name="global_voice_sample"]').on('change', function() {
            var file = this.files[0];
            if (file) {
                // Check file type
                var allowedTypes = ['mp3', 'wav', 'ogg'];
                var extension = file.name.split('.').pop().toLowerCase();

                if (!allowedTypes.includes(extension)) {
                    alert('Only MP3, WAV, and OGG audio files are allowed.');
                    $(this).val('');
                    return;
                }

                // Check file size (10MB max for audio)
                if (file.size > 10485760) {
                    alert('Audio file size must not exceed 10MB.');
                    $(this).val('');
                    return;
                }
            }
        });

        /**
         * Auto-save indicator
         */
        var autoSaveTimeout;
        $('.form-table input, .form-table select, .form-table textarea').on('change', function() {
            clearTimeout(autoSaveTimeout);
            $('#autosave-indicator').remove();

            autoSaveTimeout = setTimeout(function() {
                var indicator = $('<span id="autosave-indicator" style="color: #10b981; margin-left: 10px;">Settings will be saved when you click "Save Changes"</span>');
                $('.submit').prepend(indicator);

                setTimeout(function() {
                    indicator.fadeOut(function() {
                        $(this).remove();
                    });
                }, 3000);
            }, 500);
        });

        /**
         * Payment gateway configuration toggle
         */
        $('input[name^="skillscore_ebook_enable_"]').on('change', function() {
            var gateway = $(this).attr('name').replace('skillscore_ebook_enable_', '');
            var $row = $(this).closest('tr');

            if ($(this).is(':checked')) {
                $row.nextUntil('tr:has(input[type="checkbox"])').slideDown();
            } else {
                $row.nextUntil('tr:has(input[type="checkbox"])').slideUp();
            }
        }).trigger('change');

        /**
         * TTS engine configuration toggle
         */
        $('select[name="skillscore_ebook_tts_engine"]').on('change', function() {
            var engine = $(this).val();

            // Hide all engine-specific fields
            $('input[name^="skillscore_ebook_piper"]').closest('tr').hide();
            $('input[name^="skillscore_ebook_coqui"]').closest('tr').hide();

            // Show relevant fields
            if (engine === 'piper') {
                $('input[name^="skillscore_ebook_piper"]').closest('tr').show();
            } else if (engine === 'coqui') {
                $('input[name^="skillscore_ebook_coqui"]').closest('tr').show();
            }

            // FFmpeg is needed for all engines except web_speech
            if (engine !== 'web_speech' && engine !== 'none') {
                $('input[name="skillscore_ebook_ffmpeg_path"]').closest('tr').show();
            } else {
                $('input[name="skillscore_ebook_ffmpeg_path"]').closest('tr').hide();
            }
        }).trigger('change');

        /**
         * Orders page - Filter functionality
         */
        $('#order-status-filter').on('change', function() {
            var status = $(this).val();
            var $rows = $('.wp-list-table tbody tr');

            if (status === 'all') {
                $rows.show();
            } else {
                $rows.hide();
                $rows.filter(':has(.status-' + status + ')').show();
            }
        });

        /**
         * Search orders
         */
        $('#order-search').on('keyup', function() {
            var searchTerm = $(this).val().toLowerCase();
            var $rows = $('.wp-list-table tbody tr');

            $rows.each(function() {
                var rowText = $(this).text().toLowerCase();
                if (rowText.indexOf(searchTerm) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        /**
         * Copy API keys to clipboard
         */
        $('.copy-api-key').on('click', function(e) {
            e.preventDefault();
            var $input = $(this).siblings('input');
            $input.select();
            document.execCommand('copy');

            // Show feedback
            var $feedback = $('<span class="copy-feedback" style="color: #10b981; margin-left: 10px;">Copied!</span>');
            $(this).after($feedback);

            setTimeout(function() {
                $feedback.fadeOut(function() {
                    $(this).remove();
                });
            }, 2000);
        });

        /**
         * Bulk actions for orders/downloads
         */
        $('#doaction, #doaction2').on('click', function(e) {
            var action = $(this).prev('select').val();
            var selected = $('input[name="items[]"]:checked').length;

            if (action !== '-1' && selected === 0) {
                e.preventDefault();
                alert('Please select at least one item.');
            }
        });

        /**
         * Toggle all checkboxes
         */
        $('#cb-select-all-1, #cb-select-all-2').on('change', function() {
            var isChecked = $(this).is(':checked');
            $('input[name="items[]"]').prop('checked', isChecked);
        });

        /**
         * Warn before leaving page with unsaved changes
         */
        var formChanged = false;

        $('.form-table input, .form-table select, .form-table textarea').on('change', function() {
            formChanged = true;
        });

        $('form').on('submit', function() {
            formChanged = false;
        });

        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });

        /**
         * Real-time password strength indicator
         */
        $('input[type="password"][name*="secret"], input[type="password"][name*="key"]').on('keyup', function() {
            var password = $(this).val();
            var strength = 0;

            if (password.length > 6) strength++;
            if (password.length > 10) strength++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
            if (/\d/.test(password)) strength++;
            if (/[^a-zA-Z\d]/.test(password)) strength++;

            var indicator = $(this).siblings('.password-strength');
            if (indicator.length === 0) {
                indicator = $('<div class="password-strength"></div>');
                $(this).after(indicator);
            }

            var strengthText = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
            var strengthColor = ['#dc2626', '#f59e0b', '#eab308', '#10b981', '#059669'];

            indicator.html('<span style="color: ' + strengthColor[strength] + ';">Strength: ' + strengthText[strength] + '</span>');
        });
    });

})(jQuery);
