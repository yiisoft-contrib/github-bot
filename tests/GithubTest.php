<?php

namespace yiiunit\extensions\githubbot;

use app\components\Github;
use Yii;

/**
 * Class GithubTest
 * @package yiiunit\extensions\githubbot
 */
class GithubTest extends TestCase
{
	public function testClient_RequiredTokenException()
	{
		$this->setExpectedException('\Exception', 'Config param "github_token" is not configured!');
		(new Github())->client();
	}

	public function testClient_RequiredUserException()
	{
		$this->mockApplication([
			'params' => [
				'github_token' => 'test-token'
			]
		]);
		$this->setExpectedException('\Exception', 'Config param "github_username" is not configured!');
		(new Github())->client();
	}

	public function testClient_Authentication()
	{
		$this->mockApplication([
			'params' => [
				'github_token' => 'test-token',
				'github_username' => 'test-username',
			]
		]);
		$client = (new Github())->client();
		$this->assertInstanceOf('Github\Client', $client);
		$this->assertInstanceOf('Github\HttpClient\HttpClient', $this->getInvisibleProperty('httpClient', $client));
	}

	public function testVerifyRequest_RequiredHookSecretException()
	{
		$this->setExpectedException('\yii\base\Exception', 'Config param "hook_secret" is not configured!');
		(new Github())->verifyRequest('');
	}

	public function testVerifyRequest_MissingSignatureException()
	{
		$this->mockWebApplication([
			'params' => [
				'hook_secret' => 'test-secret',
			],
		]);
		$this->setExpectedException('\yii\web\BadRequestHttpException', 'X-Hub-Signature header is missing.');
		(new Github())->verifyRequest('');
	}

	public function testVerifyRequest_UnknownSignatureException()
	{
		$this->mockWebApplication([
			'params' => [
				'hook_secret' => 'test-secret',
			],
		]);
		Yii::$app->request->getHeaders()->set('X-Hub-Signature', "unknown=dummy");
		$this->setExpectedException('\yii\web\BadRequestHttpException', 'Unknown algorithm in X-Hub-Signature header.');
		(new Github())->verifyRequest('');
	}

	/**
	 * @dataProvider signatureDataProvider
	 */
	public function testVerifyRequest_ValidationException($signature)
	{
		$this->mockWebApplication([
			'params' => [
				'hook_secret' => 'test-secret',
			],
		]);
		Yii::$app->request->getHeaders()->set('X-Hub-Signature', "$signature=hash");
		$this->setExpectedException('\yii\web\BadRequestHttpException', 'Unable to validate submitted data.');
		(new Github())->verifyRequest('');
	}

	public function signatureDataProvider()
	{
		return [['sha1'], ['sha256'], ['sha384'], ['sha512']];
	}

}