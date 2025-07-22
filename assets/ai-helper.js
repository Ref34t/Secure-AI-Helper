jQuery(document).ready(function($) {
    $('#sai-explain-btn').on('click', function() {
        var setting = $('#sai-setting-name').val().trim();
        
        if (!setting || setting.length < 2) {
            $('#sai-output').text('Please enter a valid setting name (at least 2 characters).');
            return;
        }

        if (setting.length > 200) {
            $('#sai-output').text('Setting name too long. Please keep it under 200 characters.');
            return;
        }

        $('#sai-output').text('Loading...');
        
        $.post(sai_ajax.ajax_url, {
            action: 'sai_get_ai_help',
            nonce: sai_ajax.nonce,
            setting: setting
        }, function(response) {
            if (response.success) {
                $('#sai-output').text(response.data);
            } else {
                $('#sai-output').text('Error: ' + (response.data || 'Unknown error occurred'));
            }
        }).fail(function() {
            $('#sai-output').text('Network error. Please check your connection and try again.');
        });
    });

    $('#sai-setting-name').on('keypress', function(e) {
        if (e.which === 13) {
            $('#sai-explain-btn').click();
        }
    });
});
