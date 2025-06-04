
<?php
/**
 * Plugin Name: GiveWP CCAvenue Gateway
 * Plugin URI: https://yourwebsite.com
 * Description: CCAvenue payment gateway integration for GiveWP with tax exemption features
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: givewp-ccavenue
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GIVEWP_CCAVENUE_VERSION', '1.0.0');
define('GIVEWP_CCAVENUE_PLUGIN_FILE', __FILE__);
define('GIVEWP_CCAVENUE_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('GIVEWP_CCAVENUE_PLUGIN_URL', plugin_dir_url(__FILE__));

// Main plugin class
class GiveWP_CCAvenue_Gateway {
    
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    public function init() {
        // Check if GiveWP is active
        if (!class_exists('Give')) {
            add_action('admin_notices', array($this, 'give_missing_notice'));
            return;
        }
        
        // Load plugin files
        $this->includes();
        
        // Initialize gateway
        add_action('give_register_payment_gateway', array($this, 'register_gateway'));
        
        // Add custom form fields
        add_action('give_donation_form_before_submit', array($this, 'add_custom_fields'), 10, 1);
        
        // Process custom fields
        add_action('give_insert_payment', array($this, 'save_custom_fields'), 10, 2);
        
        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }
    
    public function includes() {
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-gateway.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/admin-settings.php';
    }
    
    public function register_gateway($gateways) {
        $gateways['ccavenue'] = array(
            'admin_label'    => __('CCAvenue', 'givewp-ccavenue'),
            'checkout_label' => __('CCAvenue', 'givewp-ccavenue'),
        );
        return $gateways;
    }
    
    public function add_custom_fields($form_id) {
        ?>
        <div id="give-ccavenue-custom-fields" style="display: none;">
            <fieldset class="give-fieldset">
                <legend><?php _e('Tax Exemption Information (Optional)', 'givewp-ccavenue'); ?></legend>
                
                <div class="form-row">
                    <label class="give-label" for="give-pan-number">
                        <?php _e('PAN Number', 'givewp-ccavenue'); ?>
                        <span class="give-tooltip give-icon give-icon-question" data-tooltip="<?php _e('Enter your PAN number for tax exemption certificate', 'givewp-ccavenue'); ?>"></span>
                    </label>
                    <input type="text" name="give_pan_number" id="give-pan-number" class="give-input" placeholder="<?php _e('ABCDE1234F', 'givewp-ccavenue'); ?>">
                </div>
                
                <div class="form-row">
                    <label class="give-label" for="give-address">
                        <?php _e('Address (for tax exemption certificate)', 'givewp-ccavenue'); ?>
                    </label>
                    <textarea name="give_address" id="give-address" class="give-input" rows="3" placeholder="<?php _e('Enter your complete address', 'givewp-ccavenue'); ?>"></textarea>
                </div>
            </fieldset>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            // Show custom fields only when CCAvenue is selected
            $('input[name="give-gateway"]').on('change', function() {
                if ($(this).val() === 'ccavenue') {
                    $('#give-ccavenue-custom-fields').show();
                } else {
                    $('#give-ccavenue-custom-fields').hide();
                }
            });
            
            // Check initial state
            if ($('input[name="give-gateway"]:checked').val() === 'ccavenue') {
                $('#give-ccavenue-custom-fields').show();
            }
        });
        </script>
        <?php
    }
    
    public function save_custom_fields($payment_id, $payment_data) {
        if (isset($_POST['give_pan_number']) && !empty($_POST['give_pan_number'])) {
            give_update_payment_meta($payment_id, 'give_pan_number', sanitize_text_field($_POST['give_pan_number']));
        }
        
        if (isset($_POST['give_address']) && !empty($_POST['give_address'])) {
            give_update_payment_meta($payment_id, 'give_address', sanitize_textarea_field($_POST['give_address']));
        }
    }
    
    public function enqueue_scripts() {
        if (function_exists('give_is_donation_form')) {
            wp_enqueue_style('givewp-ccavenue-style', GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/style.css', array(), GIVEWP_CCAVENUE_VERSION);
        }
    }
    
    public function give_missing_notice() {
        echo '<div class="notice notice-error"><p>' . __('GiveWP CCAvenue Gateway requires the GiveWP plugin to be installed and active.', 'givewp-ccavenue') . '</p></div>';
    }
    
    public function activate() {
        // Activation tasks
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        // Deactivation tasks
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new GiveWP_CCAvenue_Gateway();
