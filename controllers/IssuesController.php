<?php
/**
 * 
 * 
 * @author Carsten Brandt <mail@cebe.cc>
 */

namespace app\controllers;


use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\Response;

class IssuesController extends Controller
{
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

		if ($event === 'ping') {
			return ['success' => true, 'action' => 'pong'];
		}

		if ($event !== 'issues') {
			throw new BadRequestHttpException('Only issues events should be deployed here.');
		}

		switch($params['action'])
		{
			case 'labeled':
				// if label is added, check for actions

				if (isset($params['label'])) {
					foreach(\Yii::$app->params['actions'] as $action) {
						if ($params['label']['name'] == $action['label']) {
							$this->performAction($action, $params);
						}
					}
				}

				return ['success' => true, 'action' => 'processed'];
				break;
		}

		return ['success' => true, 'action' => 'ignored'];
	}

	protected function performAction($action, $params)
	{
		switch($action['action'])
		{
			case 'comment':
				$this->replyWithComment($params['repository'], $params['issue'], $action['comment']);
				if ($action['close']) {
					$this->closeIssue($params['repository'], $params['issue']);
				}
				break;
		}

	}

	protected function replyWithComment($repository, $issue, $comment)
	{
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
		/** @var $client \Github\Client */
		$client = Yii::$app->github->client();

		$api = new \Github\Api\Issue($client);
		$api->update($repository['owner']['login'], $repository['name'], $issue['number'], [
			'state' => 'closed',
		]);
		Yii::info("closed issue {$repository['owner']['login']}/{$repository['name']}#{$issue['number']}.", 'action');
	}

}