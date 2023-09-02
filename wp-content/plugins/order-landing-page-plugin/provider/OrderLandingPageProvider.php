<?php

require_once __DIR__ . './../classes/OrderLandingPage.php';
require_once __DIR__ . './../classes/LandingPage.php';
require_once __DIR__ . './../classes/OfferLandingPage.php';
require_once __DIR__ . './../classes/AddressLandingPage.php';
require_once __DIR__ . './../../../../conf.php';
require_once __DIR__ . './../../../../wp-provider/conf-provider.php';
require_once __DIR__ . './../../../../wp-provider/json-provider.php';
require_once __DIR__ . './../../../../wp-provider/status-provider.php';
require_once __DIR__ . './../../../../wp-provider/time-provider.php';
require_once __DIR__ . './../../../../wp-provider/string-provider.php';
require_once __DIR__ . './../services/_2c2pService.php';


class OrderLandingPageProvider
{
    private $_2c2pService;
    public function __construct(_2C2PService $_2c2pService)
    {
        global $decodeUrls;
        $decodeUrls = true;
        $this->_2c2pService = $_2c2pService;
    }
    public function init()
    {
        add_action('template_redirect', array($this, 'process_order_landing_page'));
    }

    public function listen()
    {
        add_action('template_redirect', array($this, 'process_order_received_listen'));
    }

    public function process_order_landing_page()
    {
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
            $shipping_address = isset($_GET['address']) ? sanitize_text_field($_GET['address']) : VIRTUAL_SANDBOX_SHIPPING_ADDRESS;
            $link = isset($_GET['link']) ? sanitize_text_field($_GET['link']) : VIRTUAL_SANDBOX_LINK;
            $click_id = isset($_GET['click_id']) ? sanitize_text_field($_GET['click_id']) : "<click-id>";
            $transaction_id = isset($_GET['transaction_id']) ? sanitize_text_field($_GET['transaction_id']) : "<transaction-id>";
            $pid = isset($_GET['pid']) ? sanitize_text_field($_GET['pid']) : "<pid>";
            $affiliate_id = isset($_GET['affiliate_id']) ? sanitize_text_field($_GET['affiliate_id']) : "<affiliate-id>";
            $sub_id1 = isset($_GET['sub_id1']) ? sanitize_text_field($_GET['sub_id1']) : "<sub-id1>";
            $tracker_id = intval(isset($_GET['tracker_id']) ? sanitize_text_field($_GET['tracker_id']) : get_virtual_ldp_tracker_id());
            $province_id = intval(isset($_GET['province_id']) ? sanitize_text_field($_GET['province_id']) : get_virtual_sandbox_province_id());
            $district_id = intval(isset($_GET['district_id']) ? sanitize_text_field($_GET['district_id']) : get_virtual_sandbox_district_id());
            $ward_id = intval(isset($_GET['ward_id']) ? sanitize_text_field($_GET['ward_id']) : get_virtual_sandbox_ward_id());

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
                warnColor("Order WooCommerce submitted", $order->get_data());
            }
            if ($order) {
                $this->redirect_payment($order);
            } else {
                $this->redirect_page_payment(get_redirect_link_order_failure());
            }
        }
    }

    public function process_order_received_default($order_id)
    {
        $this->process_order_received_common($order_id);
    }

    public function process_order_received_listen()
    {
        if (
            isset($_GET['order_id']) &&
            isset($_GET['order_key']) &&
            isset($_GET['order_value'])
        ) {
            $order_id = intval(isset($_GET['order_id']) ? sanitize_text_field($_GET['order_id']) : "0");
            if ($order_id == 0) {
                $this->redirect_page_payment(get_redirect_link_order_failure());
            }
            $this->process_order_received_common($order_id);
        }
    }

    private function process_order_received_common($order_id)
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
        $token = $this->_2c2pService->generate_payment_inquiry_token($order);
        $response = $this->_2c2pService->send_payment_inquiry_request($token);
        if (!is_array($response) || !array_key_exists('payload', $response)) {
            error("(ERROR) 2C2P payment inquiry not found", $response);
            exit;
        }
        $token_encoded = $response["payload"];
        $payload = $this->_2c2pService->decode_payment_payload_token($token_encoded);
        if (is_enabled_debug_mode()) {
            debugColor("2C2P raw payment inquiry", $token);
            successColor("2C2P payment inquiry result", $payload);
        }
        $this->update_order_woocommerce($order, $payload);
        $success = $this->_2c2pService->is_2c2p_response_success($payload);
        if ($success) {
            $this->redirect_page_payment(get_redirect_link_order_completed());
        } else {
            $this->redirect_page_payment(get_redirect_link_order_failure());
        }
    }

    private function update_order_woocommerce(WC_Order $order, array $payload)
    {
        global $woocommerce;
        $success = $this->_2c2pService->is_2c2p_response_success($payload);
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
            $success = $this->_2c2pService->is_2c2p_response_success($payload);
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
            $order->add_meta_data("wc_ldp_province_name", $order_ldp->getAddress()->getProvinceName());
            $order->add_meta_data("wc_ldp_district_name", $order_ldp->getAddress()->getDistrictName());
            $order->add_meta_data("wc_ldp_ward_name", $order_ldp->getAddress()->getWardName());
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
                    $list = split_by($product->get_sku(), get_symbol_sku());
                    $order->add_meta_data("wc_ldp_product_id", $list[0]);
                } else {
                    $order->add_meta_data("wc_ldp_product_id", strval($order_ldp->getOffer()->getProductId()));
                }
                $order->add_meta_data("wc_ldp_salable_product_id", strval($product->get_id()));
                $order->add_meta_data("wc_ldp_quantity", strval($product->get_menu_order()));
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
        $raw = $this->_2c2pService->generate_payment_order_token($order);
        $response = $this->_2c2pService->send_payment_order_request($raw);

        if (!is_array($response) || !array_key_exists('payload', $response)) {
            $this->redirect_page_payment(get_redirect_link_order_failure());
            exit;
        }
        $token = $response["payload"];
        $decodeToken = $this->_2c2pService->decode_payment_payload_token($token);
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
}