<?php
/******************************************************************
 * The csfixer configuration inherits the TYPO3 Core
 * configuration, but overrides the 'finder' to fix
 * everything in "typo3conf/ext"
 *****************************************************************/

/** @var \PhpCsFixer\Config $typo3CoreCsFixerConfiguration */
$typo3CoreCsFixerConfiguration = include realpath(getcwd() . "/vendor/typo3/cms/Build/.php_cs");

$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->in(getcwd() . '/web/typo3conf/ext/');
$typo3CoreCsFixerConfiguration->setFinder($finder);

return $typo3CoreCsFixerConfiguration;