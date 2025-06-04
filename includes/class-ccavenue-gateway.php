
<?php
/**
 * CCAvenue Payment Gateway Class
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CCAvenue Gateway Class
 */
class Give_CCAvenue_Gateway {
    
    /**
     * Gateway ID
     */
    public $id = 'ccavenue';
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
    }
    
    /**
     * Initialize the gateway
     */
    public function init() {
        // Process payment action
        add_action("give_gateway_{$this->id}", array($this, 'process_payment'));
        
        // Handle CCAvenue response
        add_action('init', array($this, 'handle_ccavenue_response'));
        
        // Don't show CC form for CCAvenue
        add_action("give_{$this->id}_cc_form", array($this, 'cc_form'));
    }
    
    /**
     * CC Form - CCAvenue handles this on their end
     */
    public function cc_form() {
        // CCAvenue doesn't need a credit card form
        // Payment details are handled on CCAvenue's secure page
        return;
    }
    
    /**
     * Process the payment
     */
    public function process_payment($purchase_data) {
        // Validate the nonce
        if (!wp_verify_nonce($purchase_data['gateway_nonce'], 'give-gateway')) {
            give_record_gateway_error(__('Security Error', 'givewp-ccavenue'), __('Nonce verification failed.', 'givewp-ccavenue'));
            give_send_back_to_checkout('?payment-mode=' . $this->id);
            return;
        }
        
        // Setup payment data
        $payment_data = array(
            'price'           => $purchase_data['price'],
            'give_form_title' => $purchase_data['post_data']['give-form-title'],
            'give_form_id'    => intval($purchase_data['post_data']['give-form-id']),
            'give_price_id'   => isset($purchase_data['post_data']['give-price-id']) ? $purchase_data['post_data']['give-price-id'] : '',
            'date'            => $purchase_data['date'],
            'user_email'      => $purchase_data['user_email'],
            'purchase_key'    => $purchase_data['purchase_key'],
            'currency'        => give_get_currency(),
            'user_info'       => $purchase_data['user_info'],
            'status'          => 'pending',
            'gateway'         => $this->id,
        );
        
        // Record the pending payment
        $payment_id = give_insert_payment($payment_data);
        
        if (!$payment_id) {
            give_record_gateway_error(
                __('Payment Creation Error', 'givewp-ccavenue'),
                sprintf(__('Payment creation failed. Payment data: %s', 'givewp-ccavenue'), json_encode($payment_data)),
                $payment_id
            );
            give_send_back_to_checkout('?payment-mode=' . $this->id);
            return;
        }
        
        // Redirect to CCAvenue
        $this->redirect_to_ccavenue($payment_id, $purchase_data);
    }
    
    /**
     * Redirect to CCAvenue
     */
    private function redirect_to_ccavenue($payment_id, $purchase_data) {
        // Get CCAvenue settings
        $merchant_id = give_get_option('ccavenue_merchant_id');
        $access_code = give_get_option('ccavenue_access_code');
        $working_key = give_get_option('ccavenue_working_key');
        
        // Validate settings
        if (empty($merchant_id) || empty($access_code) || empty($working_key)) {
            give_record_gateway_error(
                __('CCAvenue Configuration Error', 'givewp-ccavenue'),
                __('CCAvenue settings are incomplete. Please check Merchant ID, Access Code, and Working Key.', 'givewp-ccavenue'),
                $payment_id
            );
            give_send_back_to_checkout('?payment-mode=' . $this->id);
            return;
        }
        
        // Prepare CCAvenue arguments
        $ccavenue_args = $this->get_ccavenue_args($payment_id, $purchase_data);
        
        // Encrypt the data
        if (!class_exists('CCAvenue_Crypto')) {
            require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
        }
        
        $crypto = new CCAvenue_Crypto();
        $encrypted_data = $crypto->encrypt(http_build_query($ccavenue_args), $working_key);
        
        // Determine CCAvenue URL
        $ccavenue_url = give_is_test_mode() 
            ? 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction'
            : 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
        
        // Auto-submit form to CCAvenue
        echo '<!DOCTYPE html>
        <html>
        <head>
            <title>' . __('Redirecting to CCAvenue...', 'givewp-ccavenue') . '</title>
        </head>
        <body>
            <div style="text-align: center; padding: 50px;">
                <h3>' . __('Please wait while we redirect you to CCAvenue...', 'givewp-ccavenue') . '</h3>
            </div>
            <form id="ccavenue_form" method="post" action="' . esc_url($ccavenue_url) . '">
                <input type="hidden" name="encRequest" value="' . esc_attr($encrypted_data) . '">
                <input type="hidden" name="access_code" value="' . esc_attr($access_code) . '">
            </form>
            <script type="text/javascript">
                document.getElementById("ccavenue_form").submit();
            </script>
        </body>
        </html>';
        exit;
    }
    
    /**
     * Get CCAvenue arguments
     */
    private function get_ccavenue_args($payment_id, $purchase_data) {
        $return_url = add_query_arg(array(
            'give-listener' => 'ccavenue',
            'payment-id'    => $payment_id,
        ), home_url('/'));
        
        $cancel_url = give_get_failed_transaction_uri();
        
        $args = array(
            'merchant_id'     => give_get_option('ccavenue_merchant_id'),
            'order_id'        => $payment_id,
            'amount'          => number_format($purchase_data['price'], 2, '.', ''),
            'currency'        => give_get_currency(),
            'redirect_url'    => $return_url,
            'cancel_url'      => $cancel_url,
            'language'        => 'EN',
            'billing_name'    => trim($purchase_data['user_info']['first_name'] . ' ' . $purchase_data['user_info']['last_name']),
            'billing_email'   => $purchase_data['user_email'],
        );
        
        // Add custom fields if present
        if (!empty($_POST['give_address'])) {
            $args['billing_address'] = sanitize_textarea_field($_POST['give_address']);
        }
        
        if (!empty($_POST['give_pan_number'])) {
            $args['merchant_param1'] = sanitize_text_field($_POST['give_pan_number']);
        }
        
        return apply_filters('give_ccavenue_args', $args, $payment_id, $purchase_data);
    }
    
    /**
     * Handle CCAvenue response
     */
    public function handle_ccavenue_response() {
        // Check if this is a CCAvenue response
        if (!isset($_GET['give-listener']) || $_GET['give-listener'] !== 'ccavenue') {
            return;
        }
        
        $payment_id = isset($_GET['payment-id']) ? intval($_GET['payment-id']) : 0;
        
        if (!$payment_id) {
            wp_redirect(home_url());
            exit;
        }
        
        // Get working key
        $working_key = give_get_option('ccavenue_working_key');
        
        if (empty($working_key)) {
            give_record_gateway_error(
                __('CCAvenue Response Error', 'givewp-ccavenue'),
                __('Working key is missing for response decryption.', 'givewp-ccavenue'),
                $payment_id
            );
            wp_redirect(give_get_failed_transaction_uri());
            exit;
        }
        
        // Process the response
        if (isset($_POST['encResp']) && !empty($_POST['encResp'])) {
            if (!class_exists('CCAvenue_Crypto')) {
                require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
            }
            
            $crypto = new CCAvenue_Crypto();
            $decrypted_data = $crypto->decrypt($_POST['encResp'], $working_key);
            
            if ($decrypted_data) {
                parse_str($decrypted_data, $response);
                
                if (isset($response['order_status']) && $response['order_status'] === 'Success') {
                    // Payment successful
                    give_update_payment_status($payment_id, 'publish');
                    
                    // Add payment note
                    $note = sprintf(
                        __('CCAvenue payment completed. Transaction ID: %s', 'givewp-ccavenue'),
                        isset($response['tracking_id']) ? $response['tracking_id'] : 'N/A'
                    );
                    give_insert_payment_note($payment_id, $note);
                    
                    // Redirect to success page
                    give_send_to_success_page();
                } else {
                    // Payment failed
                    give_update_payment_status($payment_id, 'failed');
                    
                    $error_message = isset($response['failure_message']) ? $response['failure_message'] : __('Payment failed', 'givewp-ccavenue');
                    give_insert_payment_note($payment_id, sprintf(__('CCAvenue Error: %s', 'givewp-ccavenue'), $error_message));
                    
                    wp_redirect(give_get_failed_transaction_uri());
                    exit;
                }
            } else {
                // Decryption failed
                give_record_gateway_error(
                    __('CCAvenue Response Error', 'givewp-ccavenue'),
                    __('Failed to decrypt CCAvenue response.', 'givewp-ccavenue'),
                    $payment_id
                );
                wp_redirect(give_get_failed_transaction_uri());
                exit;
            }
        }
    }
}

// Initialize the gateway
new Give_CCAvenue_Gateway();
