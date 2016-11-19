<?php

namespace yiiunit\extensions\githubbot;

use app\commands\GithubController;
use Yii;
use yiiunit\extensions\githubbot\mocks\CachedHttpClientMock;
use yiiunit\extensions\githubbot\mocks\GithubControllerMock;

/**
 * Class GithubControllerTest
 * @package yiiunit\extensions\githubbot
 * @author Boudewijn Vahrmeijer <info@dynasource.eu>
 */
class GithubControllerTest extends TestCase
{
    public function testInit_HookSecretException()
    {
        $this->setExpectedException('yii\base\Exception', 'Config param "hook_secret" is not configured!');
        new GithubController('github', Yii::$app);
    }

    public function testHooks_UndefinedWebUrl()
    {
        $this->mockApplication([
            'params' => [
                'hook_secret' => 'test-secret',
            ],
        ]);
        $controller = new GithubController('github', Yii::$app);

        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Undefined index: webUrl');
        $controller->hooks();
    }

    public function testHooks()
    {
        $this->mockApplication([
            'params' => [
                'hook_secret' => 'test-secret',
                'webUrl' => 'http://localhost'
            ],
        ]);
        $controller = new GithubController('github', Yii::$app);
        $this->assertEquals([
            'issues' => 'http://localhost/index.php?r=issues'
        ], $controller->hooks());
    }

    public function testActionRegister_RequiredGithubComponentException()
    {
        $this->mockApplication([
            'params' => [
                'hook_secret' => 'test-secret'
            ],
        ]);
        $controller = new GithubController('github', Yii::$app);
        $this->setExpectedException('\yii\base\UnknownPropertyException', 'Getting unknown property: yii\console\Application::github');
        $controller->runAction('register');
    }

    public function testActionRegister_RequiredRepositoriesParamException()
    {
        $this->mockApplication([
            'components' => [
                'github' => 'app\components\Github',
            ],
            'params' => [
                'github_token' => 'test-token',
                'github_username' => 'test-username',
                'hook_secret' => 'test-secret'
            ],
        ]);
        $controller = new GithubController('github', Yii::$app);
        $this->setExpectedException('PHPUnit_Framework_Error_Notice', 'Undefined index: repositories');
        $controller->runAction('register');
    }

    public function testActionRegister_WrongTokenException()
    {
        $this->mockApplication([
            'components' => [
                'github' => 'app\components\Github',
            ],
            'params' => [
                'github_token' => 'wrong' . CachedHttpClientMock::DUMMY_TOKEN,
                'github_username' => 'username-test',
                'repositories' => [
                    'dummy-test/hook-test'
                ],
                'hook_secret' => 'test-secret',
                'webUrl' => 'http://www.domain.com/hookUrl'
            ],
        ]);
        $controller = new GithubControllerMock('github', Yii::$app);
        $this->setExpectedException('Github\Exception\RuntimeException', 'Bad credentials', 401);
        $controller->runAction('register');
    }

    public function testActionRegister()
    {
        $config = [
            'components' => [
                'github' => 'app\components\Github',
            ],
            'params' => [
                'github_token' => CachedHttpClientMock::DUMMY_TOKEN,
                'github_username' => 'username-test',
                'repositories' => [
                    'dummy-test/hook-test'
                ],
                'hook_secret' => 'test-secret',
                'webUrl' => 'http://www.domain.com/hookUrl'
            ],
        ];
        $this->mockApplication($config);
        $controller = new GithubControllerMock('github', Yii::$app);
        $controller->runAction('register');
        $actual = $controller->flushStdOutBuffer();
        $this->assertEquals("registering issues hook on " . $config['params']['repositories'][0] . "...added.\n", $actual);
    }
}