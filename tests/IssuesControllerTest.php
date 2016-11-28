<?php

namespace yiiunit\extensions\githubbot;


use app\controllers\IssuesController;
use Yii;
use yiiunit\extensions\githubbot\mocks\CachedHttpClientMock;

class IssuesControllerTest extends TestCase
{
	/**
	 * @var CachedHttpClientMock http client used to track requests
	 */
	protected $httpClient;

	protected function setUp()
	{
		parent::setUp();

		$this->httpClient = new CachedHttpClientMock();
		Yii::$container->setSingleton(\Github\HttpClient\CachedHttpClient::class, function () {
			return $this->httpClient;
		});

		$this->mockWebApplication([
			'components' => [
				'github' => \app\components\Github::class,
				'request' => [
					'enableCsrfValidation' => false,
					'enableCookieValidation' => false,
					'parsers' => [
						'application/json' => 'yii\web\JsonParser',
					],
				],
				'errorHandler' => [
					'errorAction' => 'site/error',
				],
			],
			'params' => [
				'github_token' => CachedHttpClientMock::DUMMY_TOKEN,
				'github_username' => 'username-test',
				'repositories' => [
					'dummy-test/hook-test'
				],
				'hook_secret' => 'test-secret',
				'webUrl' => 'http://www.domain.com/hookUrl',
				'actions' => [
					[
						'label' => 'expired',
						'action' => 'comment',
						'comment' => 'This issue is expired',
						'close' => true,
					],
					[
						'label' => 'info',
						'action' => 'comment',
						'comment' => 'This issue needs more info',
						'close' => false,
					],
					[
						'label' => 'ext:test',
						'action' => 'move',
						'repo' => 'cebe/test',
					],
				]
			],
		]);
	}

	protected function signRequest($request)
	{
		$hash = Yii::$app->security->hashData($request->rawBody, Yii::$app->params['hook_secret']);
		$request->headers->add('X-Hub-Signature', Yii::$app->security->macHash . '=' . substr($hash, 0, strlen(@hash_hmac(Yii::$app->security->macHash, '', '', false))));
	}

	public function testIgnoreOwnAction()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'issues');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
  	"name": "info"
  },
  "issue": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/issues/2",
    "id": 73464126,
    "number": 2,
    "title": "Spelling error in the README file",
    "state": "open",
    "body": "It looks like you accidently spelled 'commit' with two 't's."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "username-test"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'ignored'], $response);

		$this->assertCount(0, $this->httpClient->requests);
	}

	public function testIssueOnLabelComment()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'issues');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
  	"name": "info"
  },
  "issue": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/issues/2",
    "id": 73464126,
    "number": 2,
    "title": "Spelling error in the README file",
    "state": "open",
    "body": "It looks like you accidently spelled 'commit' with two 't's."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "baxterthehacker"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(1, $this->httpClient->requests);
		$request = reset($this->httpClient->requests);
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/2/comments', $request['path']);
		$this->assertContains('This issue needs more info', $request['body']);
	}

	public function testIssueOnLabelCommentAndClose()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'issues');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
  	"name": "expired"
  },
  "issue": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/issues/2",
    "id": 73464126,
    "number": 2,
    "title": "Spelling error in the README file",
    "state": "open",
    "body": "It looks like you accidently spelled 'commit' with two 't's."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "baxterthehacker"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(2, $this->httpClient->requests);
		$request1 = $this->httpClient->requests[0];
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/2/comments', $request1['path']);
		$this->assertContains('This issue is expired', $request1['body']);
		$request2 = $this->httpClient->requests[1];
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/2', $request2['path']);
		$this->assertContains('{"state":"closed"}', $request2['body']);
	}

	public function testPrOnLabelComment()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'pull_request');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
  	"name": "info"
  },
  "number": 1,
  "pull_request": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/pulls/1",
    "id": 34778301,
    "number": 1,
    "title": "Update the README with new information",
    "state": "open",
    "body": "This is a pretty simple change that we need to pull into master."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "baxterthehacker"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(1, $this->httpClient->requests);
		$request = reset($this->httpClient->requests);
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/1/comments', $request['path']);
		$this->assertContains('This issue needs more info', $request['body']);
	}

	public function testPrOnLabelCommentAndClose()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'pull_request');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
  	"name": "expired"
  },
  "number": 1,
  "pull_request": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/pulls/1",
    "id": 34778301,
    "number": 1,
    "title": "Update the README with new information",
    "state": "open",
    "body": "This is a pretty simple change that we need to pull into master."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "baxterthehacker"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(2, $this->httpClient->requests);
		$request1 = $this->httpClient->requests[0];
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/1/comments', $request1['path']);
		$this->assertContains('This issue is expired', $request1['body']);
		$request2 = $this->httpClient->requests[1];
		$this->assertEquals('repos/baxterthehacker/public-repo/pulls/1', $request2['path']);
		$this->assertContains('{"state":"closed"}', $request2['body']);
	}

	public function testIssueOnLabelMove()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'issues');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
    "name": "ext:test"
  },
  "issue": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/issues/2",
    "html_url": "https://github.com/baxterthehacker/public-repo/issues/2",
    "id": 73464126,
    "number": 2,
    "title": "Spelling error in the README file",
    "state": "open",
    "body": "It looks like you accidently spelled 'commit' with two 't's.",
	"labels": [
      {
        "id": 208045946,
        "url": "https://api.github.com/repos/baxterthehacker/public-repo/labels/ext:test",
        "name": "ext:test",
        "color": "fc2929",
        "default": false
      }
    ],
    "user": {
      "login": "baxterthehacker"
    }
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "cebe"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(3, $this->httpClient->requests);
		$request1 = $this->httpClient->requests[0];
		$this->assertEquals('repos/cebe/test/issues', $request1['path']);
		$this->assertContains('This issue has originally been reported by @baxterthehacker', $request1['body']);
		$this->assertContains('https:\/\/github.com\/baxterthehacker\/public-repo\/issues\/2', $request1['body']);
		$this->assertContains('Moved here by @cebe', $request1['body']);
		$this->assertContains("It looks like you accidently spelled 'commit' with two 't's.", $request1['body']);
		$request2 = $this->httpClient->requests[1];
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/2/comments', $request2['path']);
		$this->assertContains('Issue moved to', $request2['body']);
		$request3 = $this->httpClient->requests[2];
		$this->assertEquals('repos/baxterthehacker/public-repo/issues/2', $request3['path']);
		$this->assertContains('{"state":"closed"}', $request3['body']);
	}

	/**
	 * moving PRs should not work
	 */
	public function testPrOnLabelDontMove()
	{
		$_SERVER['REQUEST_METHOD'] = 'POST';
		$_SERVER["CONTENT_TYPE"] = 'application/json';
		Yii::$app->request->headers->add('X-Github-Event', 'pull_request');
		// body is reduced size, real github request is more verbose
		Yii::$app->request->rawBody = <<<JSON
{
  "action": "labeled",
  "label": {
    "name": "ext:test"
  },
  "number": 1,
  "pull_request": {
    "url": "https://api.github.com/repos/baxterthehacker/public-repo/pulls/1",
    "id": 34778301,
    "number": 1,
    "title": "Update the README with new information",
    "state": "open",
    "body": "This is a pretty simple change that we need to pull into master."
  },
  "repository": {
    "id": 35129377,
    "name": "public-repo",
    "full_name": "baxterthehacker/public-repo",
    "owner": {
      "login": "baxterthehacker",
      "type": "User"
    }
  },
  "sender": {
    "login": "baxterthehacker"
  }
}
JSON;
		$this->signRequest(Yii::$app->request);

		/** @var $controller IssuesController */
		list($controller, ) = Yii::$app->createController('issues');
		$response = $controller->runAction('index');
		$this->assertEquals(['success' => true, 'action' => 'processed'], $response);

		$this->assertCount(0, $this->httpClient->requests);
	}

}