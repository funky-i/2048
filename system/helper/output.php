<?php
function removeHtml($string) {
	$pattern = "/&#?[a-z0-9]+;/i";
	$text = trim(preg_replace($pattern, "", strip_tags(nl2br(html_entity_decode($string, ENT_QUOTES, 'UTF-8')))));
	return $text;
}

function getParams() {

	if (!empty($_POST)) {		
		$params['data'] = $_POST;	
	} else {
		$handle = fopen('php://input','r');
		$jsonInput = fgets($handle);
		$decoded = json_decode($jsonInput,true);
		$params['data'] = $decoded;			
	}	

	return $params;
}

function initHeader() {
	
	// $this->response->addHeader('Access-Control-Allow-Headers:*');
	// $this->response->addHeader('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	// $this->response->addHeader('Access-Control-Allow-Credentials, true');
	// $this->response->addHeader('Access-Control-Allow-Origin:*');
	// header('Access-Control-Allow-Headers:*');

	// header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	// header('Access-Control-Allow-Headers:*');
	// header('Access-Control-Allow-Headers: Origin, Content-Type');
	// header('Access-Control-Allow-Credentials, true');

	// header('Access-Control-Allow-Origin: *');
	// header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept');
	// header('Access-Control-Allow-Credentials, true');

	header('Access-Control-Allow-Origin: http://localhost:8100');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
    header('Access-Control-Allow-Headers: Accept, X-Requested-With,Content-Type');
    header('Access-Control-Allow-Credentials: true');
}