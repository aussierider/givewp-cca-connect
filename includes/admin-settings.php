
<?php
/**
 * Admin Settings for CCAvenue Gateway
 */

if (!defined('ABSPATH')) {
    exit;
}

// Add settings section
add_filter('give_get_sections_gateways', 'give_ccavenue_add_settings_section');
function give_ccavenue_add_settings_section($sections) {
    $sections['ccavenue'] = __('CCAvenue', 'givewp-ccavenue');
    return $sections;
}

// Add settings
add_filter('give_get_settings_gateways', 'give_ccavenue_add_settings', 10, 1);
function give_ccavenue_add_settings($settings) {
    switch (give_get_current_setting_section()) {
        case 'ccavenue':
            $settings = array(
                array(
                    'id'   => 'give_title_ccavenue',
                    'type' => 'title',
                ),
                array(
                    'name' => __('Merchant ID', 'givewp-ccavenue'),
                    'desc' => __('Enter your CCAvenue Merchant ID', 'givewp-ccavenue'),
                    'id'   => 'ccavenue_merchant_id',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Access Code', 'givewp-ccavenue'),
                    'desc' => __('Enter your CCAvenue Access Code', 'givewp-ccavenue'),
                    'id'   => 'ccavenue_access_code',
                    'type' => 'text',
                ),
                array(
                    'name' => __('Working Key', 'givewp-ccavenue'),
                    'desc' => __('Enter your CCAvenue Working Key', 'givewp-ccavenue'),
                    'id'   => 'ccavenue_working_key',
                    'type' => 'password',
                ),
                array(
                    'name'    => __('Payment Method Label', 'givewp-ccavenue'),
                    'desc'    => __('This is the label that users will see during checkout.', 'givewp-ccavenue'),
                    'id'      => 'ccavenue_checkout_label',
                    'type'    => 'text',
                    'default' => __('Credit Card / Debit Card / Net Banking', 'givewp-ccavenue'),
                ),
                array(
                    'id'   => 'give_title_ccavenue',
                    'type' => 'sectionend',
                ),
            );
            break;
    }
    
    return $settings;
}
