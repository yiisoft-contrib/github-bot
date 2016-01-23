<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\components;


use Yii;
use yii\base\Component;

class Github extends Component
{
	/**
	 * @return \Github\Client
	 */
	public function client()
	{
		// create client
		$client = new \Github\HttpClient\CachedHttpClient();
		$client->setCache(new \Github\HttpClient\Cache\FilesystemCache(__DIR__ . '/../tmp/cache'));
		$client = new \Github\Client($client);

		// authenticate
		$client->authenticate(Yii::$app->params['github_token'], '', \Github\Client::AUTH_HTTP_TOKEN);

		return $client;
	}

} 