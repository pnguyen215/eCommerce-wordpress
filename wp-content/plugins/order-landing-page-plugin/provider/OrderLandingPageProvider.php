<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

require_once __DIR__ . './../classes/OrderLandingPage.php';
require_once __DIR__ . './../classes/LandingPage.php';
require_once __DIR__ . './../classes/OfferLandingPage.php';
require_once __DIR__ . './../classes/AddressLandingPage.php';
require_once __DIR__ . './../../../../conf.php';

class OrderLandingPageProvider
{
    public function init()
    {
        add_action('template_redirect', array($this, 'process_order_landing_page'));
    }

    public function process_order_landing_page()
    {
        if (is_page('custom-order-form')) {
            if (
                isset($_GET['name']) &&
                isset($_GET['email']) &&
                isset($_GET['phone']) &&
                isset($_GET['product_id'])
            ) {
                $customer_name = isset($_GET['name']) ? sanitize_text_field($_GET['name']) : VIRTUAL_SANDBOX_CUSTOMER_NAME;
                $customer_email = isset($_GET['email']) ? sanitize_email($_GET['email']) : VIRTUAL_SANDBOX_CUSTOMER_EMAIL;
                $customer_phone = isset($_GET['phone']) ? sanitize_text_field($_GET['phone']) : VIRTUAL_SANDBOX_CUSTOMER_PHONE;
                $product_id = intval(isset($_GET['product_id']) ? sanitize_text_field($_GET['product_id']) : VIRTUAL_SANDBOX_PRODUCT_ID);
                $product_name = isset($_GET['product_name']) ? sanitize_text_field($_GET['product_name']) : VIRTUAL_SANDBOX_PRODUCT_NAME;
                $offer_id = intval(isset($_GET['offer_id']) ? sanitize_text_field($_GET['offer_id']) : VIRTUAL_SANDBOX_OFFER_ID);
                $province_name = isset($_GET['province_name']) ? sanitize_text_field($_GET['province_name']) : VIRTUAL_SANDBOX_PROVINCE_NAME;
                $district_name = isset($_GET['district_name']) ? sanitize_text_field($_GET['district_name']) : VIRTUAL_SANDBOX_DISTRICT_NAME;
                $ward_name = isset($_GET['ward_name']) ? sanitize_text_field($_GET['ward_name']) : VIRTUAL_SANDBOX_WARD_NAME;
                $shipping_address = isset($_GET['shipping_address']) ? sanitize_text_field($_GET['shipping_address']) : VIRTUAL_SANDBOX_SHIPPING_ADDRESS;
                $link = isset($_GET['link']) ? sanitize_text_field($_GET['link']) : VIRTUAL_SANDBOX_LINK;
                $click_id = isset($_GET['click_id']) ? sanitize_text_field($_GET['click_id']) : "<click-id>";
                $transaction_id = isset($_GET['transaction_id']) ? sanitize_text_field($_GET['transaction_id']) : "<transaction-id>";

                if (VIRTUAL_SANDBOX_ENABLED_GENERATE_CLICK_ID) {
                    $click_id = wp_generate_uuid4();
                    $transaction_id = $click_id;
                }

                $order_landing_page = new OrderLandingPage();
                $address = new AddressLandingPage();
                $offer = new OfferLandingPage();
                $landing_page = new LandingPage();

                $address
                    ->setProvinceName($province_name)
                    ->setDistrictName($district_name)
                    ->setWardName($ward_name)
                    ->setShippingAddress($shipping_address);
                $offer
                    ->setOfferId($offer_id)
                    ->setProductId($product_id)
                    ->setProductName($product_name);

                $landing_page
                    ->setClickId($click_id)
                    ->setTransactionId($transaction_id)
                    ->setLink($link);

                $order_landing_page
                    ->setCustomerName($customer_name)
                    ->setCustomerEmail($customer_email)
                    ->setCustomerPhone($customer_phone)
                    ->setAddress($address)
                    ->setOffer($offer)
                    ->setLandingPage($landing_page);

                $order = $this->create_woocommerce_order($order_landing_page);
                if ($order) {
                    if (ENABLED_REDIRECT_CHECKOUT_PAYMENT_URL) {
                        $this->redirect_checkout_payment_url($order);
                    } else {
                        $this->redirect_2c2p_payment_url($order);
                    }
                } else {
                    $this->redirect_page_order_error();
                }
            }
        }
    }

    private function create_woocommerce_order(OrderLandingPage $request): WC_Order|bool
    {
        $order = wc_create_order();
        if ($order) {
            // billing
            $order->set_billing_first_name($request->getCustomerName());
            $order->set_billing_email($request->getCustomerEmail());
            $order->set_billing_phone($request->getCustomerPhone());

            // transaction info
            $order->set_transaction_id($request->getLandingPage()->getClickId());
            $order->set_billing_postcode($request->getOffer()->getOfferId());

            // shipping
            $order->set_shipping_first_name($order->get_billing_first_name());
            $order->set_shipping_phone($order->get_billing_phone());

            // billing address
            $order->set_billing_city($request->getAddress()->getWardName()); // ward
            $order->set_billing_state($request->getAddress()->getDistrictName()); // district
            $order->set_billing_country($request->getAddress()->getProvinceName()); // province
            $order->set_billing_address_1($request->getAddress()->getShippingAddress()); // shipping address

            // shipping address
            $order->set_shipping_city($order->get_billing_city());
            $order->set_shipping_state($order->get_billing_state());
            $order->set_shipping_country($order->get_billing_country());
            $order->set_shipping_address_1($order->get_billing_address_1());

            $quantity = 1;
            $product = $this->find_products_by_sku($request->getOffer()->getProductId());

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

    private function redirect_2c2p_payment_url(WC_Order $order): void
    {
        $raw = $this->generate_payment_jwt_token($order);
        $response = $this->send_payment_jwt_token_request($raw);

        if (!is_array($response) || !array_key_exists('payload', $response)) {
            $this->redirect_page_order_error();
            exit;
        }
        $token = $response["payload"];
        $decodeToken = $this->decode_payment_jwt_token($token);

        if (is_array($decodeToken) && array_key_exists('webPaymentUrl', $decodeToken)) {
            $this->redirect_page_payment($decodeToken["webPaymentUrl"]);
        }
        exit;
    }

    private function redirect_checkout_payment_url(WC_Order $order): void
    {
        $order_pay_page_id = wc_get_page_id('checkout');
        $url = '/?page_id=' . $order_pay_page_id . '&order-pay=' . $order->get_id() . '&pay_for_order=true' . '&key=' . $order->get_order_key();
        $this->redirect_page_payment($url);
    }

    private function redirect_page_payment($url): void
    {
        wp_redirect($url);
        exit;
    }

    private function redirect_page_order_error(): void
    {
        wp_redirect(home_url('/order-error/'));
        exit;
    }

    private function find_products_by_sku($sku): WC_Product|bool|null
    {
        $product_id = wc_get_product_id_by_sku($sku);
        if ($product_id) {
            $product = wc_get_product($product_id);
            return $product;
        }
        return null;
    }

    private function generate_payment_jwt_token(WC_Order $order): string
    {
        $secret_sha_key = _2C2P_SECRET_SHA_KEY;
        $merchant_id = _2C2P_MERCHANT_ID;
        $payload = array(
            "merchantID" => $merchant_id,
            "invoiceNo" => strval($order->get_order_key()),
            "description" => $order->get_billing_first_name(),
            "amount" => $order->get_total(),
            "currencyCode" => $order->get_currency(),
            "uiParams" => array(
                "userInfo" => array(
                    "name" => $order->get_billing_first_name(),
                    "email" => $order->get_billing_email(),
                    "mobileNo" => $order->get_billing_phone()
                )
            )
        );
        $jwt = JWT::encode($payload, $secret_sha_key, 'HS256');
        return $jwt;
    }

    private function decode_payment_jwt_token($token): array|null
    {
        if ($token == null || $token == "") {
            return $token;
        }
        try {
            $decodedPayload = JWT::decode($token, new Key(_2C2P_SECRET_SHA_KEY, 'HS256'));
            $decoded_array = (array) $decodedPayload;
            return $decoded_array;
        } catch (Exception $e) {
            return null;
        }
    }

    private function send_payment_jwt_token_request($token): mixed
    {
        $endpoint = _2C2P_HOST . '/paymentToken';

        $data = array(
            'payload' => $token,
        );

        $headers = array(
            'Content-Type' => 'application/json',
        );

        $args = array(
            'headers' => $headers,
            'body' => json_encode($data),
        );

        $response = wp_remote_post($endpoint, $args);
        if (is_wp_error($response)) {
            return null;
        }
        $response_body = wp_remote_retrieve_body($response);
        $decoded_response = json_decode($response_body, true);
        return $decoded_response;
    }
}