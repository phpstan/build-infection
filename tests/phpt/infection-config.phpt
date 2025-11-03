--TEST--
infection-config.php renders proper json
--FILE--
<?php declare(strict_types=1);
$bin = PHP_BINARY . ' '. __DIR__.'/../../bin/infection-config.php';
echo shell_exec($bin." --source-directory='more/files/' --timeout=180 --mutator-class='My\Class'");
--EXPECT--
{
    "$schema": "vendor\/infection\/infection\/resources\/schema.json",
    "timeout": 180,
    "source": {
        "directories": [
            "src",
            "more\/files\/"
        ]
    },
    "staticAnalysisTool": "phpstan",
    "logs": {
        "text": "tmp\/infection.log"
    },
    "mutators": {
        "@default": false,
        "PHPStan\\Infection\\LooseBooleanMutator": true,
        "PHPStan\\Infection\\TrinaryLogicMutator": true,
        "My\\Class": true
    },
    "bootstrap": "build-infection\/vendor\/autoload.php"
}
