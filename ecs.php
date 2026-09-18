<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Import\NoUnusedImportsFixer;
use PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer;
use PhpCsFixer\Fixer\Strict\StrictComparisonFixer;
use PhpCsFixer\Fixer\Strict\StrictParamFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/assets',
        __DIR__ . '/config',
        __DIR__ . '/migrations',
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withRootFiles()

    // add a single rule
    ->withRules([
        NoUnusedImportsFixer::class,
        DeclareStrictTypesFixer::class,
        StrictComparisonFixer::class,
        StrictParamFixer::class,
    ])
    ->withPreparedSets(
        psr12: true,
        spaces: true,
        namespaces: true,
        docblocks: true,
        arrays: true,
        comments: true,
    )
    ->withPhpCsFixerSets(
        perCS20: true,
        doctrineAnnotation: true,
    )
;
