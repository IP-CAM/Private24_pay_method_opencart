<?php 
class ControllerPaymentPrivat24 extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('payment/privat24');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('privat24', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_pay'] = $this->language->get('text_pay');
		$this->data['text_card'] = $this->language->get('text_card');
		
		$this->data['entry_merchant'] = $this->language->get('entry_merchant');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_type'] = $this->language->get('entry_type');				
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['select_ccy'] = $this->language->get('select_ccy');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['merchant'])) { 
			$this->data['error_merchant'] = $this->error['merchant'];
		} else {
			$this->data['error_merchant'] = '';
		}
		
		if (isset($this->error['password'])) { 
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
		if (isset($this->error['type'])) { 
			$this->data['error_type'] = $this->error['type'];
		} else {
			$this->data['error_type'] = '';
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/privat24', 'token=' . $this->session->data['token'], 'SSL'),      		
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/privat24', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['privat24_merchant'])) {
			$this->data['privat24_merchant'] = $this->request->post['privat24_merchant'];
		} else {
			$this->data['privat24_merchant'] = $this->config->get('privat24_merchant');
		}

		if (isset($this->request->post['privat24_password'])) {
			$this->data['privat24_password'] = $this->request->post['privat24_password'];
		} else {
			$this->data['privat24_password'] = $this->config->get('privat24_password');
		}

		if (isset($this->request->post['privat24_type'])) {
			$this->data['privat24_type'] = $this->request->post['privat24_type'];
		} else {
			$this->data['privat24_type'] = $this->config->get('privat24_type');
		}
		
		if (isset($this->request->post['privat24_total'])) {
			$this->data['privat24_total'] = $this->request->post['privat24_total'];
		} else {
			$this->data['privat24_total'] = $this->config->get('privat24_total'); 
		} 
				
		if (isset($this->request->post['privat24_order_status_id'])) {
			$this->data['privat24_order_status_id'] = $this->request->post['privat24_order_status_id'];
		} else {
			$this->data['privat24_order_status_id'] = $this->config->get('privat24_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['privat24_geo_zone_id'])) {
			$this->data['privat24_geo_zone_id'] = $this->request->post['privat24_geo_zone_id'];
		} else {
			$this->data['privat24_geo_zone_id'] = $this->config->get('privat24_geo_zone_id'); 
		} 		
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['privat24_status'])) {
			$this->data['privat24_status'] = $this->request->post['privat24_status'];
		} else {
			$this->data['privat24_status'] = $this->config->get('privat24_status');
		}
		
		if (isset($this->request->post['privat24_sort_order'])) {
			$this->data['privat24_sort_order'] = $this->request->post['privat24_sort_order'];
		} else {
			$this->data['privat24_sort_order'] = $this->config->get('privat24_sort_order');
		}

		if (isset($this->request->post['privat24_ccy'])) {
			$this->data['privat24_ccy'] = $this->request->post['privat24_ccy'];
		} else {
			$this->data['privat24_ccy'] = $this->config->get('privat24_ccy');
		}

		$this->template = 'payment/privat24.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/privat24')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['privat24_merchant']) {
			$this->error['merchant'] = $this->language->get('error_merchant');
		}

		if (!$this->request->post['privat24_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>