<?php

if (empty($_POST)) {
	exit;
}

$log = print_r($_POST, true);

file_put_contents(__DIR__ . '/../requests.log', $log, FILE_APPEND | LOCK_EX);
