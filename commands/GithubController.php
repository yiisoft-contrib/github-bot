<?php

namespace app\commands;

use Github\Api\Repo;
use Github\Client;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * This command is used to register the bot for events on the github API.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class GithubController extends  Controller
{
	/**
	 * @var bool specify this flag if the bot github user has no admin privileges for managing hooks.
	 * You can enter your github name and password instead.
	 */
	public $noAdmin = false;


	public function options($actionID)
	{
		return array_merge(parent::options($actionID), ['noAdmin']);
	}

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
			// register for issues events and send them to issues controller
			// also send pull request events to issues controller
			'issues,pull_request' => $base . '/index.php?r=issues',
		];
	}


	/**
	 * Check on which repos events are registered on the github API.
	 *
	 * @param array $limitRepos limit this call to a certain set of repos. This is a comma separated list of repos.
	 * The default is to run against all configured repos.
	 */
	public function actionStatus(array $limitRepos = [])
	{
		$client = $this->getClient();

		$repositories = Yii::$app->params['repositories'];
		if (!empty($limitRepos)) {
			$repositories = array_intersect($limitRepos, $repositories);
		}

		// check hooks:
		foreach($repositories as $urepo) {
			foreach($this->hooks() as $hookName => $hookUrl) {

				$this->stdout("checking ");
				$this->stdout("$hookName", Console::BOLD);
				$this->stdout(" hook on ");
				$this->stdout($urepo, Console::BOLD);
				$this->stdout('...');
				list($user, $repo) = explode('/', $urepo);

				// https://developer.github.com/v3/repos/hooks/#create-a-hook
				$api = Yii::createObject(\Github\Api\Repo::class, [$client]);

				// check if hook exists
				$hookId = null;
				foreach ($api->hooks()->all($user, $repo) as $hook) {
					if ($hook['name'] === 'web' && isset($hook['config']['url']) && $hook['config']['url'] === $hookUrl) {
						$hookId = $hook['id'];
						break;
					}
				}
				if ($hookId) {
					$this->stdout("registered.\n", Console::BOLD, Console::FG_GREEN);
				} else {
					$this->stdout("not registered.\n", Console::BOLD, Console::FG_RED);
				}
			}
		}
	}

	/**
	 * Register for events on the github API.
	 *
	 * The bot user needs Admin privilege to do this. The privilege can be removed
	 * after register action has run, it only needs Write privilege for normal actions.
	 *
	 * Register can safely run multiple times, it does check whether a hook already exists
	 * and updates the hook instead of adding duplicates.
	 *
	 * @param array $limitRepos limit this call to a certain set of repos. This is a comma separated list of repos.
	 * The default is to run against all configured repos.
	 */
	public function actionRegister(array $limitRepos = [])
	{
		$client = $this->getClient();

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
				$api = Yii::createObject(\Github\Api\Repo::class, [$client]);

				// check if hook exists
				$hookId = null;
				foreach ($api->hooks()->all($user, $repo) as $hook) {
					if ($hook['name'] === 'web' && isset($hook['config']['url']) && $hook['config']['url'] === $hookUrl) {
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
					'events' => explode(',', $hookName),
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
		$client = $this->getClient();

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
					if ($hook['name'] === 'web' && isset($hook['config']['url']) && $hook['config']['url'] === $hookUrl) {
						$api->hooks()->remove($user, $repo, $hook['id']);
						break;
					}
				}
				$this->stdout("done.\n", Console::FG_GREEN, Console::BOLD);
			}
		}
	}

	/**
	 * @return Client
	 */
	private function getClient()
	{
		$token = null;
		if ($this->noAdmin) {
			$this->stdout("Please go to https://github.com/settings/tokens and generate a token.\n\n");
			$this->stdout("Required permissions: ");
			$this->stdout("admin:org, admin:repo_hook", Console::BOLD);
			$this->stdout("\n\n");

			$token = $this->prompt('Token? ');
		}

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client($token);

		return $client;
	}
}
