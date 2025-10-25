#!/usr/bin/env php
<?php declare(strict_types = 1);

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 'stderr');

$opts = getopt('', ['source-directory::', 'mutator-class::']);
if ($argc < 1) {
	echo "Usage: php ". $argv[0] ." [--source-directory='another/path'] [--mutator-class='Infection\Mutator\Removal\MethodCallRemoval]'\n";
	exit(1);
}
$addSourceDirectories = (array) ($opts['source-directory'] ?? []);
$addMutatorClasses = (array) ($opts['mutator-class'] ?? []);

$decoded = json_decode(file_get_contents(__DIR__.'/../resources/infection.json5'));
foreach($addSourceDirectories as $path) {
	$decoded->source->directories[] = $path;
}
foreach($addMutatorClasses as $mutatorclass) {
	$decoded->mutators->$mutatorclass = true;
}

echo json_encode($decoded);

exit(0);
