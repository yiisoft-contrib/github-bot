<?php

namespace yiiunit\extensions\githubbot\mocks;

use Github\Exception\RuntimeException;
use Github\HttpClient\CachedHttpClient;
use Yii;

/**
 * Class CachedHttpClientMock
 * @package yiiunit\extensions\githubbot\mocks
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class CachedHttpClientMock extends CachedHttpClient
{
	const DUMMY_TOKEN = 'correct-token';
	const EXCEPTION_BAD_CREDENTIALS_MSG = 'Bad credentials';
	const EXCEPTION_BAD_CREDENTIALS_CODE = 401;

	public $requests = [];

	/**
	 * @inheritdoc
	 */
	public function request($path, $body = null, $httpMethod = 'GET', array $headers = array(), array $options = array())
	{
		if (Yii::$app->params['github_token'] !== self::DUMMY_TOKEN) {
			throw new RuntimeException(self::EXCEPTION_BAD_CREDENTIALS_MSG, self::EXCEPTION_BAD_CREDENTIALS_CODE);
		} else {

			$this->requests[] = [
				'path' => $path,
				'body' => $body,
				'method' => $httpMethod,
				'headers' => $headers,
				'options' => $options,
			];

			$response = new \Guzzle\Http\Message\Response(200, [], []);
			switch($path) {
				case 'repos/cebe/test/issues':
					$response->addHeader('Content-Type', 'application/json');
					$response->setBody(<<<JSON
{
  "id": 1,
  "url": "https://api.github.com/repos/cebe/test/issues/1347",
  "repository_url": "https://api.github.com/repos/cebe/test",
  "html_url": "https://github.com/cebe/test/issues/1347",
  "number": 1347,
  "state": "open",
  "title": "Found a bug",
  "body": "I'm having a problem with this.",
  "user": {
    "login": "cebe",
    "id": 1,
    "url": "https://api.github.com/users/cebe",
    "html_url": "https://github.com/cebe",
    "type": "User"
  },
  "labels": [
    {
      "id": 208045946,
      "url": "https://api.github.com/repos/cebe/test/labels/ext:test",
      "name": "ext:test",
      "color": "f29513",
      "default": true
    }
  ],
  "locked": false,
  "comments": 0,
  "closed_at": null,
  "created_at": "2011-04-22T13:33:48Z",
  "updated_at": "2011-04-22T13:33:48Z"
}
JSON
);

				default:
					// nothing
			}
			return $response;
		}
	}
}
