<?php

defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

require(__DIR__ . '/../vendor/autoload.php');
require(__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');

// config
$config = require(__DIR__ . '/../config.php');
if (is_file($localConfig = __DIR__ . '/../config.local.php')) {
	$config = array_merge($config, require($localConfig));
}


$application = new yii\web\Application([
	'id' => 'yii-web',
	'basePath' => dirname(__DIR__),
	'controllerNamespace' => 'app\controllers',
	'params' => $config,
	'vendorPath' => dirname(__DIR__) . '/vendor',
	'runtimePath' => dirname(__DIR__) . '/tmp',

	'bootstrap' => ['log'],
	'components' => [
		'github' => 'app\components\Github',

		'request' => [
			'enableCsrfValidation' => false,
			'enableCookieValidation' => false,
			'parsers' => [
				'application/json' => 'yii\web\JsonParser',
			],
		],

		'cache' => [
			'class' => 'yii\caching\FileCache',
		],

		'errorHandler' => [
			'errorAction' => 'site/error',
		],

		'mailer' => [
			'class' => 'yii\swiftmailer\Mailer',
			// send all mails to a file by default. You have to set
			// 'useFileTransport' to false and configure a transport
			// for the mailer to send real emails.
			'useFileTransport' => false,
		],

		'log' => [
			'traceLevel' => YII_DEBUG ? 3 : 0,
			'targets' => [
				[
					'class' => 'yii\log\FileTarget',
					'logFile' => '@app/logs/access.log',
					'categories' => ['request'],
					'logVars' => [],
					'enableRotation' => false,
				],
				[
					'class' => 'yii\log\FileTarget',
					'logFile' => '@app/logs/access-full.log',
					'categories' => ['request'],
					'logVars' => ['_GET', '_POST'],
				],
				[
					'class' => 'yii\log\FileTarget',
					'logFile' => '@app/logs/actions.log',
					'categories' => ['action'],
					'logVars' => [],
					'enableRotation' => false,
				],
				[
					'class' => 'yii\log\FileTarget',
					'logFile' => '@app/logs/error.log',
					'levels' => ['error', 'warning'],
					'except' => ['yii\web\HttpException:404', 'action'],
				],
				[
					'class' => 'yii\log\EmailTarget',
					'levels' => ['error'],
					'except' => ['yii\web\HttpException:404', 'action'],
					'message' => [
						'from' => ['bot@cebe.cc'],
						'to' => ['hostmaster+yiibot@cebe.cc'],
						'subject' => 'Yii-bot error',
					],
				],
			],
		],
	],

	'on beforeAction' => function($event) {
		Yii::info('Request ' . Yii::$app->request->method . ' r"' . Yii::$app->requestedRoute . '" from ' . Yii::$app->request->userIP . ', UserAgent: ' . Yii::$app->request->userAgent, 'request');
		if (!empty(Yii::$app->request->bodyParams)) {
			Yii::info(Yii::$app->request->bodyParams, 'request');
		}
	}

]);

$application->run();
