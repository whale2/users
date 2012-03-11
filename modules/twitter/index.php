<?php
/**
 * @package StartupAPI
 * @subpackage Authentication
 */
require_once(dirname(dirname(dirname(__FILE__))).'/OAuthModule.php');

class TwitterAuthenticationModule extends OAuthAuthenticationModule
{
	protected $userCredentialsClass = 'TwitterUserCredentials';

	public function __construct($oAuthConsumerKey, $oAuthConsumerSecret)
	{
		parent::__construct(
			'Twitter',
			'http://api.twitter.com',
			$oAuthConsumerKey,
			$oAuthConsumerSecret,
			'https://api.twitter.com/oauth/request_token',
			'https://api.twitter.com/oauth/access_token',
			'https://api.twitter.com/oauth/authenticate',
			array('HMAC-SHA1'),
			'http://api.twitter.com',
			UserConfig::$USERSROOTURL.'/modules/twitter/login-button.png',
			UserConfig::$USERSROOTURL.'/modules/twitter/login-button.png',
			UserConfig::$USERSROOTURL.'/modules/twitter/login-button.png',
			array(
				array(4001, "Logged in using Twitter account", 1),
				array(4002, "Added Twitter account", 1),
				array(4003, "Removed Twitter account", 0),
				array(4004, "Registered using Twitter account", 1),
			)
		);
	}

	public function getID()
	{
		return "twitter";
	}

	public function getLegendColor()
	{
		return "60bddc";
	}

	public function getTitle()
	{
		return "Twitter";
	}

	public function getIdentity($oauth_user_id) {
		// get twitter handle
		#$request = new OAuthRequester('http://api.twitter.com/1/account/verify_credentials.xml', 'GET');
		$request = new OAuthRequester('http://api.twitter.com/1/account/verify_credentials.json', 'GET');
		$result = $request->doRequest($oauth_user_id);

		if ($result['code'] == 200) {
			$data = json_decode($result['body'], true);
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

			if (!is_null($data) && array_key_exists('id', $data) && array_key_exists('name', $data)) {
				return $data;
			}
		}

		return null;
	}

	protected function renderUserInfo($serialized_userinfo) {
		$user_info = unserialize($serialized_userinfo);
		?><a href="http://twitter.com/<?php echo UserTools::escape($user_info['screen_name']); ?>" target="_blank">@<?php echo UserTools::escape($user_info['screen_name']); ?></a><br/>
		<a href="http://twitter.com/<?php echo UserTools::escape($user_info['screen_name']); ?>" target="_blank"><img src="<?php echo UserTools::escape($user_info['profile_image_url']); ?>" title="<?php echo UserTools::escape($user_info['name']); ?> (@<?php echo UserTools::escape($user_info['screen_name']); ?>)" style="max-width: 60px; max-height: 60px"/></a>
		<?php
	}
}

class TwitterUserCredentials extends OAuthUserCredentials {
	public function getHTML() {
		return '<a href="http://twitter.com/'.UserTools::escape($this->userinfo['screen_name']).'" target="_blank">@'.$this->userinfo['screen_name'].'</a>';
	}
}
