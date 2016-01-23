<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\commands;


use Github\Api\Repo;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class GithubController extends  Controller
{
	public $hooks = [
		'issues' => 'http://bot.cebe.cc/index.php?r=issues',
	];

	public function actionRegister()
	{
		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		// create hooks:
		foreach(Yii::$app->params['repositories'] as $urepo) {
			foreach($this->hooks as $hookName => $hookUrl) {

				$this->stdout("registering ");
				$this->stdout("$hookName", Console::BOLD);
				$this->stdout(" hook on ");
				$this->stdout($urepo, Console::BOLD);
				$this->stdout('...');
				list($user, $repo) = explode('/', $urepo);

				// https://developer.github.com/v3/repos/hooks/#create-a-hook
				$api = new Repo($client);

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
						//'secret' =>  // TODO
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

	public function actionUnRegister()
	{
		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		// create hooks:
		foreach(Yii::$app->params['repositories'] as $urepo) {
			foreach($this->hooks as $hookName => $hookUrl) {

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