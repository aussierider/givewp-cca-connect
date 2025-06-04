
<?php
/**
 * Gutenberg Blocks for CCAvenue Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

class GiveWP_CCAvenue_Blocks {
    
    public function init() {
        add_action('init', array($this, 'register_blocks'));
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_block_assets'));
    }
    
    public function register_blocks() {
        // Register CCAvenue donation info block
        register_block_type('givewp-ccavenue/donation-info', array(
            'editor_script' => 'givewp-ccavenue-blocks',
            'render_callback' => array($this, 'render_donation_info_block'),
            'attributes' => array(
                'showPAN' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'showAddress' => array(
                    'type' => 'boolean',
                    'default' => true,
                ),
                'title' => array(
                    'type' => 'string',
                    'default' => 'Tax Exemption Information',
                ),
            ),
        ));
    }
    
    public function enqueue_block_assets() {
        wp_enqueue_script(
            'givewp-ccavenue-blocks',
            GIVEWP_CCAVENUE_PLUGIN_URL . 'assets/blocks.js',
            array('wp-blocks', 'wp-element', 'wp-editor'),
            GIVEWP_CCAVENUE_VERSION
        );
    }
    
    public function render_donation_info_block($attributes) {
        $show_pan = isset($attributes['showPAN']) ? $attributes['showPAN'] : true;
        $show_address = isset($attributes['showAddress']) ? $attributes['showAddress'] : true;
        $title = isset($attributes['title']) ? $attributes['title'] : 'Tax Exemption Information';
        
        ob_start();
        ?>
        <div class="givewp-ccavenue-block-container">
            <div class="givewp-ccavenue-block-header">
                <h3><?php echo esc_html($title); ?></h3>
                <p><?php _e('Information about CCAvenue payment gateway and tax benefits.', 'givewp-ccavenue'); ?></p>
            </div>
            
            <div class="givewp-ccavenue-block-content">
                <?php if ($show_pan): ?>
                <div class="givewp-ccavenue-info-item">
                    <h4><?php _e('PAN Number Benefits', 'givewp-ccavenue'); ?></h4>
                    <p><?php _e('Providing your PAN number allows us to generate a tax exemption certificate for your donation under Section 80G of the Income Tax Act.', 'givewp-ccavenue'); ?></p>
                </div>
                <?php endif; ?>
                
                <?php if ($show_address): ?>
                <div class="givewp-ccavenue-info-item">
                    <h4><?php _e('Address for Certificate', 'givewp-ccavenue'); ?></h4>
                    <p><?php _e('Your address will be used to mail or courier the physical tax exemption certificate to your location.', 'givewp-ccavenue'); ?></p>
                </div>
                <?php endif; ?>
                
                <div class="givewp-ccavenue-info-item">
                    <h4><?php _e('Secure Payment Processing', 'givewp-ccavenue'); ?></h4>
                    <p><?php _e('All payments are processed securely through CCAvenue, one of India\'s leading payment gateways.', 'givewp-ccavenue'); ?></p>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
