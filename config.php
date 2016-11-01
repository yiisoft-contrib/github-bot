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
         * Status: Need more info
         */
        [
			'label' => 'status:need more info',
			'comment' => <<<COMMENT
Thanks for posting your issue in our issue tracker. 
In order to properly assist you, we need more information. 

Can you supply us with answers to the following questions:
* When does the issue occur?
* What do you see?
* What was the expected result?
* (optional) Can you supply us with a stacktrace?
* (optional) How could a unit test look like proving your issue?

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info`._
COMMENT
			,
        ],

        [
			'label' => 'status:need more info (expired)',
			'comment' => <<<COMMENT
It has been 2 or more weeks with no response on our request for more information. In order for our issue tracker
to be effective, we are closing this issue. 

If you want it to be reopened again, feel free to supply us with the requested information.

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
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
Thank you for putting effort in the improvement of our framework. 
We have reviewed your PR.

We came to the conclusion that this PR in it's current form is covering too many different objectives.
The bigger a PR is, the more difficult it is to properly assess. 

Please divide your PR in separate PR's with clear objectives.

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
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
Thank you for putting effort in the improvement of our framework. We have reviewed your PR.

In order for the framework and your solution to remain stable in the future, we have a 
unit test requirement in place. Therefore we can only accept your  PR if it is covered by unit tests. 

Could you add these please?

Thanks!

ps. If you have any questions about the creation of unit tests? Don't hesitate to ask support.
More information about [unit tests](http://www.yiiframework.com/doc-2.0/guide-test-unit.html)

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
COMMENT
            ,
        ],

        [
            'label' => 'pr:loss performance',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of our framework. 
We have reviewed your PR.

We came to the conclusion that this PR in current form is going too much at cost of the performance
of the framework. Speed is an important core value for us. 

Please update your PR so that the performance of the framework is not compromised.

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
COMMENT
            ,
        ],

        [
            'label' => 'pr:missing usecase',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of our framework. 
We have reviewed your PR.

Unfortunately a usecase is missing. This is required to get a better understanding of the PR.
It will help us to assess the necessity and applicability in the framework.

Could you supply us with a usecase please? 

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
COMMENT
            ,
        ],

        [
            'label' => 'pr:request for 1 commit',
            'comment' => <<<COMMENT
Thank you for putting effort in the improvement of our framework. 
We have reviewed your PR.

We have noticed that this PR contains multiple commits. In order to keep our GIT history clean,
we are asking you to reset your commits to 1 commit. This can be done as follows:
 * soft reset your branch to the last commit of the base branch
 * recommit all your changes
 * execute a force push to your github fork

Thanks!

_This is an automated comment, triggered by adding the label `status:need more info (expired)`._
COMMENT
            ,
        ],

        [
			'action' => 'comment',
			'label' => 'question',
			'comment' => <<<COMMENT
_This is an automated comment, triggered by adding the label `question`._
			
Please note, that the GitHub Issue Tracker is for bug reports and feature requests only.

We are happy to help you on the [support forum](http://www.yiiframework.com/forum/), on [IRC](http://www.yiiframework.com/chat/) (#yii on freenode), or [Gitter](https://gitter.im/yiisoft/yii2).

Please use one of the above mentioned resources to discuss the problem.
If the result of the discussion turns out that there really is a bug in the framework, feel free to
come back and provide information on how to reproduce the issue. This issue will be closed for now.
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
