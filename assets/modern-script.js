
/**
 * Modern JavaScript for CCAvenue Gateway
 */

(function($) {
    'use strict';
    
    const CCAvenue = {
        
        init: function() {
            this.bindEvents();
            this.initTooltips();
            this.validateForms();
        },
        
        bindEvents: function() {
            // Show/hide fields based on gateway selection
            $(document).on('change', 'input[name="give-gateway"]', this.toggleFields);
            
            // PAN number formatting
            $(document).on('input', '#give-pan-number', this.formatPAN);
            
            // Form validation
            $(document).on('blur', '#give-pan-number', this.validatePAN);
        },
        
        toggleFields: function() {
            const container = $('#givewp-ccavenue-fields');
            const selectedGateway = $('input[name="give-gateway"]:checked').val();
            
            if (selectedGateway === 'ccavenue') {
                container.addClass('active').slideDown(300);
            } else {
                container.removeClass('active').slideUp(300);
            }
        },
        
        formatPAN: function() {
            let value = $(this).val().toUpperCase().replace(/[^A-Z0-9]/g, '');
            
            // PAN format: ABCDE1234F
            if (value.length > 5) {
                value = value.substring(0, 5) + value.substring(5).replace(/[^0-9]/g, '');
            }
            if (value.length > 9) {
                value = value.substring(0, 9) + value.substring(9).replace(/[^A-Z]/g, '');
            }
            if (value.length > 10) {
                value = value.substring(0, 10);
            }
            
            $(this).val(value);
        },
        
        validatePAN: function() {
            const value = $(this).val();
            const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            const fieldGroup = $(this).closest('.givewp-ccavenue-field-group');
            
            // Remove existing validation messages
            fieldGroup.find('.validation-message').remove();
            
            if (value && !panPattern.test(value)) {
                $(this).addClass('invalid');
                fieldGroup.append('<div class="validation-message error">Please enter a valid PAN number (e.g., ABCDE1234F)</div>');
            } else {
                $(this).removeClass('invalid');
            }
        },
        
        initTooltips: function() {
            // Enhanced tooltips for better accessibility
            $('.givewp-ccavenue-tooltip').each(function() {
                const tooltip = $(this);
                const text = tooltip.data('tooltip');
                
                tooltip.attr('aria-label', text);
                tooltip.attr('role', 'button');
                tooltip.attr('tabindex', '0');
                
                // Keyboard support
                tooltip.on('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        tooltip.trigger('mouseenter');
                    }
                });
            });
        },
        
        validateForms: function() {
            // Real-time form validation
            $(document).on('submit', 'form[id*="give-form"]', function(e) {
                const form = $(this);
                const gateway = form.find('input[name="give-gateway"]:checked').val();
                
                if (gateway === 'ccavenue') {
                    const panField = form.find('#give-pan-number');
                    const panValue = panField.val();
                    
                    if (panValue && !CCAvenue.isValidPAN(panValue)) {
                        e.preventDefault();
                        panField.focus();
                        CCAvenue.showNotification('Please enter a valid PAN number', 'error');
                        return false;
                    }
                }
            });
        },
        
        isValidPAN: function(pan) {
            const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            return panPattern.test(pan);
        },
        
        showNotification: function(message, type) {
            // Create notification element
            const notification = $('<div class="givewp-ccavenue-notification ' + type + '">' + message + '</div>');
            
            // Add to page
            $('body').append(notification);
            
            // Animate in
            setTimeout(function() {
                notification.addClass('show');
            }, 100);
            
            // Remove after 5 seconds
            setTimeout(function() {
                notification.removeClass('show');
                setTimeout(function() {
                    notification.remove();
                }, 300);
            }, 5000);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        CCAvenue.init();
        
        // Check initial state
        if ($('input[name="give-gateway"]:checked').val() === 'ccavenue') {
            $('#givewp-ccavenue-fields').addClass('active');
        }
    });
    
})(jQuery);
