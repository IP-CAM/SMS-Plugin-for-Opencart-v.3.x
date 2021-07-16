<?php
class ControllerExtensionModuleLogonsms extends Controller
{
	public function orderhook(&$route, &$data, &$output) {
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
