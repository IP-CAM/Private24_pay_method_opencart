<?php

class ModelPaymentPrivat24 extends Model {

	public function getMethod($address, $total) {
		$this->load->language('payment/privat24');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int) $this->config->get('privat24_geo_zone_id') . "' AND country_id = '" . (int) $address['country_id'] . "' AND (zone_id = '" . (int) $address['zone_id'] . "' OR zone_id = '0')");

		if ($this->config->get('privat24_total') > $total) {
			$status = false;
		} elseif (!$this->config->get('privat24_geo_zone_id')) {
			$status = true;
		} elseif ($query->num_rows) {
			$status = true;
		} else {
			$status = false;
		}

		$method_data = array();

		if ($status) {
			$method_data = array(
				'code' => 'privat24',
				'title' => $this->language->get('text_title'),
				'sort_order' => $this->config->get('privat24_sort_order')
			);
		}

		return $method_data;
	}

	public function getProductName($order_id) {
		$query = $this->db->query("SELECT name FROM " . DB_PREFIX . "order_product WHERE order_id = '" . intval($order_id) . "'");
		return $query->row['name'];
	}

	public function addPrivat24Status($order_id, $status_privat24) {
		$status = '';
		if (empty($status_privat24)) {
			if (strcasecmp($status_privat24, 'test') == 0) {
				$status = 'test';
			} elseif (strcasecmp($status_privat24, 'ok') == 0) {
				$status = 'ok';
			} elseif (strcasecmp($status_privat24, 'fail') == 0) {
				$status = 'fail';
			}
			$this->session->data['privat24'][] = array(
				'order_id' => $order_id,
				'status_privat24' => $status
			);
		}
	}

	public function updatePrivat24Status($order_id, $status_privat24) {
		if (strcasecmp($status_privat24, 'ok') == 0) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '5', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");
		}
		else if (strcasecmp($status_privat24, 'fail') == 0) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '7', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");
		} else if (strcasecmp($status_privat24, 'test') == 0) {
			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '15', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");
		}
	}

	public function getPrivat24Status($order_id) {
		if (isset($this->session->data['payment'])) {
			foreach ($this->session->data['payment'] as $value) {
				if ($value['order_id'] == $order_id) {
					return $value['status_privat24'];
				}
			}
		}

		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '2', date_modified = NOW() WHERE order_id = '" . (int) $order_id . "'");
		return 'wait_secure';
	}

}

?>