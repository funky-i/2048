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

function error($data = array()) {
	$error = [];
	$error = array(
		'status' => $data['status'],
		'description' => (isset($data['description']))? $data['description'] : ''
	);

	return $error;
}

function console($data) {
	echo '<pre>';
	print_r($data);
	echo '</pre>';
	exit;	
}

function generateCode($len){

	srand( date("s") );
	$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
	$chars.= "1234567890";
	$ret_str = "";
	$num = strlen($chars);

	for($i=0; $i < $len; $i++) {
		$ret_str.= $chars[rand()%$num];
	}
	return $ret_str;

}


function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
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