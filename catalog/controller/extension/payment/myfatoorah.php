<?php

class ControllerExtensionPaymentMyfatoorah extends Controller
{
    /**
     * @var Log $log
     */
    private $log;
    private $isTest;
    private $keyindex;
    private $sid;
    private $wallet_number;
    private $private_key;
    private $public_key;
    private $version;
    private $action;
    private $logging;

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->log = new Log('myfatoorah.log');

        $this->isTest = $this->config->get('myfatoorah_test') === '1' ? true : false;
        $this->logging = $this->config->get('myfatoorah_logging') === '1' ? true : false;
        $this->version = '1.1';

        if (!$this->isTest)
        {
            $this->merchant_code           = $this->config->get('myfatoorah_merchant_code');
            $this->merchant_username       = $this->config->get('myfatoorah_merchant_username');
            $this->merchant_password       = $this->config->get('myfatoorah_merchant_password');
            $this->gateway_url             = $this->config->get('myfatoorah_gateway_url');
            $this->payment_type            = $this->config->get('myfatoorah_payment_type');
        }
        else
        {
            $this->merchant_code           = "999999";
            $this->merchant_username       = "testapi@myfatoorah.com";
            $this->merchant_password       = "E55D0";
            $this->gateway_url             = "https://test.myfatoorah.com/pg/PayGatewayServiceV2.asmx";
            $this->payment_type            = "BOTH";
        }
    }

	public function index() {
        $this->language->load('extension/payment/myfatoorah');
        $data['button_confirm'] = $this->language->get('button_confirm');
        $data['action']         = 'index.php?route=extension/payment/myfatoorah/confirm';
        $data['continue'] = $this->url->link('checkout/success');


        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/payment/myfatoorah.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/extension/payment/myfatoorah.tpl', $data);
        } else {
            return $this->load->view('extension/payment/myfatoorah.tpl', $data);
        }
	}

    public function confirm()
    {
        $t           = time();
        $url         = $this->gateway_url;
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);


        $products    = $this->cart->getProducts();
        if(isset($_SESSION['default']['shipping_method'])){
            unset($_SESSION['default']['shipping_method']);
        }
        
        $productdata = "";

        foreach ($products as $product) { 
            if(trim($product['name'])== '')
                continue;
            $productdata .= '<ProductDC>';
            $productdata .= '<product_name>' . $product['name'] . '</product_name>';
            $productdata .= '<unitPrice>' . $product['price']*$order_info['currency_value'] . '</unitPrice>';
            $productdata .= '<qty>' . $product['quantity'] . '</qty>';
            $productdata .= '</ProductDC>';
        }
        $total = $this->cart->gettotal();

        $shipping_methods = $this->session->data['shipping_methods'] ;
        if($shipping_methods){
            $shipping_name = array_keys($shipping_methods)[0];
            $shipping_cost = $shipping_methods[$shipping_name]['quote'][$shipping_name]['cost'];

            $productdata .= '<ProductDC>';
            $productdata .= '<product_name>' . 'Shiping Cost' . '</product_name>';
            $productdata .= '<unitPrice>' . $shipping_cost*$order_info['currency_value'] . '</unitPrice>';
            $productdata .= '<qty>' . 1 . '</qty>';
            $productdata .= '</ProductDC>';

            $total += $shipping_cost;
        }

		$total *= $order_info['currency_value'];
        $fname = $this->customer->getFirstName();
        $lname = $this->customer->getLastName();
        $name  = isset($fname, $lname) ? $fname . $lname : $this->session->data['guest']['lastname'] . $this->session->data['guest']['firstname'];

        $gemail = $this->customer->getEmail();
        $email  = isset($gemail) ? $gemail : $this->session->data['guest']['email']; //"harbourspace@gmail.com";

        $gtelephone = $this->customer->getTelephone();
        $telephone  = isset($gtelephone) ? $gtelephone : $this->session->data['guest']['telephone']; //"1234567890";

$EnNum = array('0','1','2','3','4','5','6','7','8','9');
$ArNum = array('٠','١','٢','٣','٤','٥','٦','٧','٨','٩');

$telephone = str_replace($ArNum, $EnNum, $telephone);


        $post_string = '<?xml version="1.0" encoding="windows-1256"?>
                        <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
                        <soap12:Body>
                        <PaymentRequest xmlns="http://tempuri.org/">
                          <req>
                            <CustomerDC>
                              <Name>' . $name . '</Name>
                              <Email>' . $email . '</Email>
                              <Mobile>' . $telephone . '</Mobile>
                            </CustomerDC>
                            <MerchantDC>
                              <merchant_code>' . $this->merchant_code . '</merchant_code>
                              <merchant_username>' . $this->merchant_username. '</merchant_username>
                              <merchant_password>' . $this->merchant_password . '</merchant_password>
                              <merchant_ReferenceID>' . $t . '</merchant_ReferenceID>
                              <ReturnURL>' . htmlentities($this->url->link('extension/payment/myfatoorah/callback')) . '</ReturnURL>
                              <merchant_error_url>' . htmlentities($this->url->link('checkout/failure', '', true)) . '</merchant_error_url>
                            </MerchantDC>
                            <lstProductDC>' . $productdata . '</lstProductDC>
                            <totalDC>
						          <subtotal>'.$total.'</subtotal>
						        </totalDC>
								<paymentModeDC>
								  <paymentMode>'.$this->payment_type.'</paymentMode>
                            </paymentModeDC>
                            <paymentCurrencyDC>
                              <paymentCurrrency>'.$order_info['currency_code'].'</paymentCurrrency>
                            </paymentCurrencyDC>
                          </req>
                        </PaymentRequest>
                      </soap12:Body>
                    </soap12:Envelope>';
					
					// die($post_string );
					

        $soap_do     = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($soap_do, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($post_string)
        ));

        curl_setopt($soap_do, CURLOPT_USERPWD, $this->merchant_username. ":" . $this->merchant_password);
       

        $result = curl_exec($soap_do);
        $err    = curl_error($soap_do);
        $file_contents = htmlspecialchars(curl_exec($soap_do));
        curl_close($soap_do);
        $doc = new DOMDocument();

        if ($doc != null) {
            $doc->loadXML(html_entity_decode($file_contents));

            $ResponseCode = $doc->getElementsByTagName("ResponseCode");
            $ResponseCode = $ResponseCode->item(0)->nodeValue;

            $paymentUrl = $doc->getElementsByTagName("paymentURL");
            $paymentUrl = $paymentUrl->item(0)->nodeValue;

            $referenceID = $doc->getElementsByTagName("referenceID");
            $referenceID = $referenceID->item(0)->nodeValue;

            $ResponseMessage = $doc->getElementsByTagName("ResponseMessage");
            $ResponseMessage = $ResponseMessage->item(0)->nodeValue;
        } else {
            echo "Error connecting server.....";
            die;
        }


        if ($ResponseCode == 0) {
            $this->response->redirect($paymentUrl);
        } else {
            $this->response->redirect('index.php?route=checkout/failure');
        }
    }

    public function callback()
    {
        $responseID = $_REQUEST['id'];

        $t           = time();
        $url         = $this->gateway_url;
        $post_string = '<?xml version="1.0" encoding="utf-8"?>
                        <soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
                        <soap:Body>
                        <GetOrderStatusRequest xmlns="http://tempuri.org/">
                          <getOrderStatusRequestDC>
                            <merchant_code>' . $this->merchant_code . '</merchant_code>
                            <merchant_username>' . $this->merchant_username . '</merchant_username>
                            <merchant_password>' . $this->merchant_password . '</merchant_password>
                            <referenceID>' . $responseID  . '</referenceID>
                          </getOrderStatusRequestDC>
                        </GetOrderStatusRequest>
                      </soap:Body>
                    </soap:Envelope>';


        $soap_do     = curl_init();
        curl_setopt($soap_do, CURLOPT_URL, $url);
        curl_setopt($soap_do, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_TIMEOUT, 10);
        curl_setopt($soap_do, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($soap_do, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($soap_do, CURLOPT_POST, true);
        curl_setopt($soap_do, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($soap_do, CURLOPT_HTTPHEADER, array(
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($post_string)
        ));
        curl_setopt($soap_do, CURLOPT_USERPWD, $this->merchant_username . ":" . $this->merchant_password);
        
        $result = curl_exec($soap_do);
        $err    = curl_error($soap_do);

        $file_contents = htmlspecialchars(curl_exec($soap_do));
        curl_close($soap_do);

        $doc = new DOMDocument();
        $doc->loadXML(html_entity_decode($file_contents));
        $ResponseCode = $doc->getElementsByTagName("ResponseCode");
        $ResponseCode = $ResponseCode->item(0)->nodeValue;

        $ResponseMessage = $doc->getElementsByTagName("ResponseMessage");
        $ResponseMessage = $ResponseMessage->item(0)->nodeValue;

        if ($ResponseCode == 0) {
            $Paymode = $doc->getElementsByTagName("Paymode");
            $Paymode = $Paymode->item(0)->nodeValue;

            $PayTxnID = $doc->getElementsByTagName("PayTxnID");
            $PayTxnID = $PayTxnID->item(0)->nodeValue;

        }

        if ($ResponseCode == 0) {
            $this->language->load('checkout/success');
            $this->load->model('checkout/order');

            $data['text_title']      = $this->language->get('heading_title');
            $data['text_success']    = $this->language->get('text_success');
            $data['resp_code']       = $ResponseCode;
            $data['resp_msg']        = $ResponseMessage;
            $data['resp_pay_mode']   = $Paymode;
            $data['resp_pay_txn_id'] = $PayTxnID;

            $msg = $data['resp_msg'] . "<br /> Your ransaction ID is " . $data['resp_pay_txn_id'];

            $this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('myfatoorah_order_status_id'));

            $this->response->redirect('index.php?route=checkout/success');
        } else {
            $this->language->load('checkout/failure');
            $data['text_failure'] = $this->language->get('text_failure');
            $data['resp_code']    = $ResponseCode;
            $data['resp_msg']     = $ResponseMessage;
            $data['continue']     = $this->url->link('checkout/cart');

            $this->response->redirect('index.php?route=checkout/failure');
        }
    }






    private function log($message) {
        if ($this->logging)
        {
            $this->log->write($message);
        }
    }
}