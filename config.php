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

        /**
         * Status: Need more info
         */
        [
			'label' => 'status:need more info',
			'comment' => <<<COMMENT
Thanks for posting in our issue tracker.
In order to properly assist you, we need additional information:

- When does the issue occur?
- What do you see?
- What was the expected result?
- Can you supply us with a stacktrace? (optional)
- Do you have exact code to reproduce it? Maybe a PHPUnit tests that fails? (optional)

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info`._
COMMENT
			,
        ],

        [
			'label' => 'expired',
			'comment' => <<<COMMENT
It has been 2 or more weeks with no response on our request for more information. 
In order for our issue tracker to be effective, we are closing this issue. 

If you want it to be reopened again, feel free to supply us with the requested information.

Thanks!

_This is an automated comment, triggered by adding the label `expired`._
COMMENT
			,
			'close' => true,
        ],

        /**
         * PRs: closed
         */
        [
            'label' => 'pr:too many objectives',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of the Yii framework. 
We have reviewed your pull request.

We came to the conclusion that this pull request in it's current form is covering too many different objectives.
The bigger it is, the more difficult it is to properly assess. 

Please divide your pull request into separate pull requests with clear objectives.

Thanks!

_This is an automated comment, triggered by adding the label `pr:too many objectives`._
COMMENT
            ,
            'close' => true,
        ],

        /**
         * PRs: kept open
         */
        [
            'label' => 'pr:request for unit tests',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of the Yii framework. 
We have reviewed your pull request.

In order for the framework and your solution to remain stable in the future, 
we have a unit test requirement in place. Therefore we can only accept your pull request if it is covered by unit tests. 

Could you add these please?

Thanks!

p.s. If you have any questions about the creation of unit tests? Don't hesitate to ask support.
[More information about unit tests](http://www.yiiframework.com/doc-2.0/guide-test-unit.html)

_This is an automated comment, triggered by adding the label `pr:request for unit tests`._
COMMENT
            ,
        ],



        [
            'label' => 'pr:missing usecase',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of the Yii framework. 
We have reviewed your pull request.

Unfortunately an usecase is missing. This is required to get a better understanding of the pull request.
It will help us to assess the necessity and applicability in the framework.

Could you supply us with a usecase please? Please be as detailed as possible and show some code!

Thanks!

_This is an automated comment, triggered by adding the label `pr:missing usecase`._
COMMENT
            ,
        ],


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
