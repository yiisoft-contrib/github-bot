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
		[
			'action' => 'comment',
			'label' => 'question',
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

		[
			'action' => 'move',
			'label' => 'ext:apidoc',
			'repo' => 'yiisoft/yii2-apidoc',
		],

		[
			'action' => 'move',
			'label' => 'ext:authclient',
			'repo' => 'yiisoft/yii2-authclient',
		],

		[
			'action' => 'move',
			'label' => 'ext:bootstrap',
			'repo' => 'yiisoft/yii2-bootstrap',
		],

		[
			'action' => 'move',
			'label' => 'ext:codeception',
			'repo' => 'yiisoft/yii2-codeception',
		],

		[
			'action' => 'move',
			'label' => 'ext:debug',
			'repo' => 'yiisoft/yii2-debug',
		],

		[
			'action' => 'move',
			'label' => 'ext:elasticsearch',
			'repo' => 'yiisoft/yii2-elasticsearch',
		],

		[
			'action' => 'move',
			'label' => 'ext:faker',
			'repo' => 'yiisoft/yii2-faker',
		],

		[
			'action' => 'move',
			'label' => 'ext:gii',
			'repo' => 'yiisoft/yii2-gii',
		],

		[
			'action' => 'move',
			'label' => 'ext:imagine',
			'repo' => 'yiisoft/yii2-imagine',
		],

		[
			'action' => 'move',
			'label' => 'ext:jui',
			'repo' => 'yiisoft/yii2-jui',
		],

		[
			'action' => 'move',
			'label' => 'ext:mongodb',
			'repo' => 'yiisoft/yii2-mongodb',
		],

		[
			'action' => 'move',
			'label' => 'ext:redis',
			'repo' => 'yiisoft/yii2-redis',
		],

		[
			'action' => 'move',
			'label' => 'ext:sphinx',
			'repo' => 'yiisoft/yii2-sphinx',
		],

		[
			'action' => 'move',
			'label' => 'ext:swiftmailer',
			'repo' => 'yiisoft/yii2-swiftmailer',
		],

		[
			'action' => 'move',
			'label' => 'ext:twig',
			'repo' => 'yiisoft/yii2-twig',
		],

		[
			'action' => 'move',
			'label' => 'ext:smarty',
			'repo' => 'yiisoft/yii2-smarty',
		],

	],



];
