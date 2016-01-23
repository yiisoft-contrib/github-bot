<?php

return [

	// SET THIS IN config.local.php
	// needs the following permissions:
	// - repo
	// - admin:repo_hook
	// - admin:org_hook
	'github_token' => '',

	// TODO implement
	'hook_secret' => '',

	'repositories' => [
		'cebe-test/testrepo',
		'cebe-test/testrepo-redis',
	],

	// comment on issues that are labeled with a specific label
	// - label: on which label to react
	// - comment: what to post
	// - close: whether to close the issue after commenting
	'comment' => [
		[
			'label' => 'forum',
			'comment' => 'Hi, github is for bug tracking, if you need help, use the forum or other resources.',
			'close' => true,
		],
	],

	// move issues to other repos based on labels
	// - label: on which label to react
	// - repo: the repo to move the issue to
	'moveissue' => [
		[
			'label' => 'feature:redis',
			'repo' => 'cebe/testrepo-redis',
		],

	],



];
