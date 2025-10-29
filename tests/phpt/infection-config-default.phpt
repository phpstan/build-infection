--TEST--
infection-config.php renders proper json defaults
--FILE--
<?php declare(strict_types=1);
$bin = PHP_BINARY . ' '. __DIR__.'/../../bin/infection-config.php';
echo shell_exec($bin ."| jq");
--EXPECT--
{
  "$schema": "vendor/infection/infection/resources/schema.json",
  "timeout": 30,
  "source": {
    "directories": [
      "src"
    ]
  },
  "staticAnalysisTool": "phpstan",
  "logs": {
    "text": "tmp/infection.log"
  },
  "mutators": {
    "@default": false,
    "PHPStan\\Infection\\TrinaryLogicMutator": true
  },
  "bootstrap": "build-infection/vendor/autoload.php"
}
