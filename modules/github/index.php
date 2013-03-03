<?php
require_once(dirname(dirname(__DIR__)).'/classes/OAuth2Module.php');

/**
 * First register an app here: https://github.com/settings/applications/new
 * GitHub OAuth(2) docs: http://developer.github.com/v3/oauth/
 * 
 * @package StartupAPI
 * @subpackage Authentication\Githib
 */
class GithubAuthenticationModule extends OAuth2AuthenticationModule
{
	protected $userCredentialsClass = 'GithubUserCredentials';

	public function __construct($oAuth2ClientID, $oAuth2ClientSecret, $scopes = '')
	{
		parent::__construct(
			'Github',
			'http://api.github.com',
			$oAuth2ClientID,
			$oAuth2ClientSecret,
			'https://github.com/login/oauth/authorize',
			'https://github.com/login/oauth/access_token',
			$scopes,
			NULL,
			NULL,
			NULL,
			array(
				array(7051, "Logged in using Github account", 1),
				array(7052, "Added Github account", 1),
				array(7053, "Removed Github account", 0),
				array(7054, "Registered using Github account", 1),
			)
		);
	}

	public function getID()
	{
		return "github";
	}

	public function getLegendColor()
	{
		return "000000";
	}

	public static function getModulesTitle() {
		return "Github";
	}

	public static function getModulesDescription() {
		return '<p>Github login and API access module.</p>';
	}

	public function getDescription() {
		return self::getModulesDescription();
	}

	public static function getSignupURL() {
		return 'https://github.com/settings/applications/new';
	}

	public static function getModulesLogo($size = 100) {
		if ($size == 100) {
			return UserConfig::$USERSROOTURL . '/modules/github/images/octocat_100x.png';
		}
	}

	// YODO make actual github calls, not twitter
	public function getIdentity($oauth2_client_id) {
		$credentials = $this->getOAuth2Credentials($oauth2_client_id); 

		try {
			$result = $credentials->makeOAuth2Request('https://api.github.com/user', 'GET', null, array(
				'URLOPT_HTTPHEADER' => array('Accept: application/json')
			));
		} catch (OAuth2Exception $ex) {
			return null;
		}

		$data = json_decode($result, true);
		if (is_null($data)) {
			switch(json_last_error())
			{
				case JSON_ERROR_DEPTH:
					error_log('JSON Error: Maximum stack depth exceeded');
				break;
				case JSON_ERROR_CTRL_CHAR:
					error_log('JSON Error: Unexpected control character found');
				break;
				case JSON_ERROR_SYNTAX:
					error_log('JSON Error: Syntax error, malformed JSON');
				break;
				case JSON_ERROR_NONE:
					error_log('JSON Error: No errors');
				break;
			}

			return null;
		}

		if (array_key_exists('id', $data) && array_key_exists('name', $data)) {
			return $data;
		}

		return null;
	}

	protected function renderUserInfo($serialized_userinfo) {
		$user_info = unserialize($serialized_userinfo);
		?><a href="http://github.com/<?php echo UserTools::escape($user_info['login']); ?>" target="_blank">@<?php echo UserTools::escape($user_info['login']); ?></a><br/>
		<a href="http://github.com/<?php echo UserTools::escape($user_info['login']); ?>" target="_blank"><img src="<?php echo UserTools::escape($user_info['avatar_url']); ?>" title="<?php echo UserTools::escape($user_info['name']); ?>" style="max-width: 60px; max-height: 60px"/></a>
		<?php
	}
}

/**
 * @package StartupAPI
 * @subpackage Authentication\Github
 */
class GithubUserCredentials extends OAuth2UserCredentials {
	public function getHTML() {
		return '<a href="http://github.com/'.UserTools::escape($this->userinfo['login']).'" target="_blank">@'.$this->userinfo['login'].'</a>';
	}
}
