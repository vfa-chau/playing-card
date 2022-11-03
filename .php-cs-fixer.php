<?php


$finder = PhpCsFixer\Finder::create()
    ->exclude('node_modules')
    ->exclude('storage')
    ->exclude('bootstrap')
    ->in(__DIR__);

$config = new PhpCsFixer\Config();
return $config->setRules(array(
        '@PSR2' => true,
        'array_syntax' => array('syntax' => 'short'),
    ))
    ->setFinder($finder);
