<?php
class Oauth2 {
	private $handle;
	private $server;
	private $token;
	private $storage;
	private $available_scope;
	private $refresh_token;

	private $OAuthRequest;
	private $OAuthResponse;

	private $request;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$config = array(
			'user_table' => 'customer',
		);

		$this->available_scope = 'offline_access';

		$dsn      = 'mysql:dbname='. DB_DATABASE . ';host=' . DB_HOSTNAME;
		$this->storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => DB_USERNAME, 'password' => DB_PASSWORD), $config);

		// $server = new OAuth2\Server($storage);
		// $grantType = new OAuth2\GrantType\RefreshToken($storage, array(
		//     'always_issue_new_refresh_token' => true
		// ));
		// $accessToken = new OAuth2\ResponseType\AccessToken($accessStorage, $refreshStorage, array(
		//     'refresh_token_lifetime' => 2419200,
		// ));		
		
		// $this->request->post['grant_type'] = 'client_credentials';
		// print_r($this->request);
		$this->server = new OAuth2\Server($this->storage, array(
		    'access_lifetime' => 1209600
		));

		$this->refresh_token = array(
			'refresh_token_lifetime' => 1209600,
			'always_issue_new_refresh_token' => true
		);

		$this->OAuthRequest = OAuth2\Request::createFromGlobals();
		$this->OAuthResponse = new OAuth2\Response();
		
		// $this->OAuthRequest = OAuth2\Request::request('client_id');

		if (isset($this->session->data['oauth_token'])) {
			$this->token = $this->session->data['oauth_token'];		
		}

	}

	public function getUser() {
		$user = $this->server->getAccessTokenData($this->OAuthRequest);
		$info = $this->storage->getUserDetails($user['user_id']);

		return $info;
		
	}

	public function handleTokenRequest() {

		if (!$this->token) {
			$this->server->addGrantType(new OAuth2\GrantType\UserCredentials($this->storage));
			$this->server->addGrantType(new OAuth2\GrantType\ClientCredentials($this->storage));
			$this->server->addGrantType(new OAuth2\GrantType\AuthorizationCode($this->storage));		
			$this->server->addGrantType(new OAuth2\GrantType\RefreshToken($this->storage, $this->refresh_token));

			// $this->server->request['grant_type'] = 'password';
			
			// $this->server->grantAccessToken($this->OAuthRequest);
			$token = $this->server->handleTokenRequest($this->OAuthRequest);
			
			$this->token = $token->getParameters();
			if ($token) {
				$this->session->data['oauth_token'] = $token->getParameters();
			}
		}
		
		return $this->token;
		
	}

	public function getToken() {
		return $this->token;
	}

	public function verifyResourceRequest($require_scope = null) {
		$isVerified = false;

		// $token =  $this->server->grantAccessToken($this->OAuthRequest, $this->OAuthResponse);
		// $token = $this->storage->scopeExists($this->available_scope);
		// $token = $this->server->getScopeUtil()->scopeExists($user['scope']);
		// $default_scope = $this->server->getScopeUtil()->getDefaultScope($user['client_id']);
		// $user = $this->server->getAccessTokenData($this->OAuthRequest);
		// $scope = $this->server->getScopeUtil()->checkScope($user['scope'], $this->available_scope);

		if (isset($require_scope)) 
			$this->available_scope = $require_scope;
		
		if ($this->server->verifyResourceRequest($this->OAuthRequest, $this->OAuthResponse, $this->available_scope)) {
			$isVerified = true;
		}

		return $isVerified;
	}
}