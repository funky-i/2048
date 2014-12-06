<?php
use Facebook\FacebookSession;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookResponse;
use Facebook\FacebookSDKException;
use Facebook\FacebookRequestException;
use Facebook\FacebookAuthorizationException;
use Facebook\GraphObject;
use Facebook\GraphUser;
use Facebook\GraphLocation;

class Facebook {
	private $config;	
	private $db;	
	private $session;
	private $url;
	
	private $permissions = array('public_profile', 'publish_stream', 'email','user_birthday','user_location');
	private $appid;
	private $appsecret;
	private $isLogged;
	private $token;
	private $user;	
	private $status;
	private $route_uri;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->session = $registry->get('session');
		$this->db = $registry->get('db');
		$this->url = $registry->get('url');
		
		$this->appid = '912881628722070';
		$this->appsecret = 'c7ae661d29045adf056c2baa82fa8a22';
		
		$this->isLogged = false;
		$this->route_uri = 'rest/facebook';

		FacebookSession::setDefaultApplication($this->appid, $this->appsecret);

		if ($this->valid()) {
			$this->init();
		} else {
			$route = $this->url->link(urlencode($this->route_uri));
			$helper = new FacebookRedirectLoginHelper($route);
			try {
			    $session = $helper->getSessionFromRedirect();
			} catch(FacebookRequestException $ex) {
			    // When Facebook returns an error
			} catch(\Exception $ex) {
			    // When validation fails or other local issues			    
			}

			if (isset($session)) {
				$this->token($session);
				$this->init();
			}
		}
		
	}

	public function accessByToken($token) {
		$session = new FacebookSession($token);
		try {
		  $session->validate();
		} catch (FacebookRequestException $ex) {
		  // Session not valid, Graph API returned an exception with the reason.
		} catch (\Exception $ex) {
		  // Graph API returned info, but it may mismatch the current app or have expired.
		}


		if ($session) {
			
			$request = new FacebookRequest($session, 'GET', '/me');
			$response = $request->execute();
			$graphObject = $response->getGraphObject();
			$user = $response->getGraphObject(GraphUser::className());
			$location = $response->getGraphObject(GraphLocation::className());

			$data = array(
				'user' => $user,
				'location' => $location
			);

			$user_data = $this->pattern($data);
			return $user_data;
		}
	}

	public function init() {
		$request = new FacebookRequest($this->session->data['facebook_session'], 'GET', '/me');
		$response = $request->execute();
		$graphObject = $response->getGraphObject();
		$user = $response->getGraphObject(GraphUser::className());
		$location = $response->getGraphObject(GraphLocation::className());

		$data = array(
			'user' => $user,
			'location' => $location
		);

		$this->pattern($data);
	}

	public function getUser() {
		return $this->user;
	}

	public function uri($uri = '') {
		$loginUrl = '';

		$permissions = $this->permissions;

		if (!empty($uri))
			$this->route_uri = $uri;

		$route = $this->url->link(urlencode($this->route_uri));

		$helper = new FacebookRedirectLoginHelper($route);
		$loginUrl = $helper->getLoginUrl($permissions);			

		return $loginUrl;
	}

	public function valid() {
		$isValid = false;

		if (isset($this->session->data['facebook_session'])) {
			$info = $this->session->data['facebook_session']->getSessionInfo();			
			$isValid = $info->isValid();
		}

		return $isValid;
	}

	public function pattern($data) {
		$user = $data['user'];
		$local = $user->getLocation();

		$response['email'] = $user->getEmail();
		$response['password'] = $this->password($user->getId());
		$response['firstname'] = $user->getFirstName();
		$response['lastname'] = $user->getLastName();
		$response['fax'] = '';
		$response['telephone'] = '';
		$response['company'] = '';
		$response['address_1'] = '';
		$response['address_2'] = '';
		$response['city'] = '';
		$response['postcode'] = '';
		$response['country_id'] = 209;
		$response['zone_id'] = 3192;
		$response['company_id'] = 0;
		$response['tax_id'] = 0;

		// Facebook
		$response['facebook_id'] = $user->getId();
		$response['name'] = $user->getName();
		$response['link'] = $user->getLink();
		$response['username'] = '';
		$response['birthday'] = '';
		$response['gender'] = '';
		$response['token'] = '';
		$response['optional'] = array($user, $local);

		$this->user = $response;

		return $response;
		
	}

	public function password($str) {
		$password = generateCode(5);
		$fbconnect_apisecret = $this->appsecret;

		$password .= substr($fbconnect_apisecret, 0, 3) . substr($str, 0, 4) . substr($fbconnect_apisecret, -3);

		return strtolower($password);
	}

	public function token($session = '') {
		
		if ($session) {
			$this->session->data['facebook_token'] = $session->getToken();
			$this->session->data['facebook_session'] = $session;
		} else {
			$this->session->data['facebook_token'] = '';
		}

		return $this->session->data['facebook_token'];
	}

	public function logged() {
		return $this->isLogged;
	}

}