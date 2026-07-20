<?php

if (!defined('__DIR__')) {
	define('__DIR__', dirname(__FILE__));
}

main(__DIR__ . '/config.json', __DIR__ . '/app-usage.json', __DIR__ . '/storage');

function main($config_path, $app_usage_path, $storage_path) {
	if (!is_readable($config_path)) {
		response(error_ext('config.json does not exist or can not be read'));
	}
	if (!is_readable($app_usage_path)) {
		response(error_ext('app-usage.json does not exist or can not be read'));
	}

	$config = json_decode(file_get_contents($config_path), true);
	$config['storage_path'] = $storage_path;
	$config['app_usage_path'] = $app_usage_path;

	$app_usage = json_decode(file_get_contents($app_usage_path), true);
	$limit_reached = check_app_usage($app_usage);

	define('API_DEBUG', $config['debug'] === 'true');
	define('LIMIT_REACHED', $limit_reached);

	index('config', $config);

	run(get_query());
}

function run($facebook_raw_url) {
	$cache_key = set_cache_key($facebook_raw_url);
	$data = storage_get($cache_key);

	if (empty($data)) {
		if (LIMIT_REACHED) {
			return response(error(429, 'facebook rate limiting'));
		}

		if (stripos($facebook_raw_url, 'https://graph.facebook.com') === false) {
			$facebook_raw_url = 'https://graph.facebook.com/' . $facebook_raw_url;
		}

		$facebook_raw_url_token = remove_query_param('access_token', $facebook_raw_url);
		$facebook_raw_url_token .= '&access_token=395202813876688|73e8ede72008b231a0322e40f0072fe6';
		$facebook_raw_url_token .= '&locale=en_US';

		$response = client_request('GET', $facebook_raw_url_token);

		if (!empty($response)) {
			if (!empty($response['headers']) && !empty($response['headers']['x-app-usage'])) {
				$app_usage = json_decode($response['headers']['x-app-usage'], true);

				set_app_usage($app_usage);
			}

			if (!empty($response['body'])) {
				$data = $response['body'];
			}

			if (!empty($response['http_code']) && $response['http_code'] === '200') {
				storage_set($cache_key, $data);
			}

		} else {
			return response(error());
		}
	}

	return response($data, true);
}

function check_app_usage($app_usage) {
	if ($app_usage['last_check'] + storage_get_cache_time() < time()) {
		return false;
	}

	foreach ($app_usage['app_usage'] as $key => $val) {
		if ($val > 90) {
			return true;
		}
	}

	return false;
}

function set_app_usage($app_usage) {
	$config = index('config');

	$app_usage = array(
		'app_usage' => $app_usage,
		'last_check' => time()
	);

	return is_writable($config['app_usage_path']) ? file_put_contents($config['app_usage_path'], json_encode($app_usage)) : false;
}

function set_cache_key($query) {
	$excluded_params = array('access_token', 'fields', 'limit');
	$cache_key = $query;

	foreach ($excluded_params as $key) {
		$cache_key = remove_query_param($key, $cache_key);
	}

	return preg_replace('#\?$#', '', $cache_key);
}

function error($code = 400, $error_message = 'service is unavailable now', $data = null, $additional = '') {
	$error = array(
		'meta' => array(
			'code' => $code,
			'error_message' => $error_message
		)
	);

	if ($data) {
		$error['data'] = $data;
	}

	if ($additional) {
		$error['meta']['_additional'] = $additional;
	}

	return $error;
}

function error_ext($additional) {
	return error(400, 'service is unavailable now', null, $additional);
}

function response($data, $json = false) {
	$output = $json ? $data : json_encode($data);

	header('Content-type: application/json; charset=utf-8');
	exit($output);
}

function client_request($type, $url, $options = null) {
	$curl_support = function_exists('curl_init');

	if ($curl_support) {
		$curl = curl_init();

		$curl_options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HEADER => true,
			CURLOPT_URL => $url,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_CONNECTTIMEOUT => 15,
			CURLOPT_TIMEOUT => 60
		);

		curl_setopt_array($curl, $curl_options);

		$response_str = curl_exec($curl);
		$curl_info = curl_getinfo($curl);
		$curl_error = curl_error($curl);

		curl_close($curl);

		if ($curl_info['http_code'] === 0) {
			return response(error_ext('curl transport error'));
		}
	} else {
		return response(error_ext('php on web-server does not support curl'));
	}

	@list ($response_headers_str, $response_body_encoded, $alt_body_encoded) = explode("\r\n\r\n", $response_str);

	if ($alt_body_encoded) {
		$response_headers_str = $response_body_encoded;
		$response_body_encoded = $alt_body_encoded;
	}

	$response_body = $response_body_encoded;

	if (!$response_body) {
		$response_body = $response_body_encoded;
	}

	$response_headers_raw_list = explode("\r\n", $response_headers_str);
	$response_http = array_shift($response_headers_raw_list);

	preg_match('#^([^\s]+)\s(\d+)\s([^$]+)$#', $response_http, $response_http_matches);
	array_shift($response_http_matches);

	list ($response_http_protocol, $response_http_code, $response_http_message) = $response_http_matches;

	$response_headers = array();
	$response_cookies = array();

	foreach ($response_headers_raw_list as $header_row) {
		list ($header_key, $header_value) = explode(': ', $header_row, 2);

		if (strtolower($header_key) === 'set-cookie') {
			$cookie_params = explode('; ', $header_value);

			if (empty($cookie_params[0])) {
				continue;
			}

			list ($cookie_name, $cookie_value) = explode('=', $cookie_params[0]);
			$response_cookies[$cookie_name] = $cookie_value;

		} else {
			$response_headers[$header_key] = $header_value;
		}
	}
	unset($header_row, $header_key, $header_value, $cookie_name, $cookie_value);

	return array(
		'status' => 1,
		'http_protocol' => $response_http_protocol,
		'http_code' => $response_http_code,
		'http_message' => $response_http_message,
		'headers' => $response_headers,
		'cookies' => $response_cookies,
		'body' => $response_body
	);
}

function storage_get_index_path($hash) {
	$config = index('config');
	return rtrim($config['storage_path'], '/') . '/_' . substr($hash, 0, 1);
}

function storage_get_cache_time() {
	$config = index('config');
	return isset($config['cache_time']) ? intval($config['cache_time']) : 3600;
}

function storage_get($key, $check_expire = true) {
	$cache_time = storage_get_cache_time();

	$hash = md5($key);
	$index_path = storage_get_index_path($hash);
	$record_path = $index_path . '/' . $hash . '.csv';


	if (!is_readable($record_path)) {
		return null;
	}

	$record_fref = fopen($record_path, 'r');
	$row = fgetcsv($record_fref, null, ';');

	if (!$row || count($row) !== 3 || ($check_expire && time() > $row[1] + $cache_time)) {
		return null;
	}

	$data = base64_decode($row[2]);

	return !empty($data) ? $data : null;
}

function storage_set($key, $value) {
	$hash = md5($key);
	$index_path = storage_get_index_path($hash);
	$record_path = $index_path . '/' . $hash . '.csv';

	if (!is_dir($index_path) && !@mkdir($index_path, 0775, true)) {
		return false;
	}

	$record_fref = fopen($record_path, 'w');
	fputcsv($record_fref, array($key, time(), base64_encode($value)), ';');
	fclose($record_fref);

	return true;
}

function index($key, $value = null, $f = false) {
	static $index = array();

	if ($value || $f) {
		$index[$key] = $value;
	}

	return !empty($index[$key]) ? $index[$key] : null;
}

function get_query() {
	$query = input('q', $_SERVER['REQUEST_URI']);
	$root = !empty ($_SERVER['PHP_SELF']) ? dirname($_SERVER['PHP_SELF']) : '';

	return ltrim(preg_replace('#^' . $root . '#', '', $query), '/');
}

function input($name, $default = null) {
	parse_str($_SERVER['QUERY_STRING'], $output);
	return isset($output[$name]) ? $output[$name] : $default;
}

function remove_query_param($param, $url) {
	$result = $url;
	$url_data = parse_url($url);

	if (!empty($url_data['query'])) {
		parse_str($url_data['query'], $query_params);

		if (!empty($query_params[$param])) {
			unset($query_params[$param]);
		}

		$url_without_query = explode('?', $url);
		$result = $url_without_query[0] . '?' . http_build_query($query_params);
	}

	return $result;
}