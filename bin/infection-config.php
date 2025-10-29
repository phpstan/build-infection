#!/usr/bin/env php
<?php declare(strict_types = 1);

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 'stderr');

$opts = getopt('', ['source-directory::', 'mutator-class::', 'timeout::']);
if ($argc < 1) {
	echo "Usage: php ". $argv[0] ." [--source-directory='another/path'] [--mutator-class='Infection\Mutator\Removal\MethodCallRemoval] [--timeout=60]'\n";
	exit(1);
}
$addSourceDirectories = (array) ($opts['source-directory'] ?? []);
$addMutatorClasses = (array) ($opts['mutator-class'] ?? []);
$timeout = $opts['timeout'] ?? null;

$decoded = json_decode(file_get_contents(__DIR__.'/../resources/infection.json5'));
foreach($addSourceDirectories as $path) {
	$decoded->source->directories[] = $path;
}
foreach($addMutatorClasses as $mutatorclass) {
	$decoded->mutators->$mutatorclass = true;
}

if ($timeout !== null) {
	$decoded->timeout = (int) $timeout;
}

echo json_encode($decoded);

exit(0);
