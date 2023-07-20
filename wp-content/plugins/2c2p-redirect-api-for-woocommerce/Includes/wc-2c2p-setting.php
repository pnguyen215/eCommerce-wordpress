 <?php

return array(    
    'enabled' => array(
        'title' => __('Enable/Disable', 'woo_2c2p'),
        'type' => 'checkbox',
        'label' => __('Enable 2c2p', 'woo_2c2p'),
        'default' => 'no',
        'description' => 'If ticked, It will show in the Payment List as a payment option'
    ),    
    'title' => array(
        'title' => __('Title', 'woo_2c2p'),
        'type' => 'text',
        'default' => __('Credit / Debit Card and Cash Payment (2C2P)', 'woo_2c2p'),
        'description' => __('This controls the title which the user sees during checkout.', 'woo_2c2p'),
        'desc_tip' => true
    ),    
    'description' => array(
        'title' => __('Description', 'woo_2c2p'),
        'type' => 'textarea',
        'default' => __('Pay Securely by Credit / Debit card or Internet banking through 2C2P.', 'woo_2c2p'),
        'description' => __('This controls the description which the user sees during checkout.', 'woo_2c2p'),
        'desc_tip' => true
    ),    
    'wc_2c2p_api_details' => array(
        'title'       => __( 'API credentials', 'woocommerce' ),
        'type'        => 'title',
        'description' => '',
    ),
    'key_id' => array(
        'title' => __('Merchant ID', 'woo_2c2p'),
        'type' => 'text',
        'description' => __('Provided to Merchant by 2C2P team'),
        'desc_tip' => true
    ),    
    'key_secret' => array(
        'title' => __('Secret Key', 'woo_2c2p'),
        'type' => 'text',
        'description' => __('Provided to Merchant by 2C2P team'),
        'desc_tip' => true
    ),    
    'test_mode' => array(
        'title' => __('Mode', 'woo_2c2p'),
        'type' => 'select',
        'label' => __('2c2p Tranasction Mode.', 'woo_2c2p'),    
        'default' => 'test',
        'description' => __('Mode of 2c2p activities'),
        'desc_tip' => true,
        'class'        => 'wc-enhanced-select',
        'options' => array(
            'demo2' => 'Test Mode',
            't' => 'Live Mode'
        ),
    ),
    'wc_2c2p_advanced_options' => array(
        'title'       => __( 'Advanced options', 'woocommerce' ),
        'type'        => 'title',
        'description' => '',
    ),
    'wc_2c2p_stored_card_payment' => array(
        'title' => __('Enable/Disable', 'woo_2c2p'),
        'type' => 'checkbox',
        'label' => __('Stored Card Payment', 'woo_2c2p'),            
    ),   
    'wc_2c2p_123_payment_expiry' => array(
        'title' => __('123 Payment Expiry (hours)', 'woo_2c2p'),
        'type' => 'text',
        'description' => __('123 Payment Expiry in hours like (8-720)', 'woo_2c2p'),
        'desc_tip' => true,        
    ),
    'wc_2c2p_default_lang' => array(
        'title' => __('Select Language', 'woo_2c2p'),
        'type' => 'select',
        'label' => __('Select Language.', 'woo_2c2p'),    
        'default' => 'test',
        'description' => __('2C2P currently supports 6 languages'),
        'desc_tip' => true,
        'class'        => 'wc-enhanced-select',
        'options' => array(
            'en' => 'English',
            'ja' => 'Japanese',
            'th' => 'Thailand',
            'id' => 'Bahasa Indonesia',
            'my' => 'Burmese',
            'zh' => 'Simplified Chinese',
        ),
    ),    
    'wc_2c2p_fixed_description' => array(
        'title' => __('Fixed Description', 'woo_2c2p'),
        'type' => 'textarea',
        'default' => __('Fixed Description.', 'woo_2c2p'),
        'description' => __('Fixed product description which display during payment.', 'woo_2c2p'),
        'desc_tip' => true,        
    ),
);
?> 