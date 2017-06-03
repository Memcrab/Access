<?php
declare (strict_types = 1);
require_once __DIR__ . "/../vendor/autoload.php";

use memCrab\Access\Access;
use memCrab\Exception\AccessException;
use memCrab\File\Yaml;

try {
	$Yaml = new Yaml();
	$rules = $Yaml->load("config/rules.yaml", null)->getContent();

	$Access = new Access();
	$Access->loadRules($rules);

	if (!$Access->checkRights("post", "save", "admin")) {
		throw AccessException("Access Denie.", 401);
	}

	// do all your work
} catch (AccessException $error) {
	$Response = new \YourResponseClass();
	$Response->setErrorResponse($error);
}

$Response->sendHeaders();
$Response->sendContent();