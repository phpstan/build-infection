#!/usr/bin/env php
<?php declare(strict_types = 1);

// Usage: php infection-config.php [--source-directory='another/path'] [--mutator-class='Infection\Mutator\Removal\MethodCallRemoval'] [--timeout=60]

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 'stderr');

$opts = getopt('', ['source-directory::', 'mutator-class::', 'timeout::']);
$addSourceDirectories = (array) ($opts['source-directory'] ?? []);
$addMutatorClasses = (array) ($opts['mutator-class'] ?? []);
$timeout = $opts['timeout'] ?? null;

$defaults = file_get_contents(__DIR__.'/../resources/infection.json5');
if ($defaults === false) {
	throw new RuntimeException('Unable to read infection.json5');
}
$decoded = json_decode($defaults);

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
