
<?php
/**
 * Shortcodes for CCAvenue Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

class GiveWP_CCAvenue_Shortcodes {
    
    public function init() {
        add_shortcode('ccavenue_info', array($this, 'render_info_shortcode'));
        add_shortcode('ccavenue_benefits', array($this, 'render_benefits_shortcode'));
        add_shortcode('ccavenue_status', array($this, 'render_status_shortcode'));
    }
    
    public function render_info_shortcode($atts) {
        $atts = shortcode_atts(array(
            'title' => 'CCAvenue Payment Gateway',
            'show_benefits' => 'true',
            'show_security' => 'true',
        ), $atts);
        
        ob_start();
        ?>
        <div class="givewp-ccavenue-shortcode-info">
            <h3 class="givewp-ccavenue-shortcode-title"><?php echo esc_html($atts['title']); ?></h3>
            
            <?php if ($atts['show_benefits'] === 'true'): ?>
            <div class="givewp-ccavenue-benefit-list">
                <h4><?php _e('Tax Benefits Available', 'givewp-ccavenue'); ?></h4>
                <ul>
                    <li><?php _e('Tax exemption under Section 80G', 'givewp-ccavenue'); ?></li>
                    <li><?php _e('Instant payment confirmation', 'givewp-ccavenue'); ?></li>
                    <li><?php _e('Multiple payment options', 'givewp-ccavenue'); ?></li>
                    <li><?php _e('Secure transaction processing', 'givewp-ccavenue'); ?></li>
                </ul>
            </div>
            <?php endif; ?>
            
            <?php if ($atts['show_security'] === 'true'): ?>
            <div class="givewp-ccavenue-security-info">
                <h4><?php _e('Security Features', 'givewp-ccavenue'); ?></h4>
                <p><?php _e('Your donation is processed through CCAvenue\'s secure payment gateway with 256-bit SSL encryption.', 'givewp-ccavenue'); ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function render_benefits_shortcode($atts) {
        $atts = shortcode_atts(array(
            'layout' => 'grid', // grid or list
        ), $atts);
        
        ob_start();
        ?>
        <div class="givewp-ccavenue-benefits <?php echo esc_attr('layout-' . $atts['layout']); ?>">
            <div class="givewp-ccavenue-benefit-item">
                <div class="givewp-ccavenue-benefit-icon">💳</div>
                <h4><?php _e('Multiple Payment Options', 'givewp-ccavenue'); ?></h4>
                <p><?php _e('Credit Cards, Debit Cards, Net Banking, UPI, and Wallets', 'givewp-ccavenue'); ?></p>
            </div>
            
            <div class="givewp-ccavenue-benefit-item">
                <div class="givewp-ccavenue-benefit-icon">🛡️</div>
                <h4><?php _e('Secure Processing', 'givewp-ccavenue'); ?></h4>
                <p><?php _e('Bank-grade security with 256-bit SSL encryption', 'givewp-ccavenue'); ?></p>
            </div>
            
            <div class="givewp-ccavenue-benefit-item">
                <div class="givewp-ccavenue-benefit-icon">📄</div>
                <h4><?php _e('Tax Certificate', 'givewp-ccavenue'); ?></h4>
                <p><?php _e('Instant tax exemption certificate under Section 80G', 'givewp-ccavenue'); ?></p>
            </div>
            
            <div class="givewp-ccavenue-benefit-item">
                <div class="givewp-ccavenue-benefit-icon">⚡</div>
                <h4><?php _e('Instant Confirmation', 'givewp-ccavenue'); ?></h4>
                <p><?php _e('Real-time payment confirmation and receipt', 'givewp-ccavenue'); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    public function render_status_shortcode($atts) {
        $atts = shortcode_atts(array(
            'payment_id' => '',
        ), $atts);
        
        if (empty($atts['payment_id'])) {
            return '<p>' . __('Payment ID required to display status.', 'givewp-ccavenue') . '</p>';
        }
        
        $payment_id = intval($atts['payment_id']);
        $payment = give_get_payment($payment_id);
        
        if (!$payment) {
            return '<p>' . __('Payment not found.', 'givewp-ccavenue') . '</p>';
        }
        
        ob_start();
        ?>
        <div class="givewp-ccavenue-payment-status">
            <h4><?php _e('Payment Status', 'givewp-ccavenue'); ?></h4>
            <div class="givewp-ccavenue-status-details">
                <p><strong><?php _e('Payment ID:', 'givewp-ccavenue'); ?></strong> <?php echo esc_html($payment_id); ?></p>
                <p><strong><?php _e('Status:', 'givewp-ccavenue'); ?></strong> 
                    <span class="givewp-ccavenue-status-badge status-<?php echo esc_attr($payment->status); ?>">
                        <?php echo esc_html(give_get_payment_status($payment, true)); ?>
                    </span>
                </p>
                <p><strong><?php _e('Amount:', 'givewp-ccavenue'); ?></strong> <?php echo give_donation_amount($payment_id, true); ?></p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
