<?php
class ControllerExtensionModuleSmsbump extends Controller {
	private $call_model = 'model_module_smsbump';
	private $module_path = 'module/smsbump';
	private $model_class = 'ModelModuleSmsbump';
	private $smsbump_model;
	
	public function __construct($registry){
		parent::__construct($registry);
		//cross version check and module specific declarations
		if (version_compare(VERSION, '2.3.0.0', '>=')) {
			$this->call_model = 'model_extension_module_smsbump';
			$this->module_path = 'extension/module/smsbump';
			$this->model_class = 'ModelExtensionModuleSmsbump';
		}
		//SMSBump model		 
		$this->load->model($this->module_path);
		//Settings model
		$this->load->model('setting/setting');
		
		$this->smsbump_model = $this->{$this->call_model};
	}

	public function onCheckout($data = 0) {
      	if (isset($this->session->data['order_id'])) {        	
           $order_id = $this->session->data['order_id'];
        } else {
            $order_id = 0;
        } 
		if ($order_id != 0 && version_compare(VERSION,'2.3.0.1', '>')) {
			$this->smsbump_model->SMSBumpOnCheckout($order_id);
		}
    }

    public function onHistoryChange($order_id, $route = '', $data = '') {
    	if (version_compare(VERSION, '2.2.0.0', ">=") && version_compare(VERSION, '2.3.0.0', "<")) {
    		$order_id = $data;    		
    	} else if(version_compare(VERSION, '2.3.0.0', ">=")){
    		$order_id = $route[0];
    	}

    	//Send SMS when the status order is changed
    	$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);	
		
		$SMSBump = $this->model_setting_setting->getSetting('SMSBump', $order_info['store_id']);
		
		if(strcmp(VERSION,"2.1.0.1") < 0) {
			$this->load->library('smsbump');
		}

    	if(isset($SMSBump) && ($SMSBump['SMSBump']['Enabled'] == 'yes') && (!empty($SMSBump['SMSBump']['APIKey'])) && ($SMSBump['SMSBump']['OrderStatusChange']['Enabled'] == 'yes')) {

    		$result = $this->db->query("SELECT count(*) as counter FROM " . DB_PREFIX ."order_history WHERE order_id = ". $order_id);
			if ($order_info['order_status_id'] && $result->row['counter'] > 1 && (!empty($SMSBump['SMSBump']['OrderStatusChange']['OrderStatus']) && (in_array($order_info['order_status_id'], $SMSBump['SMSBump']['OrderStatusChange']['OrderStatus'])))) {
				if(isset($order_info['order_status']))
					$Status = $order_info['order_status'];
				else
					$Status = "";
				
				$language 		= $order_info['language_id'];	
				$last_order_status = $this->smsbump_model->getLastOrderStatuses($order_id,$language);
                
                $Status1 =      !empty($last_order_status[1]['name']) ? $last_order_status[1]['name'] : '';
               
                $Status2 =      $Status;
				$original		= array("{SiteName}","{OrderID}","{Status}","{Status1}","{Status2}","{StatusFrom}","{StatusTo}");
				$replace		= array($this->config->get('config_name'), $order_id, $Status,$Status1,$Status2,$Status1, $Status2);

				$message = str_replace($original, $replace, $SMSBump['SMSBump']['OrderStatusChangeText'][$language]);
				$phone = $order_info['telephone'];
				
				$sendCheck = $this->smsbump_model->sendCheck($phone);
				$from = '';
				if (isset($SMSBump['SMSBump']['UseDedicatedNumberForTransactiona']) && $SMSBump['SMSBump']['UseDedicatedNumberForTransactiona'] == 'on') {
					$from = $SMSBump['SMSBump']['SelectedDedicatedNumber'];
				} else {
					$from = $SMSBump['SMSBump']['From'];
				}
				
				if ($sendCheck) {
					SmsBump::sendMessage(array(
						'APIKey' => $SMSBump['SMSBump']['APIKey'],
						'to' => $sendCheck,
						'from' => $from,
						'message' => $message,
						'callback' => array($this->model_class, 'SmsBumpCallback')
					));
				}
			}
		}
    }

    public function onRegister() {
    	if (func_num_args() > 1) {
    		$temp_id = !is_array(func_get_arg(1)) ? func_get_arg(1) : func_get_arg(2);
    	} else {
    		$temp_id = func_get_arg(0);
    	}
    	$customer_id = $temp_id;
    
		$this->load->model('setting/setting');

		$SMSBump = $this->model_setting_setting->getSetting('SMSBump', $this->config->get('store_id'));
		if(strcmp(VERSION,"2.1.0.1") < 0) {
			$this->load->library('smsbump');
		}
		//Send SMS to the admin when new user is registered
    	if(isset($SMSBump) && ($SMSBump['SMSBump']['Enabled'] == 'yes') && (!empty($SMSBump['SMSBump']['APIKey'])) && ($SMSBump['SMSBump']['AdminRegister']['Enabled'] == 'yes')) {
				$customer = $this->db->query("SELECT firstname,lastname,telephone FROM `" . DB_PREFIX ."customer` WHERE customer_id = ".(int)$customer_id);

				if ($customer->row) {
					$nameCustomer = $customer->row['firstname']." ".$customer->row['lastname'];
				} else {
					$nameCustomer = '';
				}
					
				$original		= array("{SiteName}","{CustomerName}");
				$replace		= array($this->config->get('config_name'), $nameCustomer);

				$message = str_replace($original, $replace, $SMSBump['SMSBump']['AdminRegisterText']);

				$adminNumbers = isset($SMSBump['SMSBump']['StoreOwnerPhoneNumber']) ? $SMSBump['SMSBump']['StoreOwnerPhoneNumber'] : array();
				
				foreach($adminNumbers as $phone) {
					if (!empty($phone)) {
						SmsBump::sendMessage(array(
							'APIKey' => $SMSBump['SMSBump']['APIKey'],
							'to' => $phone,
							'from' => $SMSBump['SMSBump']['From'],
							'message' => $message,
							'callback' => array($this->model_class, 'SmsBumpCallback')
						));
					}
				}
		}

		//Send SMS to the user when the registration is successful
		if(isset($SMSBump) && ($SMSBump['SMSBump']['Enabled'] == 'yes') && (!empty($SMSBump['SMSBump']['APIKey'])) && ($SMSBump['SMSBump']['CustomerRegister']['Enabled'] == 'yes')) {
			$customer = $this->db->query("SELECT firstname,lastname,telephone FROM `" . DB_PREFIX ."customer` WHERE customer_id = ".(int)$customer_id);

			if ($customer->row) {
				$phone = $customer->row['telephone'];
				$nameCustomer = $customer->row['firstname']." ".$customer->row['lastname'];
			} else {
				$phone = '';
				$nameCustomer = '';
			}					
			
			$language 		= $this->config->get('config_language_id');
			$original		= array("{StoreName}","{CustomerName}");
			$replace		= array($this->config->get('config_name'), $nameCustomer);
			
			$message = str_replace($original, $replace, $SMSBump['SMSBump']['CustomerRegisterText'][$language]);
			
			$sendCheck = $this->smsbump_model->sendCheck($phone);
			$from = '';
			if (isset($SMSBump['SMSBump']['UseDedicatedNumberForTransactiona']) && $SMSBump['SMSBump']['UseDedicatedNumberForTransactiona'] == 'on') {
				$from = $SMSBump['SMSBump']['SelectedDedicatedNumber'];
			} else {
				$from = $SMSBump['SMSBump']['From'];
			}

			if ($sendCheck) {
				SmsBump::sendMessage(array(
					'APIKey' => $SMSBump['SMSBump']['APIKey'],
					'to' => $sendCheck,
					'from' => $from,
					'message' => $message,
					'callback' => array($this->model_class, 'SmsBumpCallback')
				));
			}
		}	
    }

    private function log($text) {
		$log = new Log("smsbump_error_log.txt");
		$log->write($text);	
	}
}