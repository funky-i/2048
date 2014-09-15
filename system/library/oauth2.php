<?php
class Oauth2 {
	private $handle;
	private $server;
	private $token;

	private $OAuthRequest;
	private $OAuthResponse;

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');
		
		$dsn      = 'mysql:dbname='. DB_DATABASE . ';host=' . DB_HOSTNAME;
		$storage = new OAuth2\Storage\Pdo(array('dsn' => $dsn, 'username' => DB_USERNAME, 'password' => DB_PASSWORD));

		// $server = new OAuth2\Server($storage);
		// $grantType = new OAuth2\GrantType\RefreshToken($storage, array(
		//     'always_issue_new_refresh_token' => true
		// ));
		// $accessToken = new OAuth2\ResponseType\AccessToken($accessStorage, $refreshStorage, array(
		//     'refresh_token_lifetime' => 2419200,
		// ));

		$server = new OAuth2\Server($storage, array(
		    'access_lifetime' => 1209600
		));

		$refresh_token = array(
			'refresh_token_lifetime' => 1209600,
			'always_issue_new_refresh_token' => true
		);

		$server->addGrantType(new OAuth2\GrantType\UserCredentials($storage));
		$server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
		$server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));		
		$server->addGrantType(new OAuth2\GrantType\RefreshToken($storage, $refresh_token));

		$this->server = $server;
		$this->OAuthRequest = OAuth2\Request::createFromGlobals();
		$this->OAuthResponse = new OAuth2\Response();

		if (isset($_SESSION['oauth_token'])) {
			$this->token = $_SESSION['oauth_token'];
		}

	}

	public function handleTokenRequest() {

		$token = $this->server->handleTokenRequest($this->OAuthRequest);

		// if (!$token = $this->server->grantAccessToken($this->OAuthRequest, $this->OAuthResponse)) {
		//     $this->OAuthResponse->send();
		//     die();
		// }
		$this->token = $token->getParameters();
		if ($token) {
			$_SESSION['oauth_token'] = $token->getParameters();
		}

		return $this->token;
		
	}

	public function getToken() {		
		return $this->token;
	}

	public function verifyResourceRequest() {
		$isVerified = false;

		if ($this->server->verifyResourceRequest($this->OAuthRequest)) {
			$isVerified = true;
		}

		return $isVerified;
	}
}