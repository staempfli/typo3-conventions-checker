<?php
/******************************************************************
 * The csfixer rules are a copy of the core rules to be found
 * in https://github.com/TYPO3/TYPO3.CMS/blob/master/Build/.php_cs
 *****************************************************************/

/** @var \PhpCsFixer\Config $typo3CoreCsFixerConfiguration */
$typo3CoreCsFixerConfiguration = \PhpCsFixer\Config::create()
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'no_leading_import_slash' => true,
        'no_trailing_comma_in_singleline_array' => true,
        'no_singleline_whitespace_before_semicolons' => true,
        'no_unused_imports' => true,
        'concat_space' => ['spacing' => 'one'],
        'no_whitespace_in_blank_line' => true,
        'ordered_imports' => true,
        'single_quote' => true,
        'no_empty_statement' => true,
        'no_extra_consecutive_blank_lines' => true,
        'phpdoc_no_package' => true,
        'phpdoc_scalar' => true,
        'no_blank_lines_after_phpdoc' => true,
        'array_syntax' => ['syntax' => 'short'],
        'whitespace_after_comma_in_array' => true,
        'function_typehint_space' => true,
        'hash_to_slash_comment' => true,
        'no_alias_functions' => true,
        'lowercase_cast' => true,
        'no_leading_namespace_whitespace' => true,
        'native_function_casing' => true,
        'self_accessor' => true,
        'no_short_bool_cast' => true,
        'no_unneeded_control_parentheses' => true,
        'phpdoc_no_empty_return' => true,
        'no_blank_lines_after_phpdoc' => true,
        'phpdoc_trim' => true,
        'no_superfluous_elseif' => true,
        'no_useless_else' => true,
    ]);

// Skim through any file in 'typo3conf/ext' that may have been modified.
$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->in(getcwd());
$typo3CoreCsFixerConfiguration->setFinder($finder);

return $typo3CoreCsFixerConfiguration;