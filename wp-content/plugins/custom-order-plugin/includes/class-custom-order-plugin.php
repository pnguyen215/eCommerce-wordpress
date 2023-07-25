<?php
class Custom_Order_Plugin
{
    public function init()
    {
        add_action('template_redirect', array($this, 'process_custom_order'));
    }

    public function process_custom_order()
    {
        // if (is_page('custom-order-form')) {
        // Check if all required parameters are present in $_GET
        if (isset($_GET['name']) && isset($_GET['email']) && isset($_GET['phone']) && isset($_GET['product_id']) && isset($_GET['product_name'])) {
            // Read information from URL
            $customer_name = sanitize_text_field($_GET['name']);
            $customer_email = sanitize_email($_GET['email']);
            $customer_phone = sanitize_text_field($_GET['phone']);
            // $product_id = sanitize_text_field($_GET['product_id']);
            $product_id = 12;
            $product_name = sanitize_text_field($_GET['product_name']);

            // Create order's WooCommerce
            $order = $this->create_woocommerce_order($customer_name, $customer_email, $customer_phone, $product_id, $product_name);
            echo 'The order WooCommerce created successfully = ' . $order;

            // If the order created successfully
            if ($order) {
                // Get payment url from 2C2P, after that redirect to payment url
                exit;
            } else {
                $this->redirect_page_order_error();
            }
        }
        // }
    }

    public function create_woocommerce_order($customer_name, $customer_email, $customer_phone, $product_id, $product_name)
    {
        // Create an empty order instance
        $order = wc_create_order();
        if ($order) {
            // Set customer information
            $order->set_billing_first_name($customer_name);
            $order->set_billing_email($customer_email);
            $order->set_billing_phone($customer_phone);

            // Add a product to the order (you may adjust product ID and quantity)
            $quantity = 1;
            $order->add_product(wc_get_product($product_id), $quantity);

            // Calculate totals and save the order
            $order->calculate_totals();
            $order->save();

            return $order;
        } else {
            return false;
        }
    }

    public function get_2c2p_payment_url($order)
    {
        // return the payment url
        return "";
    }

    public function redirect_page_payment($url)
    {
        wp_redirect($url);
        exit;
    }

    public function redirect_page_order_error()
    {
        wp_redirect(home_url('/order-error/'));
        exit;
    }
}