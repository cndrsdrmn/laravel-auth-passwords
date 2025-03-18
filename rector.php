<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Strict\Rector\Ternary\DisallowedShortTernaryRuleFixerRector;

return RectorConfig::configure()
    ->withSkip([
        DisallowedShortTernaryRuleFixerRector::class,
    ])
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        strictBooleans: true,
    )
    ->withPhpSets();
