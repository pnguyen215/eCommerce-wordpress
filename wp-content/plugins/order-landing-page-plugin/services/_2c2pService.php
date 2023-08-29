<?php
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

require_once __DIR__ . './../../../../conf.php';
require_once __DIR__ . './../../../../wp-provider/conf-provider.php';
require_once __DIR__ . './../../../../wp-provider/json-provider.php';
require_once __DIR__ . './../../../../wp-provider/status-provider.php';
require_once __DIR__ . './../../../../wp-provider/time-provider.php';
require_once __DIR__ . './../../../../wp-provider/string-provider.php';


interface _2C2PGateway
{
    public function is_2c2p_response_success($payload): bool;
    public function get_2c2p_response_codes(): array;
    public function of_2c2p_response_description(string $code): string;
    public function send_payment_inquiry_request($token): mixed;
    public function send_payment_order_request($token): mixed;
    public function decode_payment_payload_token($token): array|null;
    public function generate_payment_inquiry_token(WC_Order $order): string;
    public function generate_payment_order_token(WC_Order $order): string;
    public function get_wp_return_url(WC_Order $order = null): mixed;
    public function send_card_token_information(string $token): mixed;
}

class _2C2PService implements _2C2PGateway
{

    public function is_2c2p_response_success($payload): bool
    {
        if (!is_array($payload)) {
            return false;
        }
        if (!array_key_exists('respCode', $payload)) {
            return false;
        }
        return $payload["respCode"] === "0000";
    }

    public function get_2c2p_response_codes(): array
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

    public function of_2c2p_response_description(string $code): string
    {
        return $this->get_2c2p_response_codes()[$code];
    }

    public function send_payment_inquiry_request($token): mixed
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

    public function send_payment_order_request($token): mixed
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

    public function decode_payment_payload_token($token): array|null
    {
        if (empty($token) || is_null(($token))) {
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

    public function generate_payment_inquiry_token(WC_Order $order): string
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

    public function generate_payment_order_token(WC_Order $order): string
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
                    "mobileNo" => $order->get_billing_phone(),
                    "currencyCode" => $order->get_currency(),
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

    public function get_wp_return_url(WC_Order $order = null): mixed
    {
        if ($order) {
            $return_url = $order->get_checkout_order_received_url();
        } else {
            $return_url = wc_get_endpoint_url('order-received', '', wc_get_checkout_url());
        }
        if (is_enabled_debug_mode()) {
            warnColor("Woo order received url", $return_url);
        }
        return apply_filters('woocommerce_get_return_url', $return_url, $order);
    }

    public function send_card_token_information(string $token): mixed
    {
        $endpoint = _2C2P_HOST . '/cardtokeninfo';

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

?>