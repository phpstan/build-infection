#!/usr/bin/env php
<?php declare(strict_types = 1);

$opts = getopt('', ['mutator-class:']);
if (!$opts) {
	echo "Usage: php ". $argv[0] ." --mutator-class='Infection\Mutator\Removal\MethodCallRemoval'\n";
	exit(1);
}

$decoded = json_decode(file_get_contents(__DIR__.'/../resources/infection.json5'));
foreach($opts['mutator-class'] as $mutatorclass) {
	$decoded->mutators->$mutatorclass = true;
}

echo json_encode($decoded);

exit(0);
