<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->files()
    ->name('*.php')
    ->in(__DIR__ . '/src')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL)
    ->fixers([
        'psr0',
        'encoding',
        'short_tag',
        'braces',
        'elseif',
        'eof_ending',
        'function_call_space',
        'function_declaration',
        'indentation',
        'line_after_namespace',
        'linefeed',
        'lowercase_constants',
        'lowercase_keywords',
        'method_argument_space',
        'multiple_use',
        'parenthesis',
        'php_closing_tag',
        'single_line_after_imports',
        'trailing_spaces',
        'visibility',
        '-phpdoc_to_comment',
        '-phpdoc_var_without_name',
        '-unalign_double_arrow',
        '-unalign_equals',
        '-concat_without_spaces',
        '-pre_increment',
        '-empty_return',
        '-single_blank_line_before_namespace',
        'concat_with_spaces',
        'header_comment',
        'newline_after_open_tag',
        'ordered_use',
        'phpdoc_order',
        'short_array_syntax',
        'short_echo_tag',
    ])
    ->setUsingCache(true)
    ->finder($finder)
;
