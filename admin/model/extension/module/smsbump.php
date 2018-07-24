<?php 
class ModelExtensionModuleSmsbump extends Model {
	private $module_path = 'module/smsbump';

	private function init()
	{
		if (version_compare(VERSION , '2.3.0.0' , '>=')) {
			$this->module_path = 'extension/module/smsbump';
		}
	}

	public function getSetting($group, $store_id = 0) {
		$this->init();
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
  
	public function editSetting($group, $data, $store_id = 0) {
		$this->init();
		$this->db->query("DELETE FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `group` = '" . $this->db->escape($group) . "'");
		foreach ($data as $key => $value) {
		  if (!is_array($value)) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape($value) . "'");
		  } else {
			$this->db->query("INSERT INTO " . DB_PREFIX . "setting SET store_id = '" . (int)$store_id . "', `group` = '" . $this->db->escape($group) . "', `key` = '" . $this->db->escape($key) . "', `value` = '" . $this->db->escape(serialize($value)) . "', serialized = '1'");
		  }
		}
	}
	
	public static function SmsBumpCallback($response) {
		global $log;
		
		if ($response['status'] == 'Queued') {
			
		} else if ($response['status'] == 'error') {
			$log->write('SMSBump: '.$response['message']);
		}
	}

	public function sendCheck($number = '')	{
		$this->init();
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
	
	public function getDedicatedNumbers($apiKey){
		$this->init();
		$url = 'https://api.smsbump.com/usernumbers/'.$apiKey.'.json';
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		
		$reply = json_decode(curl_exec($ch), true);
	
		curl_close($ch);
		
		 if(!empty($reply['data']['usernumbers'])){
		 	return $reply['data']['usernumbers'];
		 } else {
		 	return array();
		 }
		
	}

	public function install() {
	   // Install Code
	   $this->init();
	   $this->load->model('extension/event');

	   if (version_compare(VERSION, '2.2.0.0', ">=")) {
		   	$this->model_extension_event->addEvent("smsbump", 'catalog/controller/checkout/success/before', $this->module_path.'/onCheckout');
	    	$this->model_extension_event->addEvent("smsbump", 'catalog/model/checkout/order/addOrderHistory/after', $this->module_path.'/onHistoryChange');
	    	$this->model_extension_event->addEvent("smsbump", 'catalog/model/account/customer/addCustomer/after', $this->module_path.'/onRegister');	   	
	   } else {
		   	//$this->model_extension_event->addEvent('smsbump', 'post.order.add', 'module/smsbump/onCheckout');
	    	$this->model_extension_event->addEvent('smsbump', 'post.order.history.add', $this->module_path.'/onHistoryChange');
	    	$this->model_extension_event->addEvent('smsbump', 'post.customer.add', $this->module_path.'/onRegister');
	   }
	} 
  
	public function uninstall() {
		// Uninstall Code
		$this->load->model('setting/setting');		
		$this->load->model('setting/store');
		$this->model_setting_setting->deleteSetting('smsbump_module',0);
		$stores=$this->model_setting_store->getStores();
		foreach ($stores as $store) {
			$this->model_setting_setting->deleteSetting('smsbump_module', $store['store_id']);
		}
        $this->load->model('extension/event');
    	$this->model_extension_event->deleteEvent('smsbump');
	}
	
	// SMSBump Bulk Messages
	public function getTotalCustomers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}   
		if (!empty($data['filter_ip'])) {
			$implode[] = "customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}   
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}           
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}       
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getCustomers($data = array()) {
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}
		if (isset($data['filter_newsletter']) && !is_null($data['filter_newsletter'])) {
			$implode[] = "c.newsletter = '" . (int)$data['filter_newsletter'] . "'";
		}   
		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}   
		if (!empty($data['filter_ip'])) {
			$implode[] = "c.customer_id IN (SELECT customer_id FROM " . DB_PREFIX . "customer_ip WHERE ip = '" . $this->db->escape($data['filter_ip']) . "')";
		}   
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}   
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "c.approved = '" . (int)$data['filter_approved'] . "'";
		}   
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(c.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'name',
			'c.email',
			'customer_group',
			'c.status',
			'c.approved',
			'c.ip',
			'c.date_added'
		);  
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];   
		} else {
			$sql .= " ORDER BY name";   
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		$query = $this->db->query($sql);
		return $query->rows;    
	}
	
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");
		return $query->row;
	}
	
	public function getTotalAffiliates($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "affiliate";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
		}   
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}           
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "approved = '" . (int)$data['filter_approved'] . "'";
		}       
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$query = $this->db->query($sql);
		return $query->row['total'];
	}
	
	public function getAffiliates($data = array()) {
		$sql = "SELECT *, CONCAT(a.firstname, ' ', a.lastname) AS name, (SELECT SUM(at.amount) FROM " . DB_PREFIX . "affiliate_transaction at WHERE at.affiliate_id = a.affiliate_id GROUP BY at.affiliate_id) AS balance FROM " . DB_PREFIX . "affiliate a";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(a.firstname, ' ', a.lastname) LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(a.email) = '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "'";
		}
		if (!empty($data['filter_code'])) {
			$implode[] = "a.code = '" . $this->db->escape($data['filter_code']) . "'";
		}
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "a.status = '" . (int)$data['filter_status'] . "'";
		}   
		if (isset($data['filter_approved']) && !is_null($data['filter_approved'])) {
			$implode[] = "a.approved = '" . (int)$data['filter_approved'] . "'";
		}       
		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(a.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}
		$sort_data = array(
			'name',
			'a.email',
			'a.code',
			'a.status',
			'a.approved',
			'a.date_added'
		);  
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];   
		} else {
			$sql .= " ORDER BY name";   
		}
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}
		$query = $this->db->query($sql);
		return $query->rows;    
	}
	
	public function getAffiliate($affiliate_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "affiliate WHERE affiliate_id = '" . (int)$affiliate_id . "'");
		return $query->row;
	}
	
	public function getTotalTelephonesByProductsOrdered($products) {
		$implode = array();
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}
		$query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0'");
		return $query->row['total'];
	}
	
	public function getTelephonesByProductsOrdered($products, $start, $end) {
		$implode = array();
		foreach ($products as $product_id) {
			$implode[] = "op.product_id = '" . (int)$product_id . "'";
		}
		$query = $this->db->query("SELECT DISTINCT telephone FROM `" . DB_PREFIX . "order` o LEFT JOIN " . DB_PREFIX . "order_product op ON (o.order_id = op.order_id) WHERE (" . implode(" OR ", $implode) . ") AND o.order_status_id <> '0' LIMIT " . (int)$start . "," . (int)$end);
		return $query->rows;
	}
	// SMSBump Bulk Messages

  }
?>