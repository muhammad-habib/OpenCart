<?php
class ControllerExtensionModuleSmsbump extends Controller {

	/**
	 * @property   String $module_path String containing the path expression for SMSBump files.
	 * @property   String $call_model String containing the call to SMSBump model.
	 * @property   ModelExtensisionModuleSmsbump $smsbump_model Object containing the loaded SMSBump model.
	 */
	private $data = array();
	private $version = '2.6.3';	
	private $call_model = 'model_extension_module_smsbump';
	private $module_path = 'module/smsbump';
	private $smsbump_model;

	/**
	 * SMSBump Controller Constructor
	 * initialize necessary dependencies from the OpenCart framework.
	 */
	public function __construct($registry){
		parent::__construct($registry);
		//cross version check and module specific declarations
		if (version_compare(VERSION, '2.3.0.0', '>=')) {
			$this->call_model = 'model_extension_module_smsbump';
			$this->module_path = 'extension/module/smsbump';
			$this->model_class = 'ModelExtensionModuleSmsbump';
		}
		$this->load->model($this->module_path);
		$this->smsbump_model = $this->{$this->call_model};
    	$this->load->language($this->module_path);
    	//Loading framework models
		$this->load->model('setting/store');
        $this->load->model('localisation/language');
        $this->load->model('design/layout');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		//Module specific resources
        $this->document->addStyle('view/stylesheet/smsbump/smsbump.css');
		$this->document->addStyle('view/stylesheet/smsbump/select2.css');
		$this->document->addScript('view/javascript/smsbump/smsbump.js');
		$this->document->addScript('view/javascript/smsbump/select2.min.js');
		$this->document->addScript('view/javascript/smsbump/charactercounter.js');
		//global module variables
		$this->data['module_path'] = $this->module_path;
		$this->data['catalogURL'] = $this->getCatalogURL();
	    
	}
    public function index() { 
        if(!isset($this->request->get['store_id'])) {
           $this->request->get['store_id'] = 0; 
        }
		$this->document->setTitle($this->language->get('heading_title').' '.$this->version);	
        $store = $this->getCurrentStore($this->request->get['store_id']);
		
        if (($this->request->server['REQUEST_METHOD'] == 'POST')) {

            if (!$this->user->hasPermission('modify', $this->module_path)) {
				$this->error['warning'] = $this->language->get('error_permission');
            }

            if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
                $this->request->post['SMSBump']['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
            }

            if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
                $this->request->post['SMSBump']['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']), true);
            }

            if (!$this->user->hasPermission('modify', $this->module_path)) {
				$this->session->data['error'] = 'You do not have permissions to edit this module!';	
			} else {
				$this->model_setting_setting->editSetting('SMSBump', $this->request->post, $this->request->post['store_id']);
				$this->session->data['success'] = $this->language->get('text_success');	
			}
            $this->response->redirect($this->url->link($this->module_path, 'token=' . $this->session->data['token'].'&store_id='.$store['store_id'], 'SSL'));
        }
		
		$this->data['image'] = 'no_image.jpg';
		$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

 		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->data['breadcrumbs']   = array();
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', 'SSL'),
        );
        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link($this->module_path, 'token=' . $this->session->data['token'].'$type=module', 'SSL'),
        );
		
        $languageVariables = array(
            'entry_code',
            'heading_title',
            'error_input_form',
            'entry_yes',
            'entry_no',
            'text_default',
            'text_enabled',
            'text_disabled',
            'text_text',
            'save_changes',
            'button_cancel',
            'text_settings',
            'button_add',
            'button_edit',            
            'button_remove',
            'text_special_duration'
          );
       
        foreach ($languageVariables as $languageVariable) {
            $this->data[$languageVariable] = $this->language->get($languageVariable);
        }
		$this->data['heading_title'] = $this->language->get('heading_title').' '.$this->version;
        $this->data['stores'] = array_merge(array(0 => array('store_id' => '0', 'name' => $this->config->get('config_name') . ' ' . $this->data['text_default'], 'url' => HTTP_SERVER, 'ssl' => HTTPS_SERVER)), $this->model_setting_store->getStores());
        $this->data['error_warning']          = '';  
        $this->data['languages']    		  = $this->model_localisation_language->getLanguages();
        foreach ($this->data['languages'] as $key => $value) {
			if(version_compare(VERSION, '2.2.0.0', "<")) {
				$this->data['languages'][$key]['flag_url'] = 'view/image/flags/'.$this->data['languages'][$key]['image'];
			} else {
				$this->data['languages'][$key]['flag_url'] = 'language/'.$this->data['languages'][$key]['code'].'/'.$this->data['languages'][$key]['code'].'.png"';
			}
		}
        $this->data['store']                  = $store;
        $this->data['token']                  = $this->session->data['token'];
        $this->data['action']                 = $this->url->link('extension/module/smsbump', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['saveApiKey']             = $this->url->link($this->module_path.'/saveApiKey', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['validatePhoneNumberUrl'] = $this->url->link($this->module_path.'/validatePhone', 'token=' . $this->session->data['token'], 'SSL');
        $this->data['cancel']                 = $this->url->link('extension/extension', 'token=' . $this->session->data['token'].'&type=module', 'SSL');
        $this->data['data']                   = $this->model_setting_setting->getSetting('SMSBump', $store['store_id']);
       

        if (!empty($this->data['data']['SMSBump']['CountryCode'])) {
        	$this->data['selected_country'] = $this->data['data']['SMSBump']['CountryCode'];
        } else{
        	$this->data['selected_country'] = 'us';
        }

	    

		
        if ( !isset($this->data['data']['SMSBump']['APIKey']) || (empty($this->data['data']['SMSBump']['APIKey'])) ) { 
            $this->data['error_warning'] = 'In order to use SMSBump, we will need you to enter your email and phone number!';
            $this->data['status'] = false;
        } else {
			$this->document->addStyle('view/javascript/smsbump/jquery/css/ui-lightness/jquery-ui.min.css');
			$this->document->addScript('view/javascript/smsbump/jquery/js/jquery-ui.min.js');
			$this->data['status'] = true;
			$apiKey = $this->data['data']['SMSBump']['APIKey'];
		}

		if (isset($apiKey) && !empty($apiKey)) {
			$this->data['dedicated_numbers'] = $this->smsbump_model->getDedicatedNumbers($apiKey);	
		}

		//Get all countries
		$this->data['countries'] = $this->getCountries();
		
		
		// SMS Bulk Start
		if(strcmp(VERSION,"2.1.0.1") < 0) {
			$this->load->model('sale/customer_group');
			$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups(0);
			$this->data['customer_autocomplete_url'] = $this->url->link('sale/customer/autocomplete','','SSL');
		} else {
			$this->load->model('customer/customer_group');
			$this->data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups(0);
			$this->data['customer_autocomplete_url'] = $this->url->link('customer/customer/autocomplete','','SSL');
		}
		// SMS Bulk End

		if(!empty($_SERVER['HTTP_REFERER'])) {
			$referer = $_SERVER['HTTP_REFERER'];
			$url = parse_url($referer);
			
			if(!empty($url['host']) && strpos($url['host'],'paypal.com') !== false) {
				$this->data['success'] = 'The payment has been sent! You may need to wait several minutes for your account balance to be updated.';
			}
		}
		

        $this->data['header']  					 = $this->load->controller('common/header');
		$this->data['column_left']				= $this->load->controller('common/column_left');
		$this->data['footer']					 = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view($this->module_path.'.tpl', $this->data));
    }


    public function saveApiKey() {
    	header("Content-Type: application/json", true);
    	if(isset($this->request->get['store_id']) && !empty($this->request->get['api_key'])) {
	    	$data = array(
	    		'store_id' => $this->request->get['store_id'],
	    		'SMSBump' => array (
	    			'APIKey' => $this->request->get['api_key'],
	    			'APISecret' => $this->request->get['api_secret'],
	    			'NumberPrefix' => $this->request->get['register_country_prefix'],
	    			'CountryCode' => $this->request->get['register_country_label']
	    		)
	    	);

	    	$this->model_setting_setting->editSetting('SMSBump', $data, $data['store_id']);
	    	$result = array(
	    		'status' => 'success',
	    		'redirect_url' => $this->url->link($this->module_path, 'token=' . $this->session->data['token'].'&store_id='.$data['store_id'], 'SSL')
	    	);
	    	
			$this->response->setOutput(json_encode($result));
    	} else {
    		$result = array(
	    		'status' => 'error'
	    	);
	    	$this->response->setOutput(json_encode($result));
    	} 
		
    }

    private function getCatalogURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_CATALOG;
        } else {
            $storeURL = HTTP_CATALOG;
        } 
        return $storeURL;
    }

    private function getServerURL() {
        if (isset($_SERVER['HTTPS']) && (($_SERVER['HTTPS'] == 'on') || ($_SERVER['HTTPS'] == '1'))) {
            $storeURL = HTTPS_SERVER;
        } else {
            $storeURL = HTTP_SERVER;
        } 
        return $storeURL;
    }

    private function getCurrentStore($store_id) {    
        if($store_id && $store_id != 0) {
            $store = $this->model_setting_store->getStore($store_id);
        } else {
            $store['store_id'] = 0;
            $store['name'] = $this->config->get('config_name');
            $store['url'] = $this->getCatalogURL(); 
        }
        return $store;
    }
    
    public function install() {
	    $this->smsbump_model->install();
    }

    public function uninstall() {
        $this->smsbump_model->uninstall();
    }
	
	private function getCountries(){
		$countries = array();
		$countries[] = array("code"=>"AF","name"=>"Afghanistan","d_code"=>"+93");
		$countries[] = array("code"=>"AL","name"=>"Albania","d_code"=>"+355");
		$countries[] = array("code"=>"DZ","name"=>"Algeria","d_code"=>"+213");
		$countries[] = array("code"=>"AS","name"=>"American Samoa","d_code"=>"+1");
		$countries[] = array("code"=>"AD","name"=>"Andorra","d_code"=>"+376");
		$countries[] = array("code"=>"AO","name"=>"Angola","d_code"=>"+244");
		$countries[] = array("code"=>"AI","name"=>"Anguilla","d_code"=>"+1");
		$countries[] = array("code"=>"AG","name"=>"Antigua","d_code"=>"+1");
		$countries[] = array("code"=>"AR","name"=>"Argentina","d_code"=>"+54");
		$countries[] = array("code"=>"AM","name"=>"Armenia","d_code"=>"+374");
		$countries[] = array("code"=>"AW","name"=>"Aruba","d_code"=>"+297");
		$countries[] = array("code"=>"AU","name"=>"Australia","d_code"=>"+61");
		$countries[] = array("code"=>"AT","name"=>"Austria","d_code"=>"+43");
		$countries[] = array("code"=>"AZ","name"=>"Azerbaijan","d_code"=>"+994");
		$countries[] = array("code"=>"BH","name"=>"Bahrain","d_code"=>"+973");
		$countries[] = array("code"=>"BD","name"=>"Bangladesh","d_code"=>"+880");
		$countries[] = array("code"=>"BB","name"=>"Barbados","d_code"=>"+1");
		$countries[] = array("code"=>"BY","name"=>"Belarus","d_code"=>"+375");
		$countries[] = array("code"=>"BE","name"=>"Belgium","d_code"=>"+32");
		$countries[] = array("code"=>"BZ","name"=>"Belize","d_code"=>"+501");
		$countries[] = array("code"=>"BJ","name"=>"Benin","d_code"=>"+229");
		$countries[] = array("code"=>"BM","name"=>"Bermuda","d_code"=>"+1");
		$countries[] = array("code"=>"BT","name"=>"Bhutan","d_code"=>"+975");
		$countries[] = array("code"=>"BO","name"=>"Bolivia","d_code"=>"+591");
		$countries[] = array("code"=>"BA","name"=>"Bosnia and Herzegovina","d_code"=>"+387");
		$countries[] = array("code"=>"BW","name"=>"Botswana","d_code"=>"+267");
		$countries[] = array("code"=>"BR","name"=>"Brazil","d_code"=>"+55");
		$countries[] = array("code"=>"IO","name"=>"British Indian Ocean Territory","d_code"=>"+246");
		$countries[] = array("code"=>"VG","name"=>"British Virgin Islands","d_code"=>"+1");
		$countries[] = array("code"=>"BN","name"=>"Brunei","d_code"=>"+673");
		$countries[] = array("code"=>"BG","name"=>"Bulgaria","d_code"=>"+359");
		$countries[] = array("code"=>"BF","name"=>"Burkina Faso","d_code"=>"+226");
		$countries[] = array("code"=>"MM","name"=>"Burma Myanmar" ,"d_code"=>"+95");
		$countries[] = array("code"=>"BI","name"=>"Burundi","d_code"=>"+257");
		$countries[] = array("code"=>"KH","name"=>"Cambodia","d_code"=>"+855");
		$countries[] = array("code"=>"CM","name"=>"Cameroon","d_code"=>"+237");
		$countries[] = array("code"=>"CA","name"=>"Canada","d_code"=>"+1");
		$countries[] = array("code"=>"CV","name"=>"Cape Verde","d_code"=>"+238");
		$countries[] = array("code"=>"KY","name"=>"Cayman Islands","d_code"=>"+1");
		$countries[] = array("code"=>"CF","name"=>"Central African Republic","d_code"=>"+236");
		$countries[] = array("code"=>"TD","name"=>"Chad","d_code"=>"+235");
		$countries[] = array("code"=>"CL","name"=>"Chile","d_code"=>"+56");
		$countries[] = array("code"=>"CN","name"=>"China","d_code"=>"+86");
		$countries[] = array("code"=>"CO","name"=>"Colombia","d_code"=>"+57");
		$countries[] = array("code"=>"KM","name"=>"Comoros","d_code"=>"+269");
		$countries[] = array("code"=>"CK","name"=>"Cook Islands","d_code"=>"+682");
		$countries[] = array("code"=>"CR","name"=>"Costa Rica","d_code"=>"+506");
		$countries[] = array("code"=>"CI","name"=>"Côte d'Ivoire" ,"d_code"=>"+225");
		$countries[] = array("code"=>"HR","name"=>"Croatia","d_code"=>"+385");
		$countries[] = array("code"=>"CU","name"=>"Cuba","d_code"=>"+53");
		$countries[] = array("code"=>"CY","name"=>"Cyprus","d_code"=>"+357");
		$countries[] = array("code"=>"CZ","name"=>"Czech Republic","d_code"=>"+420");
		$countries[] = array("code"=>"CD","name"=>"Democratic Republic of Congo","d_code"=>"+243");
		$countries[] = array("code"=>"DK","name"=>"Denmark","d_code"=>"+45");
		$countries[] = array("code"=>"DJ","name"=>"Djibouti","d_code"=>"+253");
		$countries[] = array("code"=>"DM","name"=>"Dominica","d_code"=>"+1");
		$countries[] = array("code"=>"DO","name"=>"Dominican Republic","d_code"=>"+1");
		$countries[] = array("code"=>"EC","name"=>"Ecuador","d_code"=>"+593");
		$countries[] = array("code"=>"EG","name"=>"Egypt","d_code"=>"+20");
		$countries[] = array("code"=>"SV","name"=>"El Salvador","d_code"=>"+503");
		$countries[] = array("code"=>"GQ","name"=>"Equatorial Guinea","d_code"=>"+240");
		$countries[] = array("code"=>"ER","name"=>"Eritrea","d_code"=>"+291");
		$countries[] = array("code"=>"EE","name"=>"Estonia","d_code"=>"+372");
		$countries[] = array("code"=>"ET","name"=>"Ethiopia","d_code"=>"+251");
		$countries[] = array("code"=>"FK","name"=>"Falkland Islands","d_code"=>"+500");
		$countries[] = array("code"=>"FO","name"=>"Faroe Islands","d_code"=>"+298");
		$countries[] = array("code"=>"FM","name"=>"Federated States of Micronesia","d_code"=>"+691");
		$countries[] = array("code"=>"FJ","name"=>"Fiji","d_code"=>"+679");
		$countries[] = array("code"=>"FI","name"=>"Finland","d_code"=>"+358");
		$countries[] = array("code"=>"FR","name"=>"France","d_code"=>"+33");
		$countries[] = array("code"=>"GF","name"=>"French Guiana","d_code"=>"+594");
		$countries[] = array("code"=>"PF","name"=>"French Polynesia","d_code"=>"+689");
		$countries[] = array("code"=>"GA","name"=>"Gabon","d_code"=>"+241");
		$countries[] = array("code"=>"GE","name"=>"Georgia","d_code"=>"+995");
		$countries[] = array("code"=>"DE","name"=>"Germany","d_code"=>"+49");
		$countries[] = array("code"=>"GH","name"=>"Ghana","d_code"=>"+233");
		$countries[] = array("code"=>"GI","name"=>"Gibraltar","d_code"=>"+350");
		$countries[] = array("code"=>"GR","name"=>"Greece","d_code"=>"+30");
		$countries[] = array("code"=>"GL","name"=>"Greenland","d_code"=>"+299");
		$countries[] = array("code"=>"GD","name"=>"Grenada","d_code"=>"+1");
		$countries[] = array("code"=>"GP","name"=>"Guadeloupe","d_code"=>"+590");
		$countries[] = array("code"=>"GU","name"=>"Guam","d_code"=>"+1");
		$countries[] = array("code"=>"GT","name"=>"Guatemala","d_code"=>"+502");
		$countries[] = array("code"=>"GN","name"=>"Guinea","d_code"=>"+224");
		$countries[] = array("code"=>"GW","name"=>"Guinea-Bissau","d_code"=>"+245");
		$countries[] = array("code"=>"GY","name"=>"Guyana","d_code"=>"+592");
		$countries[] = array("code"=>"HT","name"=>"Haiti","d_code"=>"+509");
		$countries[] = array("code"=>"HN","name"=>"Honduras","d_code"=>"+504");
		$countries[] = array("code"=>"HK","name"=>"Hong Kong","d_code"=>"+852");
		$countries[] = array("code"=>"HU","name"=>"Hungary","d_code"=>"+36");
		$countries[] = array("code"=>"IS","name"=>"Iceland","d_code"=>"+354");
		$countries[] = array("code"=>"IN","name"=>"India","d_code"=>"+91");
		$countries[] = array("code"=>"ID","name"=>"Indonesia","d_code"=>"+62");
		$countries[] = array("code"=>"IR","name"=>"Iran","d_code"=>"+98");
		$countries[] = array("code"=>"IQ","name"=>"Iraq","d_code"=>"+964");
		$countries[] = array("code"=>"IE","name"=>"Ireland","d_code"=>"+353");
		$countries[] = array("code"=>"IL","name"=>"Israel","d_code"=>"+972");
		$countries[] = array("code"=>"IT","name"=>"Italy","d_code"=>"+39");
		$countries[] = array("code"=>"JM","name"=>"Jamaica","d_code"=>"+1");
		$countries[] = array("code"=>"JP","name"=>"Japan","d_code"=>"+81");
		$countries[] = array("code"=>"JO","name"=>"Jordan","d_code"=>"+962");
		$countries[] = array("code"=>"KZ","name"=>"Kazakhstan","d_code"=>"+7");
		$countries[] = array("code"=>"KE","name"=>"Kenya","d_code"=>"+254");
		$countries[] = array("code"=>"KI","name"=>"Kiribati","d_code"=>"+686");
		//$countries[] = array("code"=>"XK","name"=>"Kosovo","d_code"=>"+381");
		$countries[] = array("code"=>"KW","name"=>"Kuwait","d_code"=>"+965");
		$countries[] = array("code"=>"KG","name"=>"Kyrgyzstan","d_code"=>"+996");
		$countries[] = array("code"=>"LA","name"=>"Laos","d_code"=>"+856");
		$countries[] = array("code"=>"LV","name"=>"Latvia","d_code"=>"+371");
		$countries[] = array("code"=>"LB","name"=>"Lebanon","d_code"=>"+961");
		$countries[] = array("code"=>"LS","name"=>"Lesotho","d_code"=>"+266");
		$countries[] = array("code"=>"LR","name"=>"Liberia","d_code"=>"+231");
		$countries[] = array("code"=>"LY","name"=>"Libya","d_code"=>"+218");
		$countries[] = array("code"=>"LI","name"=>"Liechtenstein","d_code"=>"+423");
		$countries[] = array("code"=>"LT","name"=>"Lithuania","d_code"=>"+370");
		$countries[] = array("code"=>"LU","name"=>"Luxembourg","d_code"=>"+352");
		$countries[] = array("code"=>"MO","name"=>"Macau","d_code"=>"+853");
		$countries[] = array("code"=>"MK","name"=>"Macedonia","d_code"=>"+389");
		$countries[] = array("code"=>"MG","name"=>"Madagascar","d_code"=>"+261");
		$countries[] = array("code"=>"MW","name"=>"Malawi","d_code"=>"+265");
		$countries[] = array("code"=>"MY","name"=>"Malaysia","d_code"=>"+60");
		$countries[] = array("code"=>"MV","name"=>"Maldives","d_code"=>"+960");
		$countries[] = array("code"=>"ML","name"=>"Mali","d_code"=>"+223");
		$countries[] = array("code"=>"MT","name"=>"Malta","d_code"=>"+356");
		$countries[] = array("code"=>"MH","name"=>"Marshall Islands","d_code"=>"+692");
		$countries[] = array("code"=>"MQ","name"=>"Martinique","d_code"=>"+596");
		$countries[] = array("code"=>"MR","name"=>"Mauritania","d_code"=>"+222");
		$countries[] = array("code"=>"MU","name"=>"Mauritius","d_code"=>"+230");
		$countries[] = array("code"=>"YT","name"=>"Mayotte","d_code"=>"+262");
		$countries[] = array("code"=>"MX","name"=>"Mexico","d_code"=>"+52");
		$countries[] = array("code"=>"MD","name"=>"Moldova","d_code"=>"+373");
		$countries[] = array("code"=>"MC","name"=>"Monaco","d_code"=>"+377");
		$countries[] = array("code"=>"MN","name"=>"Mongolia","d_code"=>"+976");
		$countries[] = array("code"=>"ME","name"=>"Montenegro","d_code"=>"+382");
		$countries[] = array("code"=>"MS","name"=>"Montserrat","d_code"=>"+1");
		$countries[] = array("code"=>"MA","name"=>"Morocco","d_code"=>"+212");
		$countries[] = array("code"=>"MZ","name"=>"Mozambique","d_code"=>"+258");
		$countries[] = array("code"=>"NA","name"=>"Namibia","d_code"=>"+264");
		$countries[] = array("code"=>"NR","name"=>"Nauru","d_code"=>"+674");
		$countries[] = array("code"=>"NP","name"=>"Nepal","d_code"=>"+977");
		$countries[] = array("code"=>"NL","name"=>"Netherlands","d_code"=>"+31");
		$countries[] = array("code"=>"AN","name"=>"Netherlands Antilles","d_code"=>"+599");
		$countries[] = array("code"=>"NC","name"=>"New Caledonia","d_code"=>"+687");
		$countries[] = array("code"=>"NZ","name"=>"New Zealand","d_code"=>"+64");
		$countries[] = array("code"=>"NI","name"=>"Nicaragua","d_code"=>"+505");
		$countries[] = array("code"=>"NE","name"=>"Niger","d_code"=>"+227");
		$countries[] = array("code"=>"NG","name"=>"Nigeria","d_code"=>"+234");
		$countries[] = array("code"=>"NU","name"=>"Niue","d_code"=>"+683");
		$countries[] = array("code"=>"NF","name"=>"Norfolk Island","d_code"=>"+672");
		$countries[] = array("code"=>"KP","name"=>"North Korea","d_code"=>"+850");
		$countries[] = array("code"=>"MP","name"=>"Northern Mariana Islands","d_code"=>"+1");
		$countries[] = array("code"=>"NO","name"=>"Norway","d_code"=>"+47");
		$countries[] = array("code"=>"OM","name"=>"Oman","d_code"=>"+968");
		$countries[] = array("code"=>"PK","name"=>"Pakistan","d_code"=>"+92");
		$countries[] = array("code"=>"PW","name"=>"Palau","d_code"=>"+680");
		$countries[] = array("code"=>"PS","name"=>"Palestine","d_code"=>"+970");
		$countries[] = array("code"=>"PA","name"=>"Panama","d_code"=>"+507");
		$countries[] = array("code"=>"PG","name"=>"Papua New Guinea","d_code"=>"+675");
		$countries[] = array("code"=>"PY","name"=>"Paraguay","d_code"=>"+595");
		$countries[] = array("code"=>"PE","name"=>"Peru","d_code"=>"+51");
		$countries[] = array("code"=>"PH","name"=>"Philippines","d_code"=>"+63");
		$countries[] = array("code"=>"PL","name"=>"Poland","d_code"=>"+48");
		$countries[] = array("code"=>"PT","name"=>"Portugal","d_code"=>"+351");
		$countries[] = array("code"=>"PR","name"=>"Puerto Rico","d_code"=>"+1");
		$countries[] = array("code"=>"QA","name"=>"Qatar","d_code"=>"+974");
		$countries[] = array("code"=>"CG","name"=>"Republic of the Congo","d_code"=>"+242");
		$countries[] = array("code"=>"RE","name"=>"Réunion" ,"d_code"=>"+262");
		$countries[] = array("code"=>"RO","name"=>"Romania","d_code"=>"+40");
		$countries[] = array("code"=>"RU","name"=>"Russia","d_code"=>"+7");
		$countries[] = array("code"=>"RW","name"=>"Rwanda","d_code"=>"+250");
		//$countries[] = array("code"=>"BL","name"=>"Saint Barthélemy" ,"d_code"=>"+590");
		$countries[] = array("code"=>"SH","name"=>"Saint Helena","d_code"=>"+290");
		$countries[] = array("code"=>"KN","name"=>"Saint Kitts and Nevis","d_code"=>"+1");
		//$countries[] = array("code"=>"MF","name"=>"Saint Martin","d_code"=>"+590");
		$countries[] = array("code"=>"PM","name"=>"Saint Pierre and Miquelon","d_code"=>"+508");
		$countries[] = array("code"=>"VC","name"=>"Saint Vincent and the Grenadines","d_code"=>"+1");
		$countries[] = array("code"=>"WS","name"=>"Samoa","d_code"=>"+685");
		$countries[] = array("code"=>"SM","name"=>"San Marino","d_code"=>"+378");
		$countries[] = array("code"=>"ST","name"=>"São Tomé and Príncipe" ,"d_code"=>"+239");
		$countries[] = array("code"=>"SA","name"=>"Saudi Arabia","d_code"=>"+966");
		$countries[] = array("code"=>"SN","name"=>"Senegal","d_code"=>"+221");
		$countries[] = array("code"=>"RS","name"=>"Serbia","d_code"=>"+381");
		$countries[] = array("code"=>"SC","name"=>"Seychelles","d_code"=>"+248");
		$countries[] = array("code"=>"SL","name"=>"Sierra Leone","d_code"=>"+232");
		$countries[] = array("code"=>"SG","name"=>"Singapore","d_code"=>"+65");
		$countries[] = array("code"=>"SK","name"=>"Slovakia","d_code"=>"+421");
		$countries[] = array("code"=>"SI","name"=>"Slovenia","d_code"=>"+386");
		$countries[] = array("code"=>"SB","name"=>"Solomon Islands","d_code"=>"+677");
		$countries[] = array("code"=>"SO","name"=>"Somalia","d_code"=>"+252");
		$countries[] = array("code"=>"ZA","name"=>"South Africa","d_code"=>"+27");
		$countries[] = array("code"=>"KR","name"=>"South Korea","d_code"=>"+82");
		$countries[] = array("code"=>"ES","name"=>"Spain","d_code"=>"+34");
		$countries[] = array("code"=>"LK","name"=>"Sri Lanka","d_code"=>"+94");
		$countries[] = array("code"=>"LC","name"=>"St. Lucia","d_code"=>"+1");
		$countries[] = array("code"=>"SD","name"=>"Sudan","d_code"=>"+249");
		$countries[] = array("code"=>"SR","name"=>"Suriname","d_code"=>"+597");
		$countries[] = array("code"=>"SZ","name"=>"Swaziland","d_code"=>"+268");
		$countries[] = array("code"=>"SE","name"=>"Sweden","d_code"=>"+46");
		$countries[] = array("code"=>"CH","name"=>"Switzerland","d_code"=>"+41");
		$countries[] = array("code"=>"SY","name"=>"Syria","d_code"=>"+963");
		$countries[] = array("code"=>"TW","name"=>"Taiwan","d_code"=>"+886");
		$countries[] = array("code"=>"TJ","name"=>"Tajikistan","d_code"=>"+992");
		$countries[] = array("code"=>"TZ","name"=>"Tanzania","d_code"=>"+255");
		$countries[] = array("code"=>"TH","name"=>"Thailand","d_code"=>"+66");
		$countries[] = array("code"=>"BS","name"=>"The Bahamas","d_code"=>"+1");
		$countries[] = array("code"=>"GM","name"=>"The Gambia","d_code"=>"+220");
		$countries[] = array("code"=>"TL","name"=>"Timor-Leste","d_code"=>"+670");
		$countries[] = array("code"=>"TG","name"=>"Togo","d_code"=>"+228");
		$countries[] = array("code"=>"TK","name"=>"Tokelau","d_code"=>"+690");
		$countries[] = array("code"=>"TO","name"=>"Tonga","d_code"=>"+676");
		$countries[] = array("code"=>"TT","name"=>"Trinidad and Tobago","d_code"=>"+1");
		$countries[] = array("code"=>"TN","name"=>"Tunisia","d_code"=>"+216");
		$countries[] = array("code"=>"TR","name"=>"Turkey","d_code"=>"+90");
		$countries[] = array("code"=>"TM","name"=>"Turkmenistan","d_code"=>"+993");
		$countries[] = array("code"=>"TC","name"=>"Turks and Caicos Islands","d_code"=>"+1");
		$countries[] = array("code"=>"TV","name"=>"Tuvalu","d_code"=>"+688");
		$countries[] = array("code"=>"UG","name"=>"Uganda","d_code"=>"+256");
		$countries[] = array("code"=>"UA","name"=>"Ukraine","d_code"=>"+380");
		$countries[] = array("code"=>"AE","name"=>"United Arab Emirates","d_code"=>"+971");
		$countries[] = array("code"=>"GB","name"=>"United Kingdom","d_code"=>"+44");
		$countries[] = array("code"=>"US","name"=>"United States","d_code"=>"+1");
		$countries[] = array("code"=>"UY","name"=>"Uruguay","d_code"=>"+598");
		$countries[] = array("code"=>"VI","name"=>"US Virgin Islands","d_code"=>"+1");
		$countries[] = array("code"=>"UZ","name"=>"Uzbekistan","d_code"=>"+998");
		$countries[] = array("code"=>"VU","name"=>"Vanuatu","d_code"=>"+678");
		$countries[] = array("code"=>"VA","name"=>"Vatican City","d_code"=>"+39");
		$countries[] = array("code"=>"VE","name"=>"Venezuela","d_code"=>"+58");
		$countries[] = array("code"=>"VN","name"=>"Vietnam","d_code"=>"+84");
		$countries[] = array("code"=>"WF","name"=>"Wallis and Futuna","d_code"=>"+681");
		$countries[] = array("code"=>"YE","name"=>"Yemen","d_code"=>"+967");
		$countries[] = array("code"=>"ZM","name"=>"Zambia","d_code"=>"+260");
		$countries[] = array("code"=>"ZW","name"=>"Zimbabwe","d_code"=>"+263");
		
		return $countries;
	}

	
	public function send() {
		$json = array();
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if (!$this->user->hasPermission('modify', $this->module_path)) {
				$json['error']['warning'] = 'You do not have permission to perform this action!';
			}
			if (!$this->request->post['message']) {
				$json['error']['message'] = 'The message field should not be empty!';
			}
			if (!$json) {
				
				$store_info = $this->model_setting_store->getStore($this->request->post['store_id']);			
				if ($store_info) {
					$store_name = $store_info['name'];
				} else {
					$store_name = $this->config->get('config_name');
				}
				
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
				} else {
					$page = 1;
				}

				$telephones_total = 0;
				$json['telephones'] = array();
				
				switch ($this->request->post['to']) {
					case 'telephones':
						$phones = isset($this->request->post['phones']) ? $this->request->post['phones'] : array();
						foreach ($phones as $result) {							
							$json['telephones'][] = $result;							
						}
						break;
					case 'newsletter':
						$customer_data = array(
							'filter_newsletter' => 1,
							'start'             => ($page - 1) * 10
						);
						$telephones_total = $this->smsbump_model->getTotalCustomers($customer_data);
						$results = $this->smsbump_model->getCustomers($customer_data);
						foreach ($results as $result) {
							$validPhone = $this->smsbump_model->sendCheck($result['telephone']);
							if ($validPhone){
								$json['telephones'][] = $validPhone;
							}
						}
						break;
					case 'customer_all':
						$customer_data = array(
							'start'  => ($page - 1) * 10
						);
						$telephones_total = $this->smsbump_model->getTotalCustomers($customer_data);
						$results = $this->smsbump_model->getCustomers($customer_data);
						foreach ($results as $result) {
							$validPhone = $this->smsbump_model->sendCheck($result['telephone']);
							if ($validPhone){
								$json['telephones'][] = $validPhone;
							}

						}						
						break;
					case 'customer_group':
						$customer_data = array(
							'filter_customer_group_id' => $this->request->post['customer_group_id'],
							'start'                    => ($page - 1) * 10
						);
						$telephones_total = $this->smsbump_model->getTotalCustomers($customer_data);
						$results = $this->smsbump_model->getCustomers($customer_data);
						foreach ($results as $result) {
							$validPhone = $this->smsbump_model->sendCheck($result['telephone']);
							if ($validPhone){
								$json['telephones'][] = $validPhone;
							}

						}						
						break;
					case 'customer':
						if (!empty($this->request->post['customer'])) {					
							foreach ($this->request->post['customer'] as $customer_id) {
								$customer_info = $this->smsbump_model->getCustomer($customer_id);
								if ($customer_info) {
									$validPhone = $this->smsbump_model->sendCheck($customer_info['telephone']);
									if ($validPhone){
										$json['telephones'][] = $validPhone;
									}
									
								}
							}
						}
						break;	
					case 'affiliate_all':
						$affiliate_data = array(
							'start'  => ($page - 1) * 10
						);
						$telephones_total = $this->smsbump_model->getTotalAffiliates($affiliate_data);		
						$results = $this->smsbump_model->getAffiliates($affiliate_data);
						foreach ($results as $result) {
							$validPhone = $this->smsbump_model->sendCheck($result['telephone']);
							if ($validPhone){
								$json['telephones'][] = $validPhone;
							}

						}						
						break;	
					case 'affiliate':
						if (!empty($this->request->post['affiliate'])) {					
							foreach ($this->request->post['affiliate'] as $affiliate_id) {
								$affiliate_info = $this->smsbump_model->getAffiliate($affiliate_id);
								if ($affiliate_info) {
									$validPhone = $this->smsbump_model->sendCheck($affiliate_info['telephone']);
									if ($validPhone){
										$json['telephones'][] = $validPhone;
									}
								}
							}
						}
						break;											
					case 'product':
						if (isset($this->request->post['product'])) {
							$telephones_total = $this->smsbump_model->getTotalTelephonesByProductsOrdered($this->request->post['product']);	
							$results = $this->smsbump_model->getTelephonesByProductsOrdered($this->request->post['product'], ($page - 1) * 10, 10);
							foreach ($results as $result) {
								$validPhone = $this->smsbump_model->sendCheck($result['telephone']);
								if ($validPhone){
									$json['telephones'][] = $validPhone;
								}
							}
						}
						break;												
				}
				
				$json['telephonesTotal'] = $telephones_total;
				
				if ($json['telephones']) {
						$json['success'] = $this->language->get('text_success');
				}
			}
		}
		$this->response->setOutput(json_encode($json));	
	}
}
?>