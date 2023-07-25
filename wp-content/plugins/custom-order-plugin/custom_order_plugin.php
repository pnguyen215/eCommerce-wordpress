<?php
/**
 * Plugin Name: Custom Order Plugin
 * Description: Custom plugin to handle orders from custom URL and create WooCommerce orders.
 * Version: 1.0.0
 * Author: Aris Nguyen
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Include the main plugin class
require_once plugin_dir_path(__FILE__) . 'includes/class-custom-order-plugin.php';

// Initialize the plugin
function custom_order_plugin_init()
{
    $custom_order_plugin = new Custom_Order_Plugin();
    $custom_order_plugin->init();
}
add_action('plugins_loaded', 'custom_order_plugin_init');