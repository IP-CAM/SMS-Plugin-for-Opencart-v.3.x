<?php
class ControllerExtensionModuleLogonsms extends Controller
{
	private $error = array();
	public function index()
	{
		$this->load->language('extension/module/logonsms');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/module');
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logonsms");
		error_log(json_encode($query->rows));
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logonsms");
		$params = array();
		foreach($query->rows as $row) {
			$params[$row['keyy']] = $row['val'];
		}
		$data['apikey'] = $params['apikey'];
		$data['campaign'] = $params['campaign'];
		$data['route'] = $params['route'];
		$data['contacts'] = $params['contacts'];
		$data['sender'] = $params['sender'];
		$data['message'] = $params['message'];
		$data['template'] = $params['template'];
		$data['entity'] = $params['entity'];

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$key = $this->request->post['apikey'];
			$campaign = $this->request->post['campaign'];
			$routeid = $this->request->post['route'];
			$contacts = $this->request->post['contacts'];
			$senderid = $this->request->post['sender'];
			$msg = $this->request->post['message'];
			$template = $this->request->post['template'];
			$entity = $this->request->post['entity'];
			
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$key' WHERE keyy='apikey'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$campaign' WHERE keyy='campaign'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$routeid' WHERE keyy='route'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$contacts' WHERE keyy='contacts'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$senderid' WHERE keyy='sender'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$msg' WHERE keyy='message'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$template' WHERE keyy='template'");
			$this->db->query("UPDATE " . DB_PREFIX . "logonsms SET val='$entity' WHERE keyy='entity'");
			
			
			/*if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('logonsms', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}*/
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_home'),'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true));
		$data['breadcrumbs'][] = array('text' => $this->language->get('text_extension'),'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'),'href' => $this->url->link('extension/module/logonsms', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'),'href' => $this->url->link('extension/module/logonsms', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
		}
	if (!isset($this->request->get['module_id'])) {
		$data['action'] = $this->url->link('extension/module/logonsms', 'user_token=' . $this->session->data['user_token'], true);
	} else {
		$data['action'] = $this->url->link('extension/module/logonsms', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
	}
	$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
	if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
		$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
	}
	if (isset($this->request->post['name'])) {
		$data['name'] = $this->request->post['name'];
	} elseif (!empty($module_info)) {
		$data['name'] = $module_info['name'];
	} else {
		$data['name'] = '';
	}
	$this->load->model('localisation/language');
	$data['languages'] = $this->model_localisation_language->getLanguages();
	if (isset($this->request->post['status'])) {
		$data['status'] = $this->request->post['status'];
	} elseif (!empty($module_info)) {
		$data['status'] = $module_info['status'];
	} else {
		$data['status'] = '';
	}
	$data['header'] = $this->load->controller('common/header');
	$data['column_left'] = $this->load->controller('common/column_left');
	$data['footer'] = $this->load->controller('common/footer');
	$this->response->setOutput($this->load->view('extension/module/logonsms', $data));
	}
	protected function validate()
	{
		if (!$this->user->hasPermission('modify', 'extension/module/logonsms')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		/*if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}*/
		return !$this->error;
	}
	public function install()
	{
		$this->load->model('setting/setting');
		$this->load->model('setting/module');
		$this->model_setting_setting->editSetting('module_logonsms', ['module_logonsms_status' => 1]);
		$this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "logonsms (id INT AUTO_INCREMENT PRIMARY KEY, keyy VARCHAR(255) NOT NULL,val VARCHAR(1023))");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('apikey', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('campaign', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('route', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('contacts', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('sender', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('message', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('template', '')");
		$this->db->query("INSERT INTO " . DB_PREFIX . "logonsms (keyy, val) VALUES ('entity', '')");
		$this->load->model('setting/event');
		$this->model_setting_event->addEvent('logonsms', 'catalog/model/checkout/order/addOrderHistory/after', 'extension/module/logonsms/orderhook');
	}
	public function uninstall()
	{
		$this->load->model('setting/setting');
		$this->load->model('setting/module');
		$this->load->model('setting/event');
		$this->model_setting_event->deleteEventByCode('logonsms');
		$this->model_setting_setting->deleteSetting('module_logonsms');
		$this->db->query("DROP TABLE " . DB_PREFIX . "logonsms");
	}
	public function orderhook(&$route, &$data, &$output=null) {
		$this->load->model('checkout/order');
		$order = $this->model_checkout_order->getOrder($data[0]);
		error_log(json_encode($order));
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "logonsms");
		$params = array();
		foreach($query->rows as $row) {
			$params[$row['keyy']] = $row['val'];
		}
		$key = $params['apikey'];
		$campaign = $params['campaign'];
		$routeid = $params['route'];
		$contacts = $order['telephone'];
		$senderid = $params['sender'];
		$msg = urlencode($params['message']);
		$template = $params['template'];
		$entity = $params['entity'];
		$tlv = urlencode(json_encode(array("EntityID"=>$entity, "ContentID"=>$template)));
		$url = "https://module.logonutility.com/smsapi/index?key=$key&campaign=$campaign&routeid=$routeid&type=text&contacts=$contacts&senderid=$senderid&msg=$msg&tlv=$tlv";
		$c = curl_init($url);
		curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
		$page = curl_exec($c);
		curl_close($c);
		error_log("Entered orderhook");
		if($output!=null) return $output;
	}
}