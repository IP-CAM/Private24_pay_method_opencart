<?php

class ControllerPaymentPrivat24 extends Controller {

	protected function index() {
		
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$this->load->model('payment/privat24');

		$product_name = $this->model_payment_privat24->getProductName($this->session->data['order_id']);

		$this->data['action'] = 'https://api.privatbank.ua/p24api/ishop';

		$this->data['amount'] = $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false);
		$this->data['currency'] = $order_info['currency_code'];
		$this->data['merchant'] = $this->config->get('privat24_merchant');
		$this->data['order'] = $this->session->data['order_id'];
		$this->data['details'] = $product_name;
		$this->data['ext_details'] = $this->config->get('config_name') . '_' . $this->session->data['order_id'];
		$this->data['return_url'] = $this->url->link('payment/privat24/confirm/'.$this->session->data['order_id'], '', 'SSL');
		$this->data['server_url'] = $this->url->link('payment/privat24/callback', '', 'SSL');
		$this->data['ccy'] = $this->config->get('privat24_ccy');

		$this->session->data['payment'] = array(
													'amount' => $this->data['amount'],
													'merchant'=> $this->data['merchant'],
													'ccy' => $this->data['ccy'],
													'order' => $this->data['order']
												);

		$state = 'test';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/privat24.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/payment/privat24.tpl';
		} else {
			$this->template = 'default/template/payment/privat24.tpl';
		}

		$this->render();
	}

	private function parseRequest($query_string) {

		$payment_query = str_replace('&amp;', '&', $query_string);

		$payment_arr = explode('&', $payment_query);

		$payment_result_arr = array();

		foreach ($payment_arr as $payment) {
			$payment_tmp = explode('=', $payment);
			$payment_result_arr[$payment_tmp[0]] = $payment_tmp[1];
		}

		return $payment_result_arr;
	}

	public function getSignature() {
		$payment = str_replace('&amp;', '&', $this->request->post['payment']);
		return sha1(md5($payment.$this->config->get('privat24_password')));
	}

	public function callback() {
		if (isset($this->request->post['payment'])) {
			$signature_server = $this->getSignature();
			$signature_bank = $this->request->post['signature'];

			$server_payment = $this->session->data['payment'];
			$bank_payment = $this->parseRequest($this->request->post['payment']);

			$order_id = $bank_payment['order'];

			if ($signature_bank == $signature_server) {
				$this->load->model('payment/privat24');
				if (
						($server_payment['amount'] == $bank_payment['amount']) &&
						($server_payment['merchant'] == $bank_payment['merchant']) &&
						($server_payment['ccy'] == $bank_payment['ccy']) &&
						($server_payment['order'] == $bank_payment['order'])
					) {
							$status = '';
							if (strcasecmp($bank_payment['state'], 'test') == 0) {
								$status = 'test';
							} elseif (strcasecmp($bank_payment['state'], 'ok') == 0) {
								$status = 'ok';
							} elseif (strcasecmp($bank_payment['state'], 'fail') == 0) {
								$status = 'fail';
							}

							$this->model_payment_privat24->addPrivat24Status($bank_payment['order'], $status);
							if ( $bank_payment['state'] == 'ok' ) {

								$this->load->model('checkout/order');
								$this->model_checkout_order->confirm($order_id, $this->config->get('privat24_order_status_id'), 'Privat24');

							} elseif ($bank_payment['state'] == 'test') {
								
								$this->load->model('checkout/order');
								$this->model_checkout_order->confirm($order_id, $this->config->get('privat24_order_status_id'), 'Privat24_TEST');

							}
						}
				
					$this->model_payment_privat24->updatePrivat24Status($order_id, $bank_payment['state']);
					return TRUE;
				}
		}
	}
	
	public function confirm() {
		if (isset($this->request->post['payment'])) {
			$signature_server = $this->getSignature();
			$signature_bank = $this->request->post['signature'];

			$server_payment = $this->session->data['payment'];
			$bank_payment = $this->parseRequest($this->request->post['payment']);

			if ($signature_bank == $signature_server) {
				$this->load->model('payment/privat24');
				if (
						($server_payment['amount'] == $bank_payment['amt']) &&
						($server_payment['ccy'] == $bank_payment['ccy']) &&
						($server_payment['merchant'] == $bank_payment['merchant']) &&
						($server_payment['order'] == $bank_payment['order'])
					) {

							if (strcasecmp($bank_payment['state'], 'ok') == 0) {
								$this->redirect($this->url->link('checkout/success', '', 'SSL'));
							} elseif (strcasecmp($bank_payment['state'], 'wait_secure') == 0) {
								$this->redirect($this->url->link('checkout/success', '', 'SSL'));
							} elseif (strcasecmp($bank_payment['state'], 'test') == 0) {
								$this->redirect($this->url->link('checkout/success', '', 'SSL'));
							} else {
								$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
							}
						}
				}
			
		} else {
		$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}
	}

}

?>