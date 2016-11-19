<?php
/**
 *
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use Github\Api\Repo;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;
use yiiunit\extensions\githubbot\mocks\RepoMock;

class GithubController extends  Controller
{
	public function init()
	{
		parent::init();
		if (empty(Yii::$app->params['hook_secret'])) {
			throw new Exception('Config param "hook_secret" is not configured!');
		}
	}

	public function hooks()
	{
		$base = rtrim(Yii::$app->params['webUrl'], '/');
		return [
			'issues' => $base . '/index.php?r=issues',
		];
	}

	public function actionRegister(array $limitRepos = [])
	{
		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$repositories = Yii::$app->params['repositories'];
		if (!empty($limitRepos)) {
			$repositories = array_intersect($limitRepos, $repositories);
		}

		// create hooks:
		foreach($repositories as $urepo) {
			foreach($this->hooks() as $hookName => $hookUrl) {

				$this->stdout("registering ");
				$this->stdout("$hookName", Console::BOLD);
				$this->stdout(" hook on ");
				$this->stdout($urepo, Console::BOLD);
				$this->stdout('...');
				list($user, $repo) = explode('/', $urepo);

				// https://developer.github.com/v3/repos/hooks/#create-a-hook
                $api = YII_ENV_TEST ? new RepoMock($client) : new Repo($client);

				// check if hook exists
				$hookId = null;
				foreach ($api->hooks()->all($user, $repo) as $hook) {
					if ($hook['name'] == 'web' && isset($hook['config']['url']) && $hook['config']['url'] === $hookUrl) {
						$this->stdout("already registered, updating...");
						$hookId = $hook['id'];
						break;
					}
				}

				$params = [
					'name' => 'web',
					'config' => [
						'url' => $hookUrl,
						'content_type' => 'json',
						'secret' => Yii::$app->params['hook_secret'],
					],
					'events' => ['issues'],
					'active' => true,
				];
				if ($hookId) {
					$response = $api->hooks()->update($user, $repo, $hookId, $params);
					//print_r($response);
					$this->stdout("updated.\n", Console::FG_GREEN, Console::BOLD);
				} else {
					$response = $api->hooks()->create($user, $repo, $params);
					//print_r($response);
					$this->stdout("added.\n", Console::FG_GREEN, Console::BOLD);
				}
			}
		}
	}

	public function actionUnRegister(array $limitRepos = [])
	{
		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$repositories = Yii::$app->params['repositories'];
		if (!empty($limitRepos)) {
			$repositories = array_intersect($limitRepos, $repositories);
		}

		// remove hooks:
		foreach($repositories as $urepo) {
			foreach($this->hooks() as $hookName => $hookUrl) {

				$this->stdout("un-registering ");
				$this->stdout("$hookName", Console::BOLD);
				$this->stdout(" hook on ");
				$this->stdout($urepo, Console::BOLD);
				$this->stdout('...');
				list($user, $repo) = explode('/', $urepo);

				// https://developer.github.com/v3/repos/hooks/#create-a-hook
				$api = new Repo($client);

				// check if hook exists
				foreach ($api->hooks()->all($user, $repo) as $hook) {
					if ($hook['name'] == 'web' && isset($hook['config']['url']) && $hook['config']['url'] === $hookUrl) {
						$api->hooks()->remove($user, $repo, $hook['id']);
					}
					break;
				}
				$this->stdout("done.\n", Console::FG_GREEN, Console::BOLD);
			}
		}
	}
}