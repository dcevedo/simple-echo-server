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
	if (isset($_SERVER['HTTP_ORIGIN']) && !headers_sent()) {
		header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
		header('Access-Control-Allow-Credentials: true');
		//header('Access-Control-Max-Age: 86400');    // cache for 1 day
	}
	// Access-Control headers are received during OPTIONS requests
	if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS' && !headers_sent()) {
		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
			header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
		}

		if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
			header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
		}
	}
}
$data = array();
$data['request_method'] = $_SERVER['REQUEST_METHOD'];
$data['request_parameters'] = $_REQUEST;

if (!empty($_REQUEST['timeout']) && intval($_REQUEST['timeout']) == $_REQUEST['timeout'] && $_REQUEST['timeout'] > 0) {
	sleep($_REQUEST['timeout']);
	$data['timeout'] = $_REQUEST['timeout'];
}


//Allow data size to be set. An array with data_size random values will be included in the output.
//If data_type is set, it is assumed that the server should return the random values as if it were a file
if (!empty($_REQUEST['data_size']) && intval($_REQUEST['data_size']) == $_REQUEST['data_size'] && $_REQUEST['data_size'] > 0) {
	
	$length = intval($_REQUEST['data_size']);
	$array = array();
	while (--$length) {
		$array[] = chr(rand(0, 100));
	}
	if (!empty($_REQUEST['data_type'])) {
		switch($_REQUEST['data_type']) {
			case 'jpg':
			case 'png':
			case 'gif':
				header('Content-Type: image/' . $_REQUEST['data_type']);
				break;
			default:
				header('Content-Type: application/octet-stream');
				header('Content-Transfer-Encoding: Binary'); 
				header('Content-disposition: attachment; filename="' . microtime(TRUE) . '.' . $_REQUEST['data_type'] . '"');
				break;
		} 

		echo $array;
		die();
	} else {
		$data['data'] = $array;
	}
}

$datatype = !empty($_REQUEST['data_type']) ? $_REQUEST['data_type'] : 'json';


switch($datatype) {
	case 'json':
	default:
		header('Content-Type: application/json');
		echo json_encode($data);
		die();
		break;
}