<?php

// composer autoload
require_once(__DIR__ . '/vendor/autoload.php');

// config
$config = require(__DIR__ . '/config.php');
if (is_file($localConfig = __DIR__ . '/config.local.php')) {
	$config = array_merge($config, require($localConfig));
}

// create client
$client = new \Github\HttpClient\CachedHttpClient();
$client->setCache(new \Github\HttpClient\Cache\FilesystemCache(__DIR__ . '/tmp/cache'));
$client = new \Github\Client($client);

// authenticate
$client->authenticate($config['github_token'], '', Github\Client::AUTH_HTTP_TOKEN);


