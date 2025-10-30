/**
 * Featured Image Generator - Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        /**
         * Test API Connection
         */
        $('#fig-test-api-btn').on('click', function(e) {
            e.preventDefault();
            
            var $button = $(this);
            var $result = $('#fig-api-test-result');
            var apiKey = $('input[name="fig_settings[unsplash_api_key]"]').val();
            
            if (!apiKey) {
                $result.html('<span style="color: red;">⚠ ' + figAjax.strings.error + '</span>');
                return;
            }
            
            $button.prop('disabled', true);
            $result.html('<span style="color: #666;">⏳ ' + figAjax.strings.testing + '</span>');
            
            $.ajax({
                url: figAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fig_test_api',
                    nonce: figAjax.nonce,
                    api_key: apiKey
                },
                success: function(response) {
                    if (response.success) {
                        $result.html('<span style="color: green;">✓ ' + figAjax.strings.success + '</span>');
                    } else {
                        $result.html('<span style="color: red;">✗ ' + figAjax.strings.error + '</span>');
                    }
                },
                error: function() {
                    $result.html('<span style="color: red;">✗ ' + figAjax.strings.error_occurred + '</span>');
                },
                complete: function() {
                    $button.prop('disabled', false);
                }
            });
        });
        
        /**
         * Bulk Generate Featured Images
         */
        $('#fig-bulk-generate-btn').on('click', function(e) {
            e.preventDefault();
            
            if (!confirm('This will generate featured images for all posts that don\'t have one. Continue?')) {
                return;
            }
            
            var $button = $(this);
            var $progress = $('#fig-bulk-progress');
            var $progressText = $('#fig-progress-text');
            var $progressBar = $('#fig-progress-bar');
            var $progressLog = $('#fig-progress-log');
            
            $button.prop('disabled', true);
            $progress.show();
            $progressLog.html('');
            $progressLog.append('<p>Starting bulk generation...</p>');
            
            $.ajax({
                url: figAjax.ajaxurl,
                type: 'POST',
                data: {
                    action: 'fig_bulk_generate',
                    nonce: figAjax.nonce
                },
                success: function(response) {
                    if (response.success && response.data) {
                        var data = response.data;
                        
                        if (data.total === 0) {
                            $progressLog.append('<p><strong>✓ All posts already have featured images!</strong></p>');
                            $button.prop('disabled', false);
                            return;
                        }
                        
                        var percent = data.total > 0 ? (data.processed / data.total * 100).toFixed(0) : 0;
                        $progressBar.val(percent);
                        $progressText.text(data.processed + '/' + data.total);
                        
                        $progressLog.append('<p>' + data.message + '</p>');
                        $progressLog.scrollTop($progressLog[0].scrollHeight);
                        
                        if (data.complete) {
                            $progressLog.append('<p><strong>✓ ' + figAjax.strings.complete + '</strong></p>');
                            $button.prop('disabled', false);
                        }
                    } else {
                        $progressLog.append('<p style="color: red;">✗ ' + figAjax.strings.error_occurred + '</p>');
                        $button.prop('disabled', false);
                    }
                },
                error: function() {
                    $progressLog.append('<p style="color: red;">✗ ' + figAjax.strings.error_occurred + '</p>');
                    $button.prop('disabled', false);
                }
            });
        });
        
    });
    
})(jQuery);
