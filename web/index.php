<?php

function get_request_headers() {
	static $headers = array();

	if (!empty($headers)) {
		return $headers;
	}
	if (!function_exists('getallheaders')) {
		foreach ($_SERVER as $key => $value) {
			if (substr($key, 0, 5) == 'HTTP_') {
				$key = substr($key, 5);
			} elseif (array_key_exists($key, $headers)) {
				continue;
			}
			$key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
			$headers[$key] = $value;
		}
	} else {
		foreach (getallheaders() as $key => $value) {
			$key = str_replace(' ', '-', ucwords(strtolower(str_replace(array('_', '-'), ' ', $key))));
			$headers[$key] = $value;
		}
	}
	if (!empty($_SERVER['REQUEST_METHOD']) && empty($headers['Method'])) {
		$headers['Method'] = $_SERVER['REQUEST_METHOD'];
	}
	return $headers;
}

$headers = get_request_headers();
if (empty($headers['DISABLE_CORS']) && empty($_REQUEST['DISABLE_CORS'])) {
	header("Access-Control-Allow-Origin: *");
}
$data = array();
$data['request_method'] = $_SERVER['REQUEST_METHOD'];
$datatype = !empty($_REQUEST['data_type']) ? $_REQUEST['data_type'] : 'json';
switch($datatype) {
	case 'json':
	default:
		header('Content-Type: application/json');
		echo json_encode($data);
		die();
		break;
}