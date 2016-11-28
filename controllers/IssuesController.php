<?php

namespace app\controllers;

use Yii;
use yii\base\InvalidConfigException;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

/**
 * IssuesController handles github webhook requests which have been registered.
 *
 * Dependent on the events certain actions are triggered which are configured in the configuration file.
 *
 * @author Carsten Brandt <mail@cebe.cc>
 */
class IssuesController extends Controller
{
	/**
	 * @see https://developer.github.com/v3/activity/events/types/#issuesevent
	 */
	const EVENTNAME_ISSUES = 'issues';
	/**
	 * @see https://developer.github.com/v3/activity/events/types/#pullrequestevent
	 */
	const EVENTNAME_PULL_REQUEST = 'pull_request';


	public function behaviors()
	{
		return [
			'verb' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'index' => ['post'],
				],
			],
		];
	}

	public function actionIndex()
	{
		\Yii::$app->response->format = Response::FORMAT_JSON;

		// content of $params should look like here: https://developer.github.com/v3/activity/events/types/#issuesevent
		$params = \Yii::$app->request->bodyParams;
		$event = \Yii::$app->request->headers->get('X-Github-Event');
		if (!$event) {
			\Yii::warning('event request without X-Github-Event header.');
			throw new BadRequestHttpException('Event request without X-Github-Event header.');
		}

		// verify request data to avoid data sent from sources other than github
		// this will throw BadRequestHttpException on invalid data
		Yii::$app->github->verifyRequest(Yii::$app->request->rawBody);

		// simple succes for 'ping' event
		if ($event === 'ping') {
			return ['success' => true, 'action' => 'pong'];
		}

		// only react on issue and pull request events
		if ($event !== self::EVENTNAME_ISSUES && $event !== self::EVENTNAME_PULL_REQUEST) {
			throw new BadRequestHttpException('Only issues and pull_request events should be deployed here.');
		}

		// ignore events triggered by the bot itself to avoid loops
		if ($params['sender']['login'] === Yii::$app->params['github_username']) {
			\Yii::warning('ignoring event triggered by myself.');
			return ['success' => true, 'action' => 'ignored'];
		}

		// dependent on the event perform some action
		switch($params['action'])
		{
			case 'labeled':
				// if label is added, check for actions

				if (isset($params['label'])) {
					foreach(\Yii::$app->params['actions'] as $action) {
						if ($params['label']['name'] == $action['label']) {
							$this->performActionByLabel($action, $params, $event);
						}
					}
				}

				return ['success' => true, 'action' => 'processed'];
				break;
		}

		return ['success' => true, 'action' => 'ignored'];
	}

	protected function performActionByLabel($action, $params, $event)
	{
		switch($action['action'])
		{
			case 'comment':
				// add a comment to issue or pull request
				if ($event === self::EVENTNAME_ISSUES) {
					$this->replyWithCommentToIssue($params['repository'], $params['issue'], $action['comment']);
					if (isset($action['close']) && $action['close']) {
						$this->closeIssue($params['repository'], $params['issue']);
					}
				} elseif($event === self::EVENTNAME_PULL_REQUEST) {
					$this->replyWithCommentToPr($params['repository'], $params['pull_request'], $action['comment']);
					if (isset($action['close']) && $action['close']) {
						$this->closePr($params['repository'], $params['pull_request']);
					}
				}
				break;
			case 'move':
				// move an issue to another repository
				if ($event === self::EVENTNAME_ISSUES) {
					if ($params['issue']['state'] !== 'open') {
						// do not move issue if it is closed, allow editing labels in closed state
						break;
					}
					$this->moveIssue($params['repository'], $action['repo'], $params['issue'], $params['sender']);
				}
				break;
			default:
				throw new InvalidConfigException('Action "' . $action['action'] . '" is not supported.');
		}
	}

	protected function replyWithCommentToIssue($repository, $issue, $comment)
	{
		sleep(2); // wait 2sec before reply to have github issue events in order

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$api = new \Github\Api\Issue($client);
		$api->comments()->create($repository['owner']['login'], $repository['name'], $issue['number'], [
			'body' => $comment,
		]);
		Yii::info("commented on issue {$repository['owner']['login']}/{$repository['name']}#{$issue['number']}.", 'action');
	}

	protected function closeIssue($repository, $issue)
	{
		sleep(2); // wait 2sec before reply to have github issue events in order

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$api = new \Github\Api\Issue($client);
		$api->update($repository['owner']['login'], $repository['name'], $issue['number'], [
			'state' => 'closed',
		]);
		Yii::info("closed issue {$repository['owner']['login']}/{$repository['name']}#{$issue['number']}.", 'action');
	}

	protected function replyWithCommentToPr($repository, $pr, $comment)
	{
		sleep(2); // wait 2sec before reply to have github issue events in order

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		// create issue instead of PR to be able to post on the normal PR wall
		// otherwise the comment must be on a file or commit
		$api = new \Github\Api\Issue($client);
		$api->comments()->create($repository['owner']['login'], $repository['name'], $pr['number'], [
			'body' => $comment,
		]);
		Yii::info("commented on pr {$repository['owner']['login']}/{$repository['name']}#{$pr['number']}.", 'action');
	}

	protected function closePr($repository, $pr)
	{
		sleep(2); // wait 2sec before reply to have github issue events in order

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$api = new \Github\Api\PullRequest($client);
		$api->update($repository['owner']['login'], $repository['name'], $pr['number'], [
			'state' => 'closed',
		]);
		Yii::info("closed pr {$repository['owner']['login']}/{$repository['name']}#{$pr['number']}.", 'action');
	}

	protected function moveIssue($fromRepository, $toRepository, $issue, $sender)
	{
		// do not move issue if from and to repo are the same (prevent loops)
		if ("{$fromRepository['owner']['login']}/{$fromRepository['name']}" === $toRepository) {
			Yii::warning("did NOT move issue {$fromRepository['owner']['login']}/{$fromRepository['name']}#{$issue['number']} to {$toRepository}.", 'action');
			return;
		}
		// also do not move issues created by the bot itself (prevent loops)
		if ($issue['user']['login'] === Yii::$app->params['github_username']) {
			Yii::warning("did NOT move issue {$fromRepository['owner']['login']}/{$fromRepository['name']}#{$issue['number']} to {$toRepository} because it was created by me.", 'action');
			return;
		}

		sleep(2); // wait 2sec before reply to have github issue events in order

		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$api = new \Github\Api\Issue($client);
		list($toUser, $toRepo) = explode('/', $toRepository);
		$newIssue = $api->create($toUser, $toRepo, [
			'title' => $issue['title'],
			'body' => 'This issue has originally been reported by @' . $issue['user']['login'] . ' at ' . $issue['html_url'] . ".\n"
				. 'Moved here by @' . $sender['login'] . '.'
				. "\n\n-----\n\n"
				. $issue['body'],
			'labels' => array_map(function($i) { return $i['name']; }, $issue['labels']),
		]);
		Yii::info("moved issue {$fromRepository['owner']['login']}/{$fromRepository['name']}#{$issue['number']} to {$toRepository}#{$newIssue['number']}.", 'action');
		sleep(2); // wait 2sec before reply to have github issue events in order
		$this->replyWithCommentToIssue($fromRepository, $issue, 'Issue moved to ' . $newIssue['html_url']);
		sleep(2); // wait 2sec before reply to have github issue events in order
		$this->closeIssue($fromRepository, $issue);
	}
}
