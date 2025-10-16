<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'example',
        'node_modules',
        'public',
        'resources',
        'vendor',
        'webpack',
    ]);

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRules([
        '@PSR12' => true,
        'array_indentation' => true,
        'array_syntax' => true,
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,
        'method_chaining_indentation' => true,
        'no_unused_imports' => true,
        'ordered_imports' => true,
        'phpdoc_line_span' => true,
        'single_quote' => true,
        'trailing_comma_in_multiline' => true,
    ])
    ->setFinder($finder);
