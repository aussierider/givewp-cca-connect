
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
 * GiveWP tested up to: 3.0
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

/**
 * Main plugin class
 */
class GiveWP_CCAvenue_Gateway_Main {
    
    /**
     * Instance of this class
     */
    private static $instance;
    
    /**
     * Get instance
     */
    public static function get_instance() {
        if (!isset(self::$instance) && !(self::$instance instanceof GiveWP_CCAvenue_Gateway_Main)) {
            self::$instance = new GiveWP_CCAvenue_Gateway_Main();
        }
        return self::$instance;
    }
    
    /**
     * Setup the plugin
     */
    private function __construct() {
        add_action('plugins_loaded', array($this, 'init'), 999);
        register_activation_hook(__FILE__, array($this, 'activation_check'));
    }
    
    /**
     * Initialize the plugin
     */
    public function init() {
        // Check if GiveWP is active
        if (!$this->check_givewp()) {
            add_action('admin_notices', array($this, 'givewp_missing_notice'));
            return;
        }
        
        // Load text domain
        add_action('init', array($this, 'load_textdomain'));
        
        // Include files
        $this->includes();
        
        // Initialize components
        $this->init_components();
    }
    
    /**
     * Check if GiveWP is active and meets requirements
     */
    private function check_givewp() {
        if (!class_exists('Give')) {
            return false;
        }
        
        // Check GiveWP version
        if (version_compare(GIVE_VERSION, '2.5.0', '<')) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-gateway.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/admin-settings.php';
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/modern-form-fields.php';
    }
    
    /**
     * Initialize components
     */
    private function init_components() {
        // Register gateway
        add_filter('give_payment_gateways', array($this, 'register_gateway'));
        
        // Add custom fields to forms
        add_action('give_donation_form_before_submit', array($this, 'add_custom_fields'), 10, 1);
        add_action('give_insert_payment', array($this, 'save_custom_fields'), 10, 2);
        
        // Enqueue assets
        add_action('wp_enqueue_scripts', array($this, 'enqueue_frontend_assets'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
    }
    
    /**
     * Register the CCAvenue gateway
     */
    public function register_gateway($gateways) {
        $gateways['ccavenue'] = array(
            'admin_label'    => __('CCAvenue', 'givewp-ccavenue'),
            'checkout_label' => give_get_option('ccavenue_checkout_label', __('Credit/Debit Card & Net Banking', 'givewp-ccavenue')),
        );
        return $gateways;
    }
    
    /**
     * Add custom fields to donation forms
     */
    public function add_custom_fields($form_id) {
        if (!class_exists('GiveWP_CCAvenue_Modern_Fields')) {
            return;
        }
        
        $fields = new GiveWP_CCAvenue_Modern_Fields();
        $fields->render_fields($form_id);
    }
    
    /**
     * Save custom field data
     */
    public function save_custom_fields($payment_id, $payment_data) {
        if (isset($_POST['give_pan_number']) && !empty($_POST['give_pan_number'])) {
            give_update_payment_meta($payment_id, 'give_pan_number', sanitize_text_field($_POST['give_pan_number']));
        }
        
        if (isset($_POST['give_address']) && !empty($_POST['give_address'])) {
            give_update_payment_meta($payment_id, 'give_address', sanitize_textarea_field($_POST['give_address']));
        }
    }
    
    /**
     * Enqueue frontend assets
     */
    public function enqueue_frontend_assets() {
        if (!wp_script_is('jquery', 'enqueued')) {
            wp_enqueue_script('jquery');
        }
        
        wp_enqueue_style(
            'givewp-ccavenue-frontend',
            GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/modern-style.css',
            array(),
            GIVEWP_CCAVENUE_VERSION
        );
        
        wp_enqueue_script(
            'givewp-ccavenue-frontend',
            GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/modern-script.js',
            array('jquery'),
            GIVEWP_CCAVENUE_VERSION,
            true
        );
        
        wp_localize_script('givewp-ccavenue-frontend', 'giveCCAvenue', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('givewp_ccavenue_nonce'),
        ));
    }
    
    /**
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook) {
        // Only load on Give admin pages
        if (strpos($hook, 'give') === false) {
            return;
        }
        
        wp_enqueue_style(
            'givewp-ccavenue-admin',
            GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/admin-style.css',
            array(),
            GIVEWP_CCAVENUE_VERSION
        );
    }
    
    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'givewp-ccavenue',
            false,
            dirname(plugin_basename(__FILE__)) . '/languages/'
        );
    }
    
    /**
     * Admin notice for missing GiveWP
     */
    public function givewp_missing_notice() {
        echo '<div class="notice notice-error"><p>';
        echo sprintf(
            __('GiveWP CCAvenue Gateway requires GiveWP version %s or higher to be installed and active.', 'givewp-ccavenue'),
            '2.5.0'
        );
        echo '</p></div>';
    }
    
    /**
     * Check requirements on activation
     */
    public function activation_check() {
        if (!$this->check_givewp()) {
            deactivate_plugins(plugin_basename(__FILE__));
            wp_die(__('This plugin requires GiveWP to be installed and active.', 'givewp-ccavenue'));
        }
    }
}

/**
 * Get the main plugin instance
 */
function givewp_ccavenue_gateway() {
    return GiveWP_CCAvenue_Gateway_Main::get_instance();
}

// Initialize the plugin
givewp_ccavenue_gateway();
