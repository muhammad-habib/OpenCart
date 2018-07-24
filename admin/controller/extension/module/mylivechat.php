<?php
class ControllerExtensionModuleMyLiveChat extends Controller {
	private $error = array ();
	public function index() {
		$this->load->language('extension/module/mylivechat');
		$this->document->setTitle ( $this->language->get ( 'heading_title' ) );
		
		$this->load->model ( 'setting/setting' );
		
		if (($this->request->server ['REQUEST_METHOD'] == 'POST') && $this->validate ()) {
			$this->model_setting_setting->editSetting ( 'mylivechat', $this->request->post );
			
			$this->session->data ['success'] = $this->language->get ( 'text_success' );
			
			$this->response->redirect ( $this->url->link ( 'extension/extension', 'token=' . $this->session->data ['token'], 'SSL' ) );
		}
		
		$data ['heading_title'] = $this->language->get ( 'heading_title' );
		$data ['entry_code'] = $this->language->get('entry_code');
		$data ['entry_displaytype'] = $this->language->get('entry_displaytype');
		$data ['text_edit'] = $this->language->get ( 'text_edit' );
		$data ['text_enabled'] = $this->language->get ( 'text_enabled' );
		$data ['text_disabled'] = $this->language->get ( 'text_disabled' );
		
		$data ['entry_status'] = $this->language->get ( 'entry_status' );
		
		$data ['button_save'] = $this->language->get ( 'button_save' );
		$data ['button_cancel'] = $this->language->get ( 'button_cancel' );
		
		if (isset ( $this->error ['warning'] )) {
			$data ['error_warning'] = $this->error ['warning'];
		} else {
			$data ['error_warning'] = '';
		}
		
		$data ['breadcrumbs'] = array ();
		
		$data ['breadcrumbs'] [] = array (
				'text' => $this->language->get ( 'text_home' ),
				'href' => $this->url->link ( 'common/dashboard', 'token=' . $this->session->data ['token'], 'SSL' ) 
		);
		
		$data ['breadcrumbs'] [] = array (
				'text' => $this->language->get ( 'text_module' ),
				'href' => $this->url->link ( 'extension/module', 'token=' . $this->session->data ['token'], 'SSL' ) 
		);
		
		$data ['breadcrumbs'] [] = array (
				'text' => $this->language->get ( 'heading_title' ),
				'href' => $this->url->link ( 'extension/module/mylivechat', 'token=' . $this->session->data ['token'], 'SSL' ) 
		);
		
		$data ['action'] = $this->url->link ( 'extension/module/mylivechat', 'token=' . $this->session->data ['token'], 'SSL' );
		
		$data ['cancel'] = $this->url->link ( 'extension/module', 'token=' . $this->session->data ['token'], 'SSL' );
		
		if (isset ( $this->request->post ['mylivechat_status'] )) {
			$data ['mylivechat_status'] = $this->request->post ['mylivechat_status'];
		} else {
			$data ['mylivechat_status'] = $this->config->get ( 'mylivechat_status' );
		}
		if (isset ( $this->request->post ['mylivechat_code'] )) {
			$data ['mylivechat_code'] = $this->request->post ['mylivechat_code'];
		} else {
			$data ['mylivechat_code'] = $this->config->get ( 'mylivechat_code' );
		}
		if (isset ( $this->request->post ['mylivechat_displaytype'] )) {
			$data ['mylivechat_displaytype'] = $this->request->post ['mylivechat_displaytype'];
		} else {
			$data ['mylivechat_displaytype'] = $this->config->get ( 'mylivechat_displaytype' );
		}
		
		$data ['header'] = $this->load->controller ( 'common/header' );
		$data ['column_left'] = $this->load->controller ( 'common/column_left' );
		$data ['footer'] = $this->load->controller ( 'common/footer' );
		
		$this->response->setOutput ( $this->load->view ( 'extension/module/mylivechat.tpl', $data ) );
	}
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/mylivechat' )) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
	


	
}