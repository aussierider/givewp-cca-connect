
<?php
/**
 * Modern Form Fields for CCAvenue Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

class GiveWP_CCAvenue_Modern_Fields {
    
    /**
     * Render the custom fields
     */
    public function render_fields($form_id) {
        // Only show fields if CCAvenue is enabled for this form
        $enabled_gateways = give_get_enabled_payment_gateways($form_id);
        
        if (!array_key_exists('ccavenue', $enabled_gateways)) {
            return;
        }
        
        ?>
        <div id="givewp-ccavenue-fields" class="givewp-ccavenue-container" style="display: none;">
            <fieldset class="givewp-ccavenue-fieldset">
                <legend class="givewp-ccavenue-legend">
                    <?php _e('Tax Exemption Information', 'givewp-ccavenue'); ?>
                    <span class="givewp-ccavenue-optional"><?php _e('(Optional)', 'givewp-ccavenue'); ?></span>
                </legend>
                
                <p class="givewp-ccavenue-description">
                    <?php _e('Provide your details below to receive a tax exemption certificate for your donation.', 'givewp-ccavenue'); ?>
                </p>
                
                <div class="givewp-ccavenue-field-wrapper">
                    <label for="give-pan-number" class="givewp-ccavenue-label">
                        <?php _e('PAN Number', 'givewp-ccavenue'); ?>
                    </label>
                    <input 
                        type="text" 
                        name="give_pan_number" 
                        id="give-pan-number" 
                        class="givewp-ccavenue-input" 
                        placeholder="<?php esc_attr_e('ABCDE1234F', 'givewp-ccavenue'); ?>"
                        maxlength="10"
                    >
                    <small class="givewp-ccavenue-help">
                        <?php _e('Enter your 10-digit PAN number for tax exemption certificate', 'givewp-ccavenue'); ?>
                    </small>
                </div>
                
                <div class="givewp-ccavenue-field-wrapper">
                    <label for="give-address" class="givewp-ccavenue-label">
                        <?php _e('Address', 'givewp-ccavenue'); ?>
                    </label>
                    <textarea 
                        name="give_address" 
                        id="give-address" 
                        class="givewp-ccavenue-textarea" 
                        rows="3" 
                        placeholder="<?php esc_attr_e('Enter your complete address for the tax exemption certificate', 'givewp-ccavenue'); ?>"
                    ></textarea>
                    <small class="givewp-ccavenue-help">
                        <?php _e('This address will appear on your tax exemption certificate', 'givewp-ccavenue'); ?>
                    </small>
                </div>
            </fieldset>
        </div>
        <?php
    }
}
