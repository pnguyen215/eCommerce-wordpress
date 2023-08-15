<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

require_once __DIR__ . './../classes/OrderLandingPage.php';
require_once __DIR__ . './../classes/LandingPage.php';
require_once __DIR__ . './../classes/OfferLandingPage.php';
require_once __DIR__ . './../classes/AddressLandingPage.php';
require_once __DIR__ . './../../../../conf.php';
require_once __DIR__ . './../../../../wp-provider/conf-provider.php';
require_once __DIR__ . './../../../../wp-provider/json-provider.php';
require_once __DIR__ . './../../../../wp-provider/status-provider.php';
require_once __DIR__ . './../../../../wp-provider/time-provider.php';

class OrderLandingPageProvider
{
    public function __construct()
    {
        global $decodeUrls;
        $decodeUrls = true;
    }
    public function init()
    {
        add_action('template_redirect', array($this, 'process_order_landing_page'));
    }

    public function process_order_landing_page()
    {
        // if (is_page('custom-order-form')) {
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
            $pid = isset($_GET['pid']) ? sanitize_text_field($_GET['pid']) : "<pid>";
            $affiliate_id = isset($_GET['affiliate_id']) ? sanitize_text_field($_GET['affiliate_id']) : "<affiliate-id>";
            $sub_id1 = isset($_GET['sub_id1']) ? sanitize_text_field($_GET['sub_id1']) : "<sub-id1>";
            $tracker_id = isset($_GET['tracker_id']) ? sanitize_text_field($_GET['tracker_id']) : get_virtual_ldp_tracker_id();
            $province_id = intval(isset($_GET['province_id']) ? sanitize_text_field($_GET['province_id']) : VIRTUAL_SANDBOX_PROVINCE_ID);
            $district_id = intval(isset($_GET['district_id']) ? sanitize_text_field($_GET['district_id']) : VIRTUAL_SANDBOX_DISTRICT_ID);
            $ward_id = intval(isset($_GET['ward_id']) ? sanitize_text_field($_GET['ward_id']) : VIRTUAL_SANDBOX_WARD_ID);

            if (is_enabled_generate_click_id()) {
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
                ->setShippingAddress($shipping_address)
                ->setProvinceId($province_id)
                ->setDistrictId($district_id)
                ->setWardId($ward_id);
            $offer
                ->setOfferId($offer_id)
                ->setProductId($product_id)
                ->setProductName($product_name);
            $landing_page
                ->setClickId($click_id)
                ->setTransactionId($transaction_id)
                ->setLink($link)
                ->setPid($pid)
                ->setAffiliateId($affiliate_id)
                ->setTrackerId($tracker_id)
                ->setSubId1($sub_id1);

            $order_landing_page
                ->setCustomerName($customer_name)
                ->setCustomerEmail($customer_email)
                ->setCustomerPhone($customer_phone)
                ->setAddress($address)
                ->setOffer($offer)
                ->setLandingPage($landing_page);

            if (is_enabled_debug_mode()) {
                debugColor("Order landing page payload", $order_landing_page);
            }
            $order = $this->create_woocommerce_order($order_landing_page);
            $this->add_meta_base_fields($order, $order_landing_page);
            if (is_enabled_debug_mode()) {
                warnColor("Process order landing page with url fallback", $this->get_woocommerce_payment_url($order));
                debugColor("Order WooCommerce submitted", $order->get_data());
            }
            if ($order) {
                $this->redirect_payment($order);
            } else {
                $this->redirect_page_order_error();
            }
        }
        // }
    }

    public function process_order_received_event_handler($order_id)
    {
        if (is_enabled_redirect_checkout_payment_url()) {
            return;
        }
        if (is_null($order_id) || empty($order_id)) {
            return;
        }
        if (!intval($order_id)) {
            warn("(WARN) process order received event trying to parse integer", $order_id);
            return;
        }
        $order = wc_get_order($order_id);
        if (!$order) {
            warn("(WARN) process order received event which order not found", $order_id);
            return;
        }
        $token = $this->generate_payment_inquiry_token($order);
        $response = $this->send_payment_inquiry_request($token);
        if (!is_array($response) || !array_key_exists('payload', $response)) {
            error("(ERROR) 2C2P payment inquiry not found", $response);
            exit;
        }
        $token_encoded = $response["payload"];
        $payload = $this->decode_payment_payload_token($token_encoded);
        if (is_enabled_debug_mode()) {
            debugColor("2C2P raw payment inquiry", $token);
            successColor("2C2P payment inquiry result", $payload);
        }
        $this->update_order_woocommerce($order, $payload);
    }

    private function update_order_woocommerce(WC_Order $order, array $payload)
    {
        global $woocommerce;
        $success = $this->is_2c2p_response_success($payload);
        try {
            if ($success) {
                $order->update_status(get_status_completed());
                $order->payment_complete();
                $order->add_order_note('2C2P payment transaction successful.<br/>order_id: ' . $order->get_id() . '<br/>transaction_ref: ' . $payload["tranRef"] . '<br/>eci: ' . $payload["eci"] . '<br/>transaction_date_time: ' . format_date(parse_date_string($payload["transactionDateTime"], 'YmdHis'), 'Y-m-d H:i:s') . '<br/>approval_code: ' . $payload["approvalCode"]);
                $woocommerce->cart->empty_cart();
            } else {
                $order->update_status(get_status_failed());
                $order->add_order_note('2C2P payment transaction failed.<br/>order_id: ' . $order->get_id() . '<br/>transaction_ref: ' . $payload["tranRef"] . '<br/>eci: ' . $payload["eci"] . '<br/>transaction_date_time: ' . format_date(parse_date_string($payload["transactionDateTime"], 'YmdHis'), 'Y-m-d H:i:s') . '<br/>approval_code: ' . $payload["approvalCode"] . '<br/>reason: ' . $payload['respDesc']);
            }
            $this->add_meta_fields($order, $payload);
        } catch (Exception $e) {
            errorColor("Updatable order woocommerce has an error occurred", $e);
        }
    }

    private function add_meta_fields(WC_Order $order, array $payload): void
    {
        try {
            $success = $this->is_2c2p_response_success($payload);
            if ($success) {
                $order->add_meta_data("wc_2c2p_amount_meta", $payload["fxAmount"]);
                $order->add_meta_data("wc_2c2p_transaction_amount_meta", $payload["fxAmount"]);
                $order->add_meta_data("wc_2c2p_fx_currency_code_meta", $payload["fxCurrencyCode"]);
            } else {
                $order->add_meta_data("wc_2c2p_amount_meta", $payload["amount"]);
            }
            $order->add_meta_data("wc_2c2p_approval_code_meta", $payload["approvalCode"]);
            $order->add_meta_data("wc_2c2p_backend_invoice_meta", $payload["referenceNo"]);
            $order->add_meta_data("wc_2c2p_browser_info_meta", null);
            $order->add_meta_data("wc_2c2p_channel_response_code_meta", $payload["respCode"]);
            $order->add_meta_data("wc_2c2p_channel_response_desc_meta", $payload["respDesc"]);
            $order->add_meta_data("wc_2c2p_currency_code_meta", $payload["currencyCode"]);
            $order->add_meta_data("wc_2c2p_eci_meta", $payload["eci"]);
            $order->add_meta_data("wc_2c2p_invoice_no_meta", $payload["invoiceNo"]);
            $order->add_meta_data("wc_2c2p_ippInterestRate_meta", null);
            $order->add_meta_data("wc_2c2p_ippInterestType_meta", null);
            $order->add_meta_data("wc_2c2p_ippMerchantAbsorbRate_meta", null);
            $order->add_meta_data("wc_2c2p_ippPeriod_meta", null);
            $order->add_meta_data("wc_2c2p_masked_pan_desc_meta", $payload["cardNo"]);
            $order->add_meta_data("wc_2c2p_merchant_id_meta", $payload["merchantID"]);
            $order->add_meta_data("wc_2c2p_order_id_meta", strval($order->get_id()));
            $order->add_meta_data("wc_2c2p_paid_agent_meta", null);
            $order->add_meta_data("wc_2c2p_paid_channel_meta", null);
            $order->add_meta_data("wc_2c2p_payment_channel_meta", null);
            $order->add_meta_data("wc_2c2p_payment_status_meta", null);
            $order->add_meta_data("wc_2c2p_recurring_unique_id_meta", null);
            $order->add_meta_data("wc_2c2p_request_timestamp_meta", null);
            $order->add_meta_data("wc_2c2p_stored_card_unique_id_meta", null);
            $order->add_meta_data("wc_2c2p_transaction_datetime_meta", format_date(parse_date_string($payload["transactionDateTime"], 'YmdHis'), 'Y-m-d H:i:s'));
            $order->add_meta_data("wc_2c2p_transaction_ref_meta", $payload["tranRef"]);
            $order->add_meta_data("wc_2c2p_user_defined_1_meta", $payload["userDefined1"]);
            $order->add_meta_data("wc_2c2p_user_defined_2_meta", $payload["userDefined2"]);
            $order->add_meta_data("wc_2c2p_user_defined_3_meta", $payload["userDefined3"]);
            $order->add_meta_data("wc_2c2p_user_defined_4_meta", $payload["userDefined4"]);
            $order->add_meta_data("wc_2c2p_user_defined_5_meta", $payload["userDefined5"]);
            $order->add_meta_data("wc_2c2p_event_at_meta", format_date(get_current_date_time(), 'Y-m-d H:i:s'));
            $order->save_meta_data();
            $order->set_transaction_id($payload["tranRef"]);
            $order->save();
        } catch (Exception $e) {
            errorColor("Addable field on order woocommerce has an error occurred", $e);
        }
    }

    private function add_meta_base_fields(WC_Order $order, OrderLandingPage $order_ldp): void
    {
        try {
            $order->add_meta_data("wc_ldp_customer_name", $order_ldp->getCustomerName());
            $order->add_meta_data("wc_ldp_customer_phone", $order_ldp->getCustomerPhone());
            $order->add_meta_data("wc_ldp_click_id", $order_ldp->getLandingPage()->getClickId());
            $order->add_meta_data("wc_ldp_offer_id", strval($order_ldp->getOffer()->getOfferId()));
            $order->add_meta_data("wc_ldp_pid", $order_ldp->getLandingPage()->getPid());
            $order->add_meta_data("wc_ldp_product_name", $order_ldp->getOffer()->getProductName());
            $order->add_meta_data("wc_ldp_affiliate_id", $order_ldp->getLandingPage()->getAffiliateId());
            $order->add_meta_data("wc_ldp_tracker_id", strval($order_ldp->getLandingPage()->getTrackerId()));
            $order->add_meta_data("wc_ldp_link", $order_ldp->getLandingPage()->getLink());
            if ($order_ldp->getAddress()->getProvinceId() > 0) {
                $order->add_meta_data("wc_ldp_province_id", strval($order_ldp->getAddress()->getProvinceId()));
            }
            if ($order_ldp->getAddress()->getDistrictId() > 0) {
                $order->add_meta_data("wc_ldp_district_id", strval($order_ldp->getAddress()->getDistrictId()));
            }
            if ($order_ldp->getAddress()->getWardId() > 0) {
                $order->add_meta_data("wc_ldp_ward_id", strval($order_ldp->getAddress()->getWardId()));
            }
            if (is_enabled_redirect_checkout_payment_url()) {
                $order->add_meta_data("wc_ldp_direction", get_ldp_direction_inward());
            } else {
                $order->add_meta_data("wc_ldp_direction", get_ldp_direction_outward());
            }
            if (is_enabled_using_woo_product_id()) {
                $product = $this->find_products_by_id($order_ldp->getOffer()->getProductId());
            } else {
                $product = $this->find_products_by_sku($order_ldp->getOffer()->getProductId());
            }
            if ($product) {
                if (is_enabled_using_woo_product_id()) {
                    $order->add_meta_data("wc_ldp_product_id", $product->get_sku());
                } else {
                    $order->add_meta_data("wc_ldp_product_id", strval($order_ldp->getOffer()->getProductId()));
                }
                $order->add_meta_data("wc_ldp_salable_product_id", strval($product->get_id()));
                $order->add_meta_data("wc_ldp_quantity", strval($product->get_menu_order()));
            }
            if (is_enabled_debug_mode()) {
                warnColor("Product stock quantity", $product->get_stock_quantity());
                debugColor("Product attributes menu order (as salable quantity)", $product->get_menu_order());
            }
            $order->save_meta_data();
        } catch (Exception $e) {
            errorColor("Addable base field on order woocommerce has an error occurred", $e);
        }
    }

    private function get_woocommerce_payment_url(WC_Order $order): string
    {
        if (is_null($order)) {
            return "";
        }
        if (is_enabled_debug_mode()) {
            infoColor("WooCommerce version", get_woo_version_key());
        }
        $checkout_payment_url = (is_new_woo_version_by("2.1.0")) ? $order->get_checkout_payment_url(true) : get_permalink(get_option('woocommerce_pay_page_id'));
        return $checkout_payment_url;
    }

    private function redirect_payment(WC_Order $order)
    {
        if (is_enabled_redirect_checkout_payment_url()) {
            $this->redirect_checkout_payment_url($order);
        } else {
            $this->redirect_2c2p_payment_url($order);
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

            // shipping
            $order->set_shipping_first_name($order->get_billing_first_name());
            $order->set_shipping_phone($order->get_billing_phone());

            // billing address
            $order->set_billing_city($request->getAddress()->getWardName()); // ward
            $order->set_billing_state($request->getAddress()->getDistrictName()); // district
            $order->set_billing_country($request->getAddress()->getProvinceName()); // province
            $order->set_billing_address_1($request->getAddress()->getShippingAddress()); // shipping address
            $order->set_customer_note($request->getAddress()->getShippingAddress()); // shipping address

            // shipping address
            $order->set_shipping_city($order->get_billing_city());
            $order->set_shipping_state($order->get_billing_state());
            $order->set_shipping_country($order->get_billing_country());
            $order->set_shipping_address_1($order->get_billing_address_1());

            $quantity = 1;
            if (is_enabled_using_woo_product_id()) {
                $product = $this->find_products_by_id($request->getOffer()->getProductId());
            } else {
                $product = $this->find_products_by_sku($request->getOffer()->getProductId());
            }
            if ($product) {
                $order->add_product($product, $quantity); // args = product, quantity
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
        $raw = $this->generate_payment_order_token($order);
        $response = $this->send_payment_order_request($raw);

        if (!is_array($response) || !array_key_exists('payload', $response)) {
            $this->redirect_page_order_error();
            exit;
        }
        $token = $response["payload"];
        $decodeToken = $this->decode_payment_payload_token($token);
        if (is_enabled_debug_mode()) {
            debugColor("2C2P raw payment token", $raw);
            successColor("2C2P payment result", $decodeToken);
        }
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

    private function find_products_by_id($id): WC_Product|bool|null
    {
        $product = wc_get_product($id);
        return $product;
    }

    private function generate_payment_order_token(WC_Order $order): string
    {
        if (!$order) {
            return "";
        }
        $secret_sha_key = _2C2P_SECRET_SHA_KEY;
        $merchant_id = strval(_2C2P_MERCHANT_ID);
        $payload = array(
            "merchantID" => $merchant_id,
            "order_id" => $order->get_id(),
            "invoiceNo" => strval($order->get_id()),
            "description" => $order->get_billing_first_name(),
            "amount" => $order->get_total(),
            "currencyCode" => $order->get_currency(),
            "uiParams" => array(
                "userInfo" => array(
                    "name" => $order->get_billing_first_name(),
                    "email" => $order->get_billing_email(),
                    "mobileNo" => $order->get_billing_phone()
                )
            ),
            "payment_description" => $order->get_billing_first_name() . " Buyer",
            "default_lang" => "en",
            "backendReturnUrl" => "",
            "frontendReturnUrl" => "",
        );
        if (is_enabled_redirect_backend_url()) {
            $payload["backendReturnUrl"] = _2C2P_REDIRECT_BACKEND_URL;
        }
        if (is_enabled_redirect_frontend_url()) {
            $payload["frontendReturnUrl"] = _2C2P_REDIRECT_FRONTEND_URL;
        }
        if (is_enabled_redirect_notification()) {
            $redirect_url = $this->get_wp_return_url($order);
            $payload["backendReturnUrl"] = $redirect_url;
            $payload["frontendReturnUrl"] = $redirect_url;
        }
        if (is_enabled_debug_mode()) {
            debug("2C2P payment token request", $payload);
        }
        $jwt = JWT::encode($payload, $secret_sha_key, 'HS256');
        return $jwt;
    }

    private function generate_payment_inquiry_token(WC_Order $order): string
    {
        if (!$order) {
            return "";
        }
        $secret_sha_key = _2C2P_SECRET_SHA_KEY;
        $merchant_id = strval(_2C2P_MERCHANT_ID);
        $payload = array(
            "merchantID" => $merchant_id,
            "invoiceNo" => strval($order->get_id()),
            "locale" => "en"
        );
        if (is_enabled_debug_mode()) {
            infoColor("2C2P payment inquiry request", $payload);
        }
        $jwt = JWT::encode($payload, $secret_sha_key, 'HS256');
        return $jwt;
    }

    private function get_wp_return_url(WC_Order $order = null)
    {
        if ($order) {
            $return_url = $order->get_checkout_order_received_url();
        } else {
            $return_url = wc_get_endpoint_url('order-received', '', wc_get_checkout_url());
        }
        return apply_filters('woocommerce_get_return_url', $return_url, $order);
    }

    private function decode_payment_payload_token($token): array|null
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

    private function send_payment_order_request($token): mixed
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

    private function send_payment_inquiry_request($token): mixed
    {
        $endpoint = _2C2P_HOST . '/paymentInquiry';

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

    private function is_2c2p_response_success($payload): bool
    {
        if (!is_array($payload)) {
            return false;
        }
        if (!array_key_exists('respCode', $payload)) {
            return false;
        }
        return $payload["respCode"] === "0000";
    }

    private function _2c2p_response_codes(): array
    {
        $responses = array(
            "0000" => "Successful",
            "0001" => "Transaction is pending",
            "0003" => "Transaction is cancelled",
            "0999" => "System error",
            "2001" => "Transaction in progress",
            "2002" => "Transaction not found",
            "2003" => "Failed To Inquiry",
            "4001" => "Refer to card issuer",
            "4002" => "Refer to issuer's special conditions",
            "4003" => "Invalid merchant ID",
            "4004" => "Pick up card",
            "4005" => "Do not honor",
            "4006" => "Error",
            "4007" => "Pick up card, special condition",
            "4008" => "Honor with ID",
            "4009" => "Request in progress",
            "4010" => "Partial amount approved",
            "4011" => "Approved VIP",
            "4012" => "Invalid Transaction",
            "4013" => "Invalid Amount",
            "4014" => "Invalid Card Number",
            "4015" => "No such issuer",
            "4016" => "Approved, Update Track 3",
            "4017" => "Customer Cancellation",
            "4018" => "Customer Dispute",
            "4019" => "Re-enter Transaction",
            "4020" => "Invalid Response",
            "4021" => "No Action Taken",
            "4022" => "Suspected Malfunction",
            "4023" => "Unacceptable Transaction Fee",
            "4024" => "File Update Not Supported by Receiver",
            "4025" => "Unable to Locate Record on File",
            "4026" => "Duplicate File Update Record",
            "4027" => "File Update Field Edit Error",
            "4028" => "File Update File Locked Out",
            "4029" => "File Update not Successful",
            "4030" => "Format Error",
            "4031" => "Bank Not Supported by Switch",
            "4032" => "Completed Partially",
            "4033" => "Expired Card - Pick Up",
            "4034" => "Suspected Fraud - Pick Up",
            "4035" => "Restricted Card - Pick Up",
            "4036" => "Allowable PIN Tries Exceeded",
            "4037" => "No Credit Account",
            "4038" => "Allowable PIN Tries Exceeded",
            "4039" => "No Credit Account",
            "4040" => "Requested Function not Supported",
            "4041" => "Lost Card - Pick Up",
            "4042" => "No Universal Amount",
            "4043" => "Stolen Card - Pick Up",
            "4044" => "No Investment Account",
            "4045" => "Settlement Success",
            "4046" => "Settlement Fail",
            "4047" => "Cancel Success",
            "4048" => "Cancel Fail",
            "4049" => "No Transaction Reference Number",
            "4050" => "Host Down",
            "4051" => "Insufficient Funds",
            "4052" => "No Cheque Account",
            "4053" => "No Savings Account",
            "4054" => "Expired Card",
            "4055" => "Incorrect PIN",
            "4056" => "No Card Record",
            "4057" => "Transaction Not Permitted to Cardholder",
            "4058" => "Transaction Not Permitted to Terminal",
            "4059" => "Suspected Fraud",
            "4060" => "Card Acceptor Contact Acquirer",
            "4061" => "Exceeds Withdrawal Amount Limits",
            "4062" => "Restricted Card",
            "4063" => "Security Violation",
            "4064" => "Original Amount Incorrect",
            "4065" => "Exceeds Withdrawal Frequency Limit",
            "4066" => "Card Acceptor Call Acquirer Security",
            "4067" => "Hard Capture - Pick Up Card at ATM",
            "4068" => "Response Received Too Late",
            "4069" => "Reserved",
            "4070" => "Settle amount cannot exceed authorized amount",
            "4071" => "Inquiry Record Not Exist",
            "4072" => "Promotion not allowed in current payment method",
            "4073" => "Promotion Limit Reached",
            "4074" => "Reserved",
            "4075" => "Allowable PIN Tries Exceeded",
            "4076" => "Invalid Credit Card Format",
            "4077" => "Invalid Expiry Date Format",
            "4078" => "Invalid Three Digits Format",
            "4079" => "Reserved",
            "4080" => "User Cancellation by Closing Internet Browser",
            "4081" => "Unable to Authenticate Card Holder",
            "4082" => "Reserved",
            "4083" => "Reserved",
            "4084" => "Reserved",
            "4085" => "Reserved",
            "4086" => "ATM Malfunction",
            "4087" => "No Envelope Inserted",
            "4088" => "Unable to Dispense",
            "4089" => "Administration Error",
            "4090" => "Cut-off in Progress",
            "4091" => "Issuer or Switch is Inoperative",
            "4092" => "Financial Institution Not Found",
            "4093" => "Trans Cannot Be Completed",
            "4094" => "Duplicate Transmission",
            "4095" => "Reconcile Error",
            "4096" => "System Malfunction",
            "4097" => "Reconciliation Totals Reset",
            "4098" => "MAC Error",
            "4099" => "Unable to Complete Payment",
            "4110" => "Settled",
            "4120" => "Refunded",
            "4121" => "Refund Rejected",
            "4122" => "Refund Failed",
            "4130" => "Chargeback",
            "4131" => "Chargeback Rejected",
            "4132" => "Chargeback Failed",
            "4140" => "Transaction Does Not Exist",
            "4200" => "Tokenization Successful",
            "4201" => "Tokenization Failed",
            "4202" => "Invalid cardToken",
            "5002" => "Timeout",
            "5003" => "Invalid Message",
            "5004" => "Invalid Profile (Merchant) ID",
            "5005" => "Duplicated Invoice",
            "5006" => "Invalid Amount",
            "5007" => "Insufficient Balance",
            "5008" => "Invalid Currency Code",
            "5009" => "Payment Expired",
            "5010" => "Payment Canceled By Payer",
            "5011" => "Invalid Payee ID",
            "5012" => "Invalid Customer ID",
            "5013" => "Account Does Not Exist",
            "5014" => "Authentication Failed",
            "5015" => "Customer paid more than transaction amount",
            "5016" => "Customer paid less than transaction amount",
            "5017" => "Paid Expired",
            "5018" => "Reserved",
            "5019" => "No-Action From WebPay",
            "5998" => "Internal Error",
            "6012" => "Invalid Transaction",
            "6101" => "Invalid request message",
            "6102" => "Required Payload",
            "6103" => "Invalid JWT data",
            "6104" => "Required merchantId",
            "6105" => "Required paymentChannel",
            "6106" => "Required authCode",
            "6107" => "Invalid merchantId",
            "6108" => "Invalid paymentChannel",
            "6109" => "paymentChannel is not configured",
            "6110" => "Unable to retrieve usertoken",
            "7012" => "Invalid Transaction",
            "9004" => "The value is not valid",
            "9005" => "Some mandatory fields are missing",
            "9006" => "This field exceeded its authorized length",
            "9007" => "Invalid merchant",
            "9008" => "Invalid payment expiry",
            "9009" => "Amount is invalid",
            "9010" => "Invalid Currency Code",
            "9012" => "paymentItem name is required",
            "9013" => "paymentItem quantity is required",
            "9014" => "paymentItem amount is required",
            "9015" => "Existing Invoice Number",
            "9016" => "Failed to retrieve PaymentInstruction",
            "9017" => "PaymentInstruction not available",
            "9035" => "Payment failed",
            "9037" => "Merchant configuration is missing",
            "9038" => "Failed To Generate Token",
            "9039" => "The merchant frontend URL is missing",
            "9040" => "The token is invalid",
            "9041" => "Payment token already used",
            "9042" => "Hash value mismatch",
            "9057" => "Payment options are invalid",
            "9058" => "Payment channel invalid",
            "9059" => "Payment channel unauthorized",
            "9060" => "Payment channel unconfigured",
            "9078" => "Promotion code does not exist",
            "9080" => "Tokenization not allowed",
            "9088" => "SubMerchant is required",
            "9089" => "Duplicated SubMerchant",
            "9090" => "SubMerchant Not Found",
            "9091" => "Invalid Sub Merchant ID",
            "9092" => "Invalid Sub Merchant invoiceNo",
            "9093" => "Existing Sub Merchant Invoice Number",
            "9094" => "Invalid Sub Merchant Amount",
            "9095" => "Sub Merchant Amount mismatch",
            "9100" => "FxRateId and OriginalAmount are required",
            "9101" => "Not allow to make a payment with Fx",
            "9102" => "FxRate not available",
            "9103" => "Invalid amount. (for the transaction which using the FxRateId)",
            "9104" => "Invalid Country Code (Airline Info)",
            "9105" => "Invalid Currency Code (Airline Info)",
            "9106" => "Invalid Loyalty Redeem Amount",
            "9107" => "Invalid Loyalty Provider",
            "9108" => "Duplicated Loyalty Reward Id",
            "9109" => "Required Loyalty's External Merchant Id",
            "9110" => "Failed to Inquiry Loyalty Rewards",
            "9202" => "Invalid cardToken",
            "9900" => "Unable to decrypt the payload",
            "9901" => "Invalid invoicePrefix",
            "9902" => "allowAccumulate is required",
            "9903" => "maxAccumulateAmount is required",
            "9904" => "recurringInterval or ChargeOnDate is required",
            "9905" => "recurringCount is required",
            "9906" => "recurringInterval or ChargeOnDate is required",
            "9907" => "Invalid ChargeNextDate",
            "9908" => "Invalid ChargeOnDate",
            "9909" => "chargeNextDate is required",
            "9990" => "Request to merchant front end has failed",
            "9991" => "Request merchant secure has failed",
            "9992" => "Request payment secure has failed",
            "9993" => "An unknown error has occurred",
            "9994" => "Request DB service has failed",
            "9995" => "Request payment service has failed",
            "9996" => "Request Qwik service has failed",
            "9997" => "Request user preferences has failed",
            "9998" => "Request store card has failed",
            "9999" => "Request to merchant backend has failed",
        );
        return $responses;
    }

    private function of_2c2p_response_description(string $code): string
    {
        return $this->_2c2p_response_codes()[$code];
    }
}