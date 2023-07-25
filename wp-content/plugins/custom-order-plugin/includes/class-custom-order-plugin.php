<?php
use \Firebase\JWT\JWT;

require_once __DIR__ . '/conf.php';

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
            $customer_name = sanitize_text_field($_GET['name']);
            $customer_email = sanitize_email($_GET['email']);
            $customer_phone = sanitize_text_field($_GET['phone']);
            $product_id = sanitize_text_field($_GET['product_id']);
            $product_name = sanitize_text_field($_GET['product_name']);

            // Create order's WooCommerce 
            $order = $this->create_woocommerce_order($customer_name, $customer_email, $customer_phone, $product_id, $product_name);
            echo '=> The order WooCommerce created successfully = ' . $order;

            // If the order created successfully
            if ($order) {
                // Get payment url from 2C2P, after that redirect to payment url
                $token = $this->generate_payment_jwt_token($order);
                echo '=> payment_token = ' . $token;
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
            $product = $this->find_products_by_sku($product_id);
            
            if ($product) {
                $order->add_product($product, $quantity);
            }

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

    public function find_products_by_sku($sku)
    {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            return $product;
        }
        return null;
    }

    public function generate_payment_jwt_token(WC_Order $order)
    {
        $secret_sha_key = _2C2P_SECRET_SHA_KEY;
        $merchant_id = _2C2P_MERCHANT_ID;
        $payload = array(
            "merchantID" => $merchant_id,
            "invoiceNo" => $order->get_id(),
            "description" => $order->get_billing_first_name(),
            "amount" => $order->get_total(),
            "currencyCode" => $order->get_currency()
        );
        $jwt = JWT::encode($payload, $secret_sha_key, 'HS256');
        $token = '{"payload":"' . $jwt . '"}';
        return $token;
    }

}