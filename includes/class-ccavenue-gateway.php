
<?php
/**
 * CCAvenue Payment Gateway Class
 */

if (!defined('ABSPATH')) {
    exit;
}

class Give_CCAvenue_Gateway extends Give_Payment_Gateway {
    
    public function __construct() {
        $this->id = 'ccavenue';
        
        parent::__construct();
    }
    
    public function init() {
        add_action("give_gateway_{$this->id}", array($this, 'process_payment'));
        add_action("give_{$this->id}_cc_form", array($this, 'cc_form'));
        add_action('init', array($this, 'handle_ccavenue_response'));
        add_action('wp_loaded', array($this, 'handle_ccavenue_response'));
    }
    
    public function cc_form($form_id) {
        // CCAvenue doesn't need credit card form as it redirects to their page
        return;
    }
    
    public function process_payment($purchase_data) {
        // Validate nonce
        give_validate_nonce($purchase_data['gateway_nonce'], 'give-gateway');
        
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
            give_record_gateway_error(__('Payment Error', 'givewp-ccavenue'), sprintf(__('Payment creation failed before sending donor to CCAvenue. Payment data: %s', 'givewp-ccavenue'), json_encode($payment_data)), $payment_id);
            give_send_back_to_checkout('?payment-mode=' . $purchase_data['post_data']['give-gateway']);
        }
        
        // Redirect to CCAvenue
        $this->redirect_to_ccavenue($payment_id, $purchase_data);
    }
    
    private function redirect_to_ccavenue($payment_id, $purchase_data) {
        $ccavenue_args = $this->get_ccavenue_args($payment_id, $purchase_data);
        
        $working_key = give_get_option('ccavenue_working_key');
        $access_code = give_get_option('ccavenue_access_code');
        
        if (empty($working_key) || empty($access_code)) {
            give_record_gateway_error(__('CCAvenue Configuration Error', 'givewp-ccavenue'), __('Working Key or Access Code is missing.', 'givewp-ccavenue'), $payment_id);
            give_send_back_to_checkout('?payment-mode=' . $this->id);
            return;
        }
        
        // Encrypt the data
        require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
        $crypto = new CCAvenue_Crypto();
        $encrypted_data = $crypto->encrypt(http_build_query($ccavenue_args), $working_key);
        
        $ccavenue_url = give_is_test_mode() ? 'https://test.ccavenue.com/transaction/transaction.do?command=initiateTransaction' : 'https://secure.ccavenue.com/transaction/transaction.do?command=initiateTransaction';
        
        // Create form for auto-submission
        echo '<html><body>';
        echo '<form id="ccavenue_form" method="post" action="' . $ccavenue_url . '">';
        echo '<input type="hidden" name="encRequest" value="' . $encrypted_data . '">';
        echo '<input type="hidden" name="access_code" value="' . $access_code . '">';
        echo '<script type="text/javascript">document.getElementById("ccavenue_form").submit();</script>';
        echo '</form>';
        echo '</body></html>';
        exit;
    }
    
    private function get_ccavenue_args($payment_id, $purchase_data) {
        $return_url = add_query_arg(array(
            'give-listener' => 'ccavenue',
            'payment-id'    => $payment_id,
        ), home_url('/'));
        
        $cancel_url = give_get_failed_transaction_uri();
        
        $args = array(
            'merchant_id'     => give_get_option('ccavenue_merchant_id'),
            'order_id'        => $payment_id,
            'amount'          => $purchase_data['price'],
            'currency'        => give_get_currency(),
            'redirect_url'    => $return_url,
            'cancel_url'      => $cancel_url,
            'language'        => 'EN',
            'billing_name'    => $purchase_data['user_info']['first_name'] . ' ' . $purchase_data['user_info']['last_name'],
            'billing_email'   => $purchase_data['user_email'],
            'billing_tel'     => isset($purchase_data['user_info']['phone']) ? $purchase_data['user_info']['phone'] : '',
            'delivery_name'   => $purchase_data['user_info']['first_name'] . ' ' . $purchase_data['user_info']['last_name'],
            'delivery_email'  => $purchase_data['user_email'],
        );
        
        // Add custom fields if present
        if (isset($_POST['give_address']) && !empty($_POST['give_address'])) {
            $args['billing_address'] = sanitize_textarea_field($_POST['give_address']);
            $args['delivery_address'] = sanitize_textarea_field($_POST['give_address']);
        }
        
        return apply_filters('give_ccavenue_args', $args, $payment_id, $purchase_data);
    }
    
    public function handle_ccavenue_response() {
        if (!isset($_GET['give-listener']) || $_GET['give-listener'] !== 'ccavenue') {
            return;
        }
        
        $payment_id = isset($_GET['payment-id']) ? intval($_GET['payment-id']) : 0;
        
        if (!$payment_id) {
            return;
        }
        
        $working_key = give_get_option('ccavenue_working_key');
        
        if (isset($_POST['encResp'])) {
            require_once GIVEWP_CCAVENUE_PLUGIN_DIR . 'includes/class-ccavenue-crypto.php';
            $crypto = new CCAvenue_Crypto();
            $decrypted_data = $crypto->decrypt($_POST['encResp'], $working_key);
            
            parse_str($decrypted_data, $response);
            
            if ($response['order_status'] === 'Success') {
                give_update_payment_status($payment_id, 'publish');
                give_insert_payment_note($payment_id, 'CCAvenue Transaction ID: ' . $response['tracking_id']);
                
                // Redirect to success page
                give_send_to_success_page();
            } else {
                give_update_payment_status($payment_id, 'failed');
                give_insert_payment_note($payment_id, 'CCAvenue Error: ' . $response['failure_message']);
                
                // Redirect to failed page
                wp_redirect(give_get_failed_transaction_uri());
                exit;
            }
        }
    }
}

new Give_CCAvenue_Gateway();
