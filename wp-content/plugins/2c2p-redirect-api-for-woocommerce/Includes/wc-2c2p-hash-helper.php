<?php

class wc_2c2p_hash_helper{

	private $params;
    private $hashValue;
    private $pg_2c2p_setting_values;
    
	function __construct() {
        $objWC_Gateway_2c2p = new WC_Gateway_2c2p();
        $this->pg_2c2p_setting_values = $objWC_Gateway_2c2p->wc_2c2p_get_setting();    
    }

	//This function is used to check hash value is valid or not.
	function wc_2c2p_is_valid_hash($parameter){

		if(array_key_exists('version',$parameter)) $this->params .= $parameter['version'];

		if(array_key_exists('request_timestamp',$parameter)) $this->params .= $parameter['request_timestamp'];

		if(array_key_exists('merchant_id',$parameter)) $this->params .= $parameter['merchant_id'];

		if(array_key_exists('order_id',$parameter)) $this->params .= $parameter['order_id'];

		if(array_key_exists('invoice_no',$parameter)) $this->params .= $parameter['invoice_no'];

		if(array_key_exists('currency',$parameter)) $this->params .= $parameter['currency'];

		if(array_key_exists('amount',$parameter)) $this->params .= $parameter['amount'];

		if(array_key_exists('transaction_ref',$parameter)) $this->params .= $parameter['transaction_ref'];

		if(array_key_exists('approval_code',$parameter)) $this->params .= $parameter['approval_code'];

		if(array_key_exists('eci',$parameter)) $this->params .= $parameter['eci'];

		if(array_key_exists('transaction_datetime',$parameter)) $this->params .= $parameter['transaction_datetime'];

		if(array_key_exists('payment_channel',$parameter)) $this->params .= $parameter['payment_channel'];

		if(array_key_exists('payment_status',$parameter)) $this->params .= $parameter['payment_status'];

		if(array_key_exists('channel_response_code',$parameter)) $this->params .= $parameter['channel_response_code'];

		if(array_key_exists('channel_response_desc',$parameter)) $this->params .= $parameter['channel_response_desc'];

		if(array_key_exists('masked_pan',$parameter)) $this->params .= $parameter['masked_pan'];

		if(array_key_exists('stored_card_unique_id',$parameter)) $this->params .= $parameter['stored_card_unique_id'];

		if(array_key_exists('backend_invoice',$parameter)) $this->params .= $parameter['backend_invoice'];

		if(array_key_exists('paid_channel',$parameter)) $this->params .= $parameter['paid_channel'];

		if(array_key_exists('paid_agent',$parameter)) $this->params .= $parameter['paid_agent'];

		if(array_key_exists('recurring_unique_id',$parameter)) $this->params .= $parameter['recurring_unique_id'];

		if(array_key_exists('user_defined_1',$parameter)) $this->params .= $parameter['user_defined_1'];

		if(array_key_exists('user_defined_2',$parameter)) $this->params .= $parameter['user_defined_2'];

		if(array_key_exists('user_defined_3',$parameter)) $this->params .= $parameter['user_defined_3'];

		if(array_key_exists('user_defined_4',$parameter)) $this->params .= $parameter['user_defined_4'];

		if(array_key_exists('user_defined_5',$parameter)) $this->params .= $parameter['user_defined_5'];

		if(array_key_exists('browser_info',$parameter)) $this->params .= $parameter['browser_info'];

		if(array_key_exists('ippPeriod',$parameter)) $this->params .= $parameter['ippPeriod'];

		if(array_key_exists('ippInterestType',$parameter)) $this->params .= $parameter['ippInterestType'];

		if(array_key_exists('ippInterestRate',$parameter)) $this->params .= $parameter['ippInterestRate'];

		if(array_key_exists('ippMerchantAbsorbRate',$parameter)) $this->params .= $parameter['ippMerchantAbsorbRate'];

		$secret_key  = $this->pg_2c2p_setting_values['key_secret'];

        $hash = hash_hmac('sha1', $this->params,$secret_key, false); // Generate the hash value.

        if(strcasecmp($hash,$parameter['hash_value']) == 0) return true;

        return false;
    }

	//This function is used to create hash value and sent to 2c2p PG.
    function wc_2c2p_create_hashvalue($parameter){

    	if(array_key_exists('version',$parameter)){
    		if(!empty($parameter['version'])) $this->hashValue .= $parameter['version'];
    	}
    	if(array_key_exists('merchant_id',$parameter)){
    		if(!empty($parameter['merchant_id'])) $this->hashValue .= $parameter['merchant_id'];
    	}
    	if(array_key_exists('payment_description',$parameter)){
    		if(!empty($parameter['payment_description'])) $this->hashValue .= $parameter['payment_description'];
    	}
    	if(array_key_exists('order_id',$parameter)){
    		if(!empty($parameter['order_id'])) $this->hashValue .= $parameter['order_id'];
    	}
    	if(array_key_exists('invoice_no',$parameter)){
    		if(!empty($parameter['invoice_no'])) $this->hashValue .= $parameter['invoice_no'];
    	}
    	if(array_key_exists('currency',$parameter)){
    		if(!empty($parameter['currency'])) $this->hashValue .= $parameter['currency'];
    	}
    	if(array_key_exists('amount',$parameter)){
    		if(!empty($parameter['amount'])) $this->hashValue .= $parameter['amount'];
    	}
    	if(array_key_exists('customer_email',$parameter)){
    		if(!empty($parameter['customer_email'])) $this->hashValue .= $parameter['customer_email'];
    	}
    	if(array_key_exists('pay_category_id',$parameter)){
    		if(!empty($parameter['pay_category_id'])) $this->hashValue .= $parameter['pay_category_id'];
    	}
    	if(array_key_exists('promotion',$parameter)){
    		if(!empty($parameter['promotion'])) $this->hashValue .= $parameter['promotion'];
    	}
    	if(array_key_exists('user_defined_1',$parameter)){
    		if(!empty($parameter['user_defined_1'])) $this->hashValue .= $parameter['user_defined_1'];
    	}
    	if(array_key_exists('user_defined_2',$parameter)){
    		if(!empty($parameter['user_defined_2'])) $this->hashValue .= $parameter['user_defined_2'];
    	}
    	if(array_key_exists('user_defined_3',$parameter)){
    		if(!empty($parameter['user_defined_3'])) $this->hashValue .= $parameter['user_defined_3'];
    	}
    	if(array_key_exists('user_defined_4',$parameter)){
    		if(!empty($parameter['user_defined_4'])) $this->hashValue .= $parameter['user_defined_4'];
    	}
    	if(array_key_exists('user_defined_5',$parameter)){
    		if(!empty($parameter['user_defined_5'])) $this->hashValue .= $parameter['user_defined_5'];
    	}
    	if(array_key_exists('result_url_1',$parameter)){
    		if(!empty($parameter['result_url_1'])) $this->hashValue .= $parameter['result_url_1'];
    	}
    	if(array_key_exists('result_url_2',$parameter)){
    		if(!empty($parameter['result_url_2'])) $this->hashValue .= $parameter['result_url_2'];
    	}
    	if(array_key_exists('enable_store_card',$parameter)){
    		if(!empty($parameter['enable_store_card'])) $this->hashValue .= $parameter['enable_store_card'];
    	}
    	if(array_key_exists('stored_card_unique_id',$parameter)){
    		if(!empty($parameter['stored_card_unique_id'])) $this->hashValue .= $parameter['stored_card_unique_id'];
    	}
    	if(array_key_exists('request_3ds',$parameter)){
    		//if(!empty($parameter['request_3ds'])) $hashValue .= $parameter['request_3ds'];
            $this->hashValue .= $parameter['request_3ds'];
    	}
    	if(array_key_exists('recurring',$parameter)){
    		if(!empty($parameter['recurring'])) $this->hashValue .= $parameter['recurring'];
    	}
    	if(array_key_exists('order_prefix',$parameter)){
    		if(!empty($parameter['order_prefix'])) $this->hashValue .= $parameter['order_prefix'];
    	}
    	if(array_key_exists('recurring_amount',$parameter)){
    		if(!empty($parameter['recurring_amount'])) $this->hashValue .= $parameter['recurring_amount'];
    	}
    	if(array_key_exists('allow_accumulate',$parameter)){
    		if(!empty($parameter['allow_accumulate'])) $this->hashValue .= $parameter['allow_accumulate'];
    	}
    	if(array_key_exists('max_accumulate_amount',$parameter)){
    		if(!empty($parameter['max_accumulate_amount'])) $this->hashValue .= $parameter['max_accumulate_amount'];
    	}
    	if(array_key_exists('recurring_interval',$parameter)){
    		if(!empty($parameter['recurring_interval'])) $this->hashValue .= $parameter['recurring_interval'];
    	}
    	if(array_key_exists('recurring_count',$parameter)){
    		if(!empty($parameter['recurring_count'])) $this->hashValue .= $parameter['recurring_count'];
    	}
    	if(array_key_exists('charge_next_date',$parameter)){
    		if(!empty($parameter['charge_next_date'])) $this->hashValue .= $parameter['charge_next_date'];
    	}
    	if(array_key_exists('charge_on_date',$parameter)){
    		if(!empty($parameter['charge_on_date'])) $this->hashValue .= $parameter['charge_on_date'];
    	}
    	if(array_key_exists('payment_option',$parameter)){
    		if(!empty($parameter['payment_option'])) $this->hashValue .= $parameter['payment_option'];
    	}
    	if(array_key_exists('ipp_interest_type',$parameter)){
    		if(!empty($parameter['ipp_interest_type'])) $this->hashValue .= $parameter['ipp_interest_type'];
    	}
    	if(array_key_exists('payment_expiry',$parameter)){
    		if(!empty($parameter['payment_expiry'])) $this->hashValue .= $parameter['payment_expiry'];
    	}
    	if(array_key_exists('default_lang',$parameter)){
    		if(!empty($parameter['default_lang'])) $this->hashValue .= $parameter['default_lang'];
    	}
    	if(array_key_exists('statement_descriptor',$parameter)){
    		if(!empty($parameter['statement_descriptor'])) $this->hashValue .= $parameter['statement_descriptor'];
    	}

    	$SECRETKEY  = esc_attr($this->pg_2c2p_setting_values['key_secret']);   

        // Generate the hash value.
    	return hash_hmac('sha1', $this->hashValue, $SECRETKEY, false);
    }
}

?>