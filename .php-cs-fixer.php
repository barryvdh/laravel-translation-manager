<?php

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@Symfony' => true,
    '@PHP71Migration' => true,
    'array_push' => true,
    'ordered_imports' => [
        'imports_order' => [
            'class', 'function', 'const',
        ],
        'sort_algorithm' => 'length',
    ],
    'no_leading_import_slash' => true,
    'return_assignment' => true,
    'phpdoc_no_empty_return' => true,
    'no_blank_lines_after_phpdoc' => true,
    'general_phpdoc_tag_rename' => true,
    'phpdoc_inline_tag_normalizer' => true,

    'combine_consecutive_issets' => true,
    'combine_consecutive_unsets' => true,
    'no_useless_else' => true,
    'lowercase_keywords' => true,
    'modernize_types_casting' => true,
    'no_short_bool_cast' => true,
    'no_php4_constructor' => true,
    'php_unit_construct' => [
        'assertions' => ['assertSame', 'assertEquals', 'assertNotEquals', 'assertNotSame'],
    ],
];

$finder = Finder::create()
    ->in([
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/src',
        __DIR__.'/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

$config = new Config();

return $config->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
