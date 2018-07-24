<?php
class ModelExtensionModuleSmsbump extends Model {
  
  	public function getSetting($group, $store_id) {
    	$data = array(); 
    	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
    	foreach ($query->rows as $result) {
      		if (!$result['serialized']) {
        		$data[$result['key']] = $result['value'];
      		} else {
        		$data[$result['key']] = unserialize($result['value']);
      		}
    	} 
    	return $data;
  	}

	public static function SmsBumpCallback($response) {
		$log = new Log('smsbump_callback_log.txt');		
		if ($response['status'] == 'Queued') {
			$log->write($response['message']);		
		} else if ($response['status'] == 'error') {
			$log->write($response['message']);
		}
	}

	public function SMSBumpOnCheckout($order_id){
		//Get order info
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);	
		//Get SMSBump settings
		$this->load->model('setting/setting');		
		$SMSBump = $this->model_setting_setting->getSetting('SMSBump', $order_info['store_id']);

		if(strcmp(VERSION,"2.1.0.1") < 0) {
			//load SMSBump library
			$this->load->library('smsbump');
		}
		
		//Send SMS to the customer
		if(isset($SMSBump) && ($SMSBump['SMSBump']['Enabled'] == 'yes') && (!empty($SMSBump['SMSBump']['APIKey'])) && ($SMSBump['SMSBump']['CustomerPlaceOrder']['Enabled'] == 'yes')) {

			if (!empty($order_info['telephone'])) {
				$phone = $order_info['telephone'];
			} else {
				$phone = '';
			}
			$language 		= $this->config->get('config_language_id');
				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
	
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
	
				$replace = array(
					'firstname' => $order_info['payment_firstname'],
					'lastname'  => $order_info['payment_lastname'],
					'company'   => $order_info['payment_company'],
					'address_1' => $order_info['payment_address_1'],
					'address_2' => $order_info['payment_address_2'],
					'city'      => $order_info['payment_city'],
					'postcode'  => $order_info['payment_postcode'],
					'zone'      => $order_info['payment_zone'],
					'zone_code' => $order_info['payment_zone_code'],
					'country'   => $order_info['payment_country']
				);
	
				$payment_address = str_replace($find, $replace, $format);
	
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
				} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
	
				$find = array(
					'{firstname}',
					'{lastname}',
					'{company}',
					'{address_1}',
					'{address_2}',
					'{city}',
					'{postcode}',
					'{zone}',
					'{zone_code}',
					'{country}'
				);
	
				$replace = array(
					'firstname' => $order_info['shipping_firstname'],
					'lastname'  => $order_info['shipping_lastname'],
					'company'   => $order_info['shipping_company'],
					'address_1' => $order_info['shipping_address_1'],
					'address_2' => $order_info['shipping_address_2'],
					'city'      => $order_info['shipping_city'],
					'postcode'  => $order_info['shipping_postcode'],
					'zone'      => $order_info['shipping_zone'],
					'zone_code' => $order_info['shipping_zone_code'],
					'country'   => $order_info['shipping_country']
				);
	
				$shipping_address = str_replace($find, $replace, $format);
			
			$original		= array(
				"{OrderID}",
				"{SiteName}",
				"{CartTotal}",
				"{ShippingAddress}",
				"{ShippingMethod}",
				"{PaymentAddress}",
				"{PaymentMethod}"
			);

			$replace		= array(
				$order_id,
				$this->config->get('config_name'),
				$order_info['total'],
				$shipping_address,
				$order_info['shipping_method'],
				$payment_address,
				$order_info['payment_method']
			);

			$message = str_replace($original, $replace, $SMSBump['SMSBump']['CustomerPlaceOrderText'][$language]);
			
			$sendCheck = $this->sendCheck($phone);

			$from = '';
			if (isset($SMSBump['SMSBump']['UseDedicatedNumberForTransactiona']) && $SMSBump['SMSBump']['UseDedicatedNumberForTransactiona'] == 'on') {
				$from = $SMSBump['SMSBump']['SelectedDedicatedNumber'];
			} else {
				$from = $SMSBump['SMSBump']['From'];
			}

			if (!empty($phone) && $sendCheck) {
				SmsBump::sendMessage(array(
					'APIKey' => $SMSBump['SMSBump']['APIKey'],
					'to' => $sendCheck,
					'from' => $from,
					'message' => $message,
					'callback' => array('ModelModuleSmsbump', 'SmsBumpCallback')
				));

				$this->session->data["smsbump_lastorder"]['price'] = $order_info['total'];
				$this->session->data["smsbump_lastorder"]['time'] = date('m/d/Y h:i:s a', time());
			}
	
		}

		//Send SMS to the admin
		if(isset($SMSBump) && ($SMSBump['SMSBump']['Enabled'] == 'yes') && (!empty($SMSBump['SMSBump']['APIKey'])) && ($SMSBump['SMSBump']['AdminPlaceOrder']['Enabled'] == 'yes')) {
				
				if ($order_info['order_status_id'] != 0) {
					if (!empty($order_info['telephone'])) {
						$phone = $order_info['telephone'];
					} else {
						$phone = '';
					}
					$language 		= $this->config->get('config_language_id');
					$original		= array("{OrderID}","{SiteName}","{CartTotal}");
					$replace		= array($order_id, $this->config->get('config_name'),$order_info['total']);

					$message = str_replace($original, $replace, $SMSBump['SMSBump']['AdminPlaceOrderText']);
    				
					$adminNumbers = isset($SMSBump['SMSBump']['StoreOwnerPhoneNumber']) ? $SMSBump['SMSBump']['StoreOwnerPhoneNumber'] : array();
					
					foreach($adminNumbers as $phone) {						
						if (!empty($phone)) {
							SmsBump::sendMessage(array(
								'APIKey' => $SMSBump['SMSBump']['APIKey'],
								'to' => $phone,
								'from' => $SMSBump['SMSBump']['From'],
								'message' => $message,
								'callback' => array('ModelModuleSmsbump', 'SmsBumpCallback')
							));
						}
					}
				}
		}
	
	}
	public function getLastOrderStatuses($order_id, $language_id){
        $order_statuses = array();
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "' ORDER BY `order_history_id` DESC LIMIT 0, 2");
       
        foreach($query->rows as $result){
            $order_statuses[] = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" .(int)$result['order_status_id']."' AND `language_id` = '" . $language_id . "'")->row;
           
        }
        return  $order_statuses;
    }
    
	public function sendCheck($number = '')	{
		$this->load->model('setting/setting');
		$SMSBump = $this->model_setting_setting->getSetting('SMSBump', $this->config->get('config_store_id'));

		$number = str_replace(' ', '', $number);
		$number = str_replace('-', '', $number);
		$number = str_replace('(', '', $number);
		$number = str_replace(')', '', $number);

		if (isset($SMSBump['SMSBump']['NumberPrefix']) && !empty($SMSBump['SMSBump']['NumberPrefix']) && ($SMSBump['SMSBump']['PhoneNumberPrefix']=='yes')){
			$prefix = $SMSBump['SMSBump']['NumberPrefix'];

			$numberCheck = ltrim($number, '+');	
			$numberCheck = ltrim($numberCheck, '0');	
			$prefixCheck = ltrim($prefix, '+');
		
			$formattedNumber = false;

			if(is_numeric($number)) {
				if ((strpos($number,'+') === 0 || strpos($number, '00') === 0) && strpos($numberCheck,$prefixCheck) === 0) {
					$formattedNumber = '+'.$numberCheck;
				} else if ((strpos($number,'+') === 0 || strpos($number, '00') === 0) && strpos($numberCheck,$prefixCheck) !== 0){
				 	$formattedNumber = false;		
				} else if (strpos($numberCheck, $prefixCheck) !== 0) {
					$formattedNumber = $prefix.$numberCheck;
				} else if((strpos($number,'+') !== 0) && (strpos($number,'00') !== 0) && strpos($numberCheck,$prefixCheck) === 0) {
					$formattedNumber = '+'.$number;
				} else {
					$formattedNumber = false;
				}
			}
			return $formattedNumber;
		}	
		return $number;	
	}

	public function debug($email, $event, $message)	{
		$mail = new Mail();
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($email);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
		$mail->setSubject($event);
		$mail->setText($message);
		$mail->send();
	}
}
?>