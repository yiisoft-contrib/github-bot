<?php

require_once __DIR__ . '/init.php';

// create hooks:
foreach($config['repositories'] as $urepo) {
	echo "registering issues hook on $urepo...";
	list($user, $repo) = explode('/', $urepo);
	// https://developer.github.com/v3/repos/hooks/#create-a-hook
	$client->api('repo')->hooks()->create($user, $repo, [
		'name' => 'web',
		'config' => [
			'url' => 'http://bot.cebe.cc/issue_hook.php',
			//'secret' =>  // TODO
		],
		'events' => ['issues'],
		'active' => true,

		
	]);
	echo "done.\n";
}




