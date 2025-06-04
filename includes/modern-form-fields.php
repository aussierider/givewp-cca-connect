
<?php
/**
 * Modern Form Fields for CCAvenue Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

class GiveWP_CCAvenue_Modern_Fields {
    
    public function render_fields($form_id) {
        ?>
        <div id="givewp-ccavenue-fields" class="givewp-ccavenue-container" data-form-id="<?php echo esc_attr($form_id); ?>">
            <div class="givewp-ccavenue-card">
                <div class="givewp-ccavenue-header">
                    <h3 class="givewp-ccavenue-title">
                        <?php _e('Tax Exemption Information', 'givewp-ccavenue'); ?>
                        <span class="givewp-ccavenue-optional"><?php _e('(Optional)', 'givewp-ccavenue'); ?></span>
                    </h3>
                    <p class="givewp-ccavenue-description">
                        <?php _e('Provide your details below to receive a tax exemption certificate for your donation.', 'givewp-ccavenue'); ?>
                    </p>
                </div>
                
                <div class="givewp-ccavenue-content">
                    <div class="givewp-ccavenue-field-group">
                        <label for="give-pan-number" class="givewp-ccavenue-label">
                            <?php _e('PAN Number', 'givewp-ccavenue'); ?>
                            <span class="givewp-ccavenue-tooltip" data-tooltip="<?php _e('Enter your PAN number for tax exemption certificate', 'givewp-ccavenue'); ?>">
                                <svg class="givewp-ccavenue-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m9,9 0,0 a3,3 0 1,1 6,0c0,2 -3,3 -3,3"></path>
                                    <path d="m12,17 0,.01"></path>
                                </svg>
                            </span>
                        </label>
                        <input 
                            type="text" 
                            name="give_pan_number" 
                            id="give-pan-number" 
                            class="givewp-ccavenue-input" 
                            placeholder="<?php _e('ABCDE1234F', 'givewp-ccavenue'); ?>"
                            pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}"
                            maxlength="10"
                        >
                        <div class="givewp-ccavenue-field-hint">
                            <?php _e('Enter your 10-digit PAN number (e.g., ABCDE1234F)', 'givewp-ccavenue'); ?>
                        </div>
                    </div>
                    
                    <div class="givewp-ccavenue-field-group">
                        <label for="give-address" class="givewp-ccavenue-label">
                            <?php _e('Address', 'givewp-ccavenue'); ?>
                        </label>
                        <textarea 
                            name="give_address" 
                            id="give-address" 
                            class="givewp-ccavenue-textarea" 
                            rows="3" 
                            placeholder="<?php _e('Enter your complete address for the tax exemption certificate', 'givewp-ccavenue'); ?>"
                        ></textarea>
                        <div class="givewp-ccavenue-field-hint">
                            <?php _e('This address will appear on your tax exemption certificate', 'givewp-ccavenue'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
