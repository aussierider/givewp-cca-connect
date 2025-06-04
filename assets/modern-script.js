
/**
 * Modern JavaScript for CCAvenue Gateway
 */

(function($) {
    'use strict';
    
    if (typeof $ === 'undefined') {
        return;
    }
    
    var CCAvenue = {
        
        init: function() {
            this.bindEvents();
            this.checkInitialState();
        },
        
        bindEvents: function() {
            // Show/hide fields based on gateway selection
            $(document).on('change', 'input[name="give-gateway"]', this.toggleFields);
            
            // PAN number formatting and validation
            $(document).on('input', '#give-pan-number', this.formatPAN);
            $(document).on('blur', '#give-pan-number', this.validatePAN);
        },
        
        checkInitialState: function() {
            // Check if CCAvenue is pre-selected
            var selectedGateway = $('input[name="give-gateway"]:checked').val();
            if (selectedGateway === 'ccavenue') {
                this.showFields();
            }
        },
        
        toggleFields: function() {
            var selectedGateway = $('input[name="give-gateway"]:checked').val();
            
            if (selectedGateway === 'ccavenue') {
                CCAvenue.showFields();
            } else {
                CCAvenue.hideFields();
            }
        },
        
        showFields: function() {
            var container = $('#givewp-ccavenue-fields');
            container.addClass('active').show();
        },
        
        hideFields: function() {
            var container = $('#givewp-ccavenue-fields');
            container.removeClass('active').hide();
        },
        
        formatPAN: function() {
            var value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // PAN format: ABCDE1234F (5 letters, 4 numbers, 1 letter)
            if (value.length > 5) {
                var letters = value.substring(0, 5).replace(/[^A-Z]/g, '');
                var numbers = value.substring(5, 9).replace(/[^0-9]/g, '');
                var lastLetter = value.substring(9, 10).replace(/[^A-Z]/g, '');
                value = letters + numbers + lastLetter;
            }
            
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            $(this).val(value);
        },
        
        validatePAN: function() {
            var value = $(this).val();
            var panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            var field = $(this);
            
            // Remove existing error messages
            field.removeClass('invalid');
            field.siblings('.givewp-ccavenue-error').remove();
            
            if (value && !panPattern.test(value)) {
                field.addClass('invalid');
                field.after('<span class="givewp-ccavenue-error">Please enter a valid PAN number (e.g., ABCDE1234F)</span>');
            }
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        CCAvenue.init();
    });
    
})(jQuery);
