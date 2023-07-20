<?php

class WC_2C2P_Meta_Data_Helper{

	function __construct() { }

	//This function is stored meta data value into wordpress meta table.
	public function wc_2c2p_store_response_meta($parameter){

		$order_id = isset($parameter['order_id']) ? sanitize_text_field($parameter['order_id']) : "0";

		if(isset($order_id)){
			update_post_meta($order_id, 'wc_2c2p_order_id_meta', sanitize_text_field($order_id));
		}

		if(array_key_exists('version',$parameter)){
			if(isset($parameter['version']))
				update_post_meta($order_id, 'wc_2c2p_version_meta', sanitize_text_field($parameter['version']));
		}

		if(array_key_exists('request_timestamp',$parameter)){
			if(isset($parameter['request_timestamp']))
				update_post_meta($order_id, 'wc_2c2p_request_timestamp_meta', sanitize_text_field($parameter['request_timestamp']));
		}

		if(array_key_exists('merchant_id',$parameter)) {
			if(isset($parameter['merchant_id']))
				update_post_meta($order_id, 'wc_2c2p_merchant_id_meta', sanitize_text_field($parameter['merchant_id']));	
		}

		if(array_key_exists('invoice_no',$parameter)){
			if(isset($parameter['invoice_no']))
				update_post_meta($order_id, 'wc_2c2p_invoice_no_meta', sanitize_text_field($parameter['invoice_no']));
		}

		if(array_key_exists('currency',$parameter)){
			if(isset($parameter['currency']))
				update_post_meta($order_id, 'wc_2c2p_currency_meta', sanitize_text_field($parameter['currency']));
		}

		if(array_key_exists('amount',$parameter)){
			if(isset($parameter['amount']))
				update_post_meta($order_id, 'wc_2c2p_amount_meta',sanitize_text_field($parameter['amount']));
		}

		if(array_key_exists('transaction_ref',$parameter)){
			if(isset($parameter['transaction_ref']))
				update_post_meta($order_id, 'wc_2c2p_transaction_ref_meta', sanitize_text_field($parameter['transaction_ref']));
		}

		if(array_key_exists('approval_code',$parameter)){
			if(isset($parameter['approval_code']))
				update_post_meta($order_id, 'wc_2c2p_approval_code_meta', sanitize_text_field($parameter['approval_code']));
		}

		if(array_key_exists('eci',$parameter)){
			if(isset($parameter['eci']))
				update_post_meta($order_id, 'wc_2c2p_eci_meta', sanitize_text_field($parameter['eci']));
		}

		if(array_key_exists('transaction_datetime',$parameter)){
			if(isset($parameter['transaction_datetime']))
				update_post_meta($order_id, 'wc_2c2p_transaction_datetime_meta', sanitize_text_field($parameter['transaction_datetime']));
		}

		if(array_key_exists('payment_channel',$parameter)){
			if(isset($parameter['payment_channel']))
				update_post_meta($order_id, 'wc_2c2p_payment_channel_meta', sanitize_text_field($parameter['payment_channel']));
		}

		if(array_key_exists('payment_status',$parameter)){
			if(isset($parameter['payment_status']))
				update_post_meta($order_id, 'wc_2c2p_payment_status_meta',sanitize_text_field($parameter['payment_status']));
		}

		if(array_key_exists('channel_response_code',$parameter)){
			if(isset($parameter['channel_response_code']))
				update_post_meta($order_id, 'wc_2c2p_channel_response_code_meta', sanitize_text_field($parameter['channel_response_code']));	
		}

		if(array_key_exists('channel_response_desc',$parameter)){
			if(isset($parameter['channel_response_desc']))
				update_post_meta($order_id, 'wc_2c2p_channel_response_desc_meta', sanitize_text_field($parameter['channel_response_desc']));
		}

		if(array_key_exists('masked_pan',$parameter)){
			if(isset($parameter['masked_pan']))
				update_post_meta($order_id, 'wc_2c2p_masked_pan_desc_meta', sanitize_text_field($parameter['masked_pan']));
		}

		if(array_key_exists('stored_card_unique_id',$parameter)){
			if(isset($parameter['stored_card_unique_id']))
				update_post_meta($order_id, 'wc_2c2p_stored_card_unique_id_meta', sanitize_text_field($parameter['stored_card_unique_id']));
		}

		if(array_key_exists('backend_invoice',$parameter)){
			if(isset($parameter['backend_invoice']))
				update_post_meta($order_id, 'wc_2c2p_backend_invoice_meta', sanitize_text_field($parameter['backend_invoice']));
		}

		if(array_key_exists('paid_channel',$parameter)){
			if(isset($parameter['paid_channel']))
				update_post_meta($order_id, 'wc_2c2p_paid_channel_meta', sanitize_text_field($parameter['paid_channel']));
		}

		if(array_key_exists('paid_agent',$parameter)){
			if(isset($parameter['paid_agent']))
				update_post_meta($order_id, 'wc_2c2p_paid_agent_meta', sanitize_text_field($parameter['paid_agent']));
		}

		if(array_key_exists('recurring_unique_id',$parameter)){
			if(isset($parameter['recurring_unique_id']))
				update_post_meta($order_id, 'wc_2c2p_recurring_unique_id_meta', sanitize_text_field($parameter['recurring_unique_id']));
		}

		if(array_key_exists('user_defined_1',$parameter)){
			if(isset($parameter['user_defined_1']))
				update_post_meta($order_id, 'wc_2c2p_user_defined_1_meta', sanitize_text_field($parameter['user_defined_1']));
		}

		if(array_key_exists('user_defined_2',$parameter)){
			if(isset($parameter['user_defined_2']))
				update_post_meta($order_id, 'wc_2c2p_user_defined_2_meta', sanitize_text_field($parameter['user_defined_2']));
		}

		if(array_key_exists('user_defined_3',$parameter)){
			if(isset($parameter['user_defined_3']))
				update_post_meta($order_id, 'wc_2c2p_user_defined_3_meta', sanitize_text_field($parameter['user_defined_3']));
		}

		if(array_key_exists('user_defined_4',$parameter)){
			if(isset($parameter['user_defined_4']))
				update_post_meta($order_id, 'wc_2c2p_user_defined_4_meta', sanitize_text_field($parameter['user_defined_4']));
		}

		if(array_key_exists('user_defined_5',$parameter)){
			if(isset($parameter['user_defined_5']))
				update_post_meta($order_id, 'wc_2c2p_user_defined_5_meta', sanitize_text_field($parameter['user_defined_5']));
		}

		if(array_key_exists('browser_info',$parameter)){
			if(isset($parameter['browser_info']))
				update_post_meta($order_id, 'wc_2c2p_browser_info_meta', sanitize_text_field($parameter['browser_info']));
		}

		if(array_key_exists('ippPeriod',$parameter)){
			if(isset($parameter['ippPeriod']))
				update_post_meta($order_id, 'wc_2c2p_ippPeriod_meta', sanitize_text_field($parameter['ippPeriod']));
		}

		if(array_key_exists('ippInterestType',$parameter)){
			if(isset($parameter['ippInterestType']))
				update_post_meta($order_id, 'wc_2c2p_ippInterestType_meta', sanitize_text_field($parameter['ippInterestType']));
		}

		if(array_key_exists('ippInterestRate',$parameter)){
			if(isset($parameter['ippInterestRate']))
				update_post_meta($order_id, 'wc_2c2p_ippInterestRate_meta', sanitize_text_field($parameter['ippInterestRate']));
		}

		if(array_key_exists('ippMerchantAbsorbRate',$parameter)){
			if(isset($parameter['ippMerchantAbsorbRate']))
				update_post_meta($order_id, 'wc_2c2p_ippMerchantAbsorbRate_meta', sanitize_text_field($parameter['ippMerchantAbsorbRate']));
		}
	}
}

?>