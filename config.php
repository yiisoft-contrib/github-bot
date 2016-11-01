<?php

return [

	// the url that points to the web/ folder
	'webUrl' => 'http://bot.cebe.cc/',

	// SET THIS IN config.local.php
	// needs the following permissions:
	// - repo
	// - admin:repo_hook
	'github_token' => '',
	'github_username' => '',

	// a secret to verify hooks are really coming from github
	'hook_secret' => '',

	// the repositories to install hooks for
	'repositories' => [
		'cebe-test/testrepo',
		'cebe-test/testrepo-redis',
	],

	// comment on issues that are labeled with a specific label
	// - action: "comment"
	// - label: on which label to react
	// - comment: what to post
	// - close: whether to close the issue after commenting

	// move issues to other repos based on labels
	// - action: "move"
	// - label: on which label to react
	// - repo: the repo to move the issue to

	'actions' => [

		/**
		 * Questions
		 */
		[
			'label' => 'question',
			'action' => 'comment',
			'comment' => <<<COMMENT
Thank you for your question.
In order for this issue tracker to be effective, it should only contain bug reports and feature requests.

We advise you to use our other community driven resources:
* [The support forum](http://www.yiiframework.com/forum/)
* [The IRC chat room](http://www.yiiframework.com/chat/)
* [The Gitter chat room](https://gitter.im/yiisoft/yii2)
* [The Definitive Guide to Yii](http://www.yiiframework.com/doc-2.0/guide-index.html)

If you are confident that there is a bug in the framework, feel free to
provide information on how to reproduce it. This issue will be closed for now.

_This is an automated comment, triggered by adding the label `question`._
COMMENT
,
			'close' => true,
		],

		/**
		 * Extensions
		 */
		[
			'label' => 'ext:apidoc',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-apidoc',
		],

		[
			'label' => 'ext:authclient',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-authclient',
		],

		[
			'label' => 'ext:bootstrap',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-bootstrap',
		],

		[
			'label' => 'ext:codeception',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-codeception',
		],

		[
			'label' => 'ext:debug',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-debug',
		],

		[
			'label' => 'ext:elasticsearch',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-elasticsearch',
		],

		[
			'label' => 'ext:faker',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-faker',
		],

		[
			'label' => 'ext:gii',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-gii',
		],

		[
			'label' => 'ext:imagine',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-imagine',
		],

		[
			'label' => 'ext:jui',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-jui',
		],

		[
			'label' => 'ext:mongodb',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-mongodb',
		],

		[
			'label' => 'ext:redis',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-redis',
		],

		[
			'label' => 'ext:sphinx',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-sphinx',
		],

		[
			'label' => 'ext:swiftmailer',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-swiftmailer',
		],

		[
			'label' => 'ext:twig',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-twig',
		],

		[
			'label' => 'ext:smarty',
			'action' => 'move',
			'repo' => 'yiisoft/yii2-smarty',
		],

	],



];
