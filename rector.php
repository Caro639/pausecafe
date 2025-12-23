<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;

return RectorConfig::configure()
    // Register single rule
    ->withRules([
        TypedPropertyFromStrictConstructorRector::class
    ])
    // Here we can define what prepared sets of rules will be applied
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true
    )
    // Define paths to be processed
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    // Skip files/directories
    ->withSkip([
        __DIR__ . '/src/Kernel.php',
    ]);