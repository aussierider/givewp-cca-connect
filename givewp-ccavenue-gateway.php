<?php
/**
 * Plugin Name: GiveWP CCAvenue Gateway
 * Plugin URI: https://yourwebsite.com
 * Description: Modern CCAvenue payment gateway integration for GiveWP with tax exemption features
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
        
        // Modern form integration
        add_action('give_donation_form_before_submit', array($this, 'add_modern_fields'), 10, 1);
        add_action('give_insert_payment', array($this, 'save_custom_fields'), 10, 2);
        
        // Enqueue modern scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_modern_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        
        // Register Gutenberg block
        add_action('init', array($this, 'register_blocks'));
        
        // Register shortcodes
        add_action('init', array($this, 'register_shortcodes'));
    }
    
    public function includes() {
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-gateway.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/admin-settings.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/modern-form-fields.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/gutenberg-blocks.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/shortcodes.php';
    }
    
    public function register_gateway($gateways) {
        $gateways['ccavenue'] = array(
            'admin_label'    => __('CCAvenue', 'givewp-ccavenue'),
            'checkout_label' => give_get_option('ccavenue_checkout_label', __('Credit Card / Debit Card / Net Banking', 'givewp-ccavenue')),
        );
        return $gateways;
    }
    
    public function add_modern_fields($form_id) {
        $modern_fields = new GiveWP_CCAvenue_Modern_Fields();
        $modern_fields->render_fields($form_id);
    }
    
    public function save_custom_fields($payment_id, $payment_data) {
        if (isset($_POST['give_pan_number']) && !empty($_POST['give_pan_number'])) {
            give_update_payment_meta($payment_id, 'give_pan_number', sanitize_text_field($_POST['give_pan_number']));
        }
        
        if (isset($_POST['give_address']) && !empty($_POST['give_address'])) {
            give_update_payment_meta($payment_id, 'give_address', sanitize_textarea_field($_POST['give_address']));
        }
    }
    
    public function enqueue_modern_assets() {
        wp_enqueue_style('givewp-ccavenue-modern', GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/modern-style.css', array(), GIVEWP_CCAVENUE_VERSION);
        wp_enqueue_script('givewp-ccavenue-modern', GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/modern-script.js', array('jquery'), GIVEWP_CCAVENUE_VERSION, true);
        
        wp_localize_script('givewp-ccavenue-modern', 'giveCCAvenue', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('givewp_ccavenue_nonce'),
        ));
    }
    
    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'give') !== false) {
            wp_enqueue_style('givewp-ccavenue-admin', GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/admin-style.css', array(), GIVEWP_CCAVENUE_VERSION);
        }
    }
    
    public function register_blocks() {
        $blocks = new GiveWP_CCAvenue_Blocks();
        $blocks->init();
    }
    
    public function register_shortcodes() {
        $shortcodes = new GiveWP_CCAvenue_Shortcodes();
        $shortcodes->init();
    }
    
    public function give_missing_notice() {
        echo '<div class="notice notice-error"><p>' . __('GiveWP CCAvenue Gateway requires the GiveWP plugin to be installed and active.', 'givewp-ccavenue') . '</p></div>';
    }
    
    public function activate() {
        flush_rewrite_rules();
    }
    
    public function deactivate() {
        flush_rewrite_rules();
    }
}

// Initialize the plugin
new GiveWP_CCAvenue_Gateway();
