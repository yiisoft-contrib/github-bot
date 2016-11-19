<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\components;


use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\base\Security;
use yii\web\BadRequestHttpException;
use yiiunit\extensions\githubbot\mocks\CachedHttpClientMock;

class Github extends Component
{
	/**
	 * @return \Github\Client
	 */
	public function client()
	{
		// create client
        $client = YII_ENV_TEST ? new CachedHttpClientMock() : new \Github\HttpClient\CachedHttpClient();
		$client->setCache(new \Github\HttpClient\Cache\FilesystemCache(__DIR__ . '/../tmp/github-cache'));
		$client = new \Github\Client($client);

		if (empty(Yii::$app->params['github_token'])) {
			throw new Exception('Config param "github_token" is not configured!');
		}
		if (empty(Yii::$app->params['github_username'])) {
			throw new Exception('Config param "github_username" is not configured!');
		}

		// authenticate
		$client->authenticate(Yii::$app->params['github_token'], '', \Github\Client::AUTH_HTTP_TOKEN);

		return $client;
	}

	public function verifyRequest($body)
	{
		// 'sha1='+OpenSSL::HMAC.hexdigest(HMAC_DIGEST, secret, body)
		// https://github.com/github/github-services/blob/f3bb3dd780feb6318c42b2db064ed6d481b70a1f/lib/service/http_helper.rb#L77
		if (empty(Yii::$app->params['hook_secret'])) {
			throw new Exception('Config param "hook_secret" is not configured!');
		}
		$secret = Yii::$app->params['hook_secret'];
		$signHeader = \Yii::$app->request->headers->get('X-Hub-Signature');
		if (!$signHeader || strpos($signHeader, '=') === false) {
			throw new BadRequestHttpException('X-Hub-Signature header is missing.');
		}
		list($algo, $hash) = explode('=', $signHeader, 2);
		if (!in_array($algo, ['sha1', 'sha256', 'sha384', 'sha512'])) {
			throw new BadRequestHttpException('Unknown algorithm in X-Hub-Signature header.');
		}

		$oldAlgo = Yii::$app->security->macHash;
		Yii::$app->security->macHash = $algo;
		if (Yii::$app->security->validateData($hash.$body, $secret) === false) {
			throw new BadRequestHttpException('Unable to validate submitted data.');
		}
		Yii::$app->security->macHash = $oldAlgo;
	}

} 