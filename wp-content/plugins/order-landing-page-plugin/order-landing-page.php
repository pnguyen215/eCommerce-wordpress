<?php
/**
 * Plugin Name: Order Landing Page Provider
 * Description: Custom plugin to handle orders from custom URL and create WooCommerce orders.
 * Version: 1.0.0
 * Author: Aris Nguyen
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the main plugin class
require_once plugin_dir_path(__FILE__) . 'provider/OrderLandingPageProvider.php';

// Initialize the plugin
function order_landing_page_plugin_init()
{
    $order_plugin = new OrderLandingPageProvider();
    $order_plugin->init();
}
add_action('plugins_loaded', 'order_landing_page_plugin_init');

// Define a function to handle the "order-received" event 
function order_landing_page_received_event_handler($order_id)
{
    $order_plugin = new OrderLandingPageProvider();
    $order_plugin->process_order_received_event_handler($order_id);
}
add_action('woocommerce_thankyou', 'order_landing_page_received_event_handler', 10, 1);