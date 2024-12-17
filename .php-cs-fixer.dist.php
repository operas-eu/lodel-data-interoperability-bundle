<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude(['config/secrets', 'var'])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        'date_time_immutable' => true,
        'declare_strict_types' => true,
        'strict_comparison' => true,
        'phpdoc_to_comment' => false,
        'return_assignment' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;
