<?php


$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
;

return PhpCsFixer\Config::create()
    ->setRules([
        // Enables all PSR2 rules
        '@PSR2' => true,

        // Enables all Doctrine annotation rules
        // - enforces assignment operator to =
        // - enforces braces to always be used
        // - enforces correct indentation
        // - enforces correct spaces
        '@DoctrineAnnotation' => true,

        // Enforces short array syntax
        'array_syntax' => ['syntax' => 'short'],

        // Enforces blank lines to always be present around
        // 'break', 'continue', 'declare', 'return', 'throw', 'try'
        'blank_line_before_statement' => true,

        // Enforces braces around structures including
        // - enforcing braces position after anonymous classes/functions
        'braces' => [
            'position_after_anonymous_constructs' => 'same'
        ],

        // No spaces around a cast
        'cast_spaces' => ['space' => 'none'],

        // Seperates all class attributes by a blank line
        'class_attributes_separation' => true,

        // When using annotations, a comment block is converted to a phpdoc
        'comment_to_phpdoc' => true,

        // Enforces no space around the concat operator
        'concat_space' => true,

        // No spaces around declare equals
        'declare_equal_normalize' => [
            'space' => 'none'
        ],

        // Require strict type
        'declare_strict_types' => true,

        // Enforce = for array assignment in annotations
        'doctrine_annotation_array_assignment' => [
            'operator' => '=',
        ],

        // Enforce braces on all annotations
        'doctrine_annotation_braces' => [
            'ignored_tags' => ['abstract', 'access', 'code', 'const', 'deprec', 'encode', 'exception', 'final', 'ingroup', 'inheritdoc', 'inheritDoc', 'magic', 'name', 'toc', 'tutorial', 'private', 'static', 'staticvar', 'staticVar', 'throw', 'api', 'author', 'category', 'copyright', 'deprecated', 'example', 'filesource', 'global', 'ignore', 'internal', 'license', 'link', 'method', 'package', 'param', 'property', 'property-read', 'property-write', 'return', 'see', 'since', 'source', 'subpackage', 'throws', 'todo', 'TODO', 'usedBy', 'uses', 'var', 'version', 'after', 'afterClass', 'backupGlobals', 'backupStaticAttributes', 'before', 'beforeClass', 'codeCoverageIgnore', 'codeCoverageIgnoreStart', 'codeCoverageIgnoreEnd', 'covers', 'coversDefaultClass', 'coversNothing', 'dataProvider', 'depends', 'expectedException', 'expectedExceptionCode', 'expectedExceptionMessage', 'expectedExceptionMessageRegExp', 'group', 'large', 'medium', 'preserveGlobalState', 'requires', 'runTestsInSeparateProcesses', 'runInSeparateProcess', 'small', 'test', 'testdox', 'ticket', 'uses', 'SuppressWarnings', 'noinspection', 'package_version', 'enduml', 'startuml', 'fix', 'FIXME', 'fixme', 'override'],
            'syntax' => 'with_braces',
        ],

        // Enforces explicit variables
        'explicit_indirect_variable' => true,
        'explicit_string_variable' => true,

        // Enforce all classes are final or abstract
//        'final_class' => true,

        // Replaces some core function calls to statics
        'function_to_constant' => true,

        // Enforce space between function arguments and its typehints
        'function_typehint_space' => true,

        // Convert any heredocs to nowdocs when they have no variables
        'heredoc_to_nowdoc' => true,

        // Disallow is_null calls in favour of === null
        'is_null' => true,

        // Use short list syntax
        'list_syntax' => ['syntax' => 'short'],

        // Use logical operators &&/|| over and/or
        'logical_operators' => true,

        // Static references are lowercased
        'lowercase_static_reference' => true,

        // Magic methods/constants casing
        'magic_constant_casing' => true,
        'magic_method_casing' => true,

        // Enforce mb_ function usage when available
        'mb_str_functions' => true,

        // Method chains should be correctly indented
        'method_chaining_indentation' => true,

        // Replaces oldschool *val() methods with casts
        'modernize_types_casting' => true,

        // Strip newlines before a semicolon
        'multiline_whitespace_before_semicolons' => true,

        // Native function correctness
        'native_constant_invocation' => true,
        'native_function_casing' => true,
        'native_function_invocation' => true,

        // New calls must have braces
        'new_with_braces' => true,

        // No alternative control structures, use braces
        'no_alternative_syntax' => true,

        // Whitespace rules
        'no_blank_lines_after_class_opening' => true,
        'no_blank_lines_after_phpdoc' => true,

        // A '// no break' comment is required for case fallthrough
        'no_break_comment' => true,

        // No empty comments or statements
        'no_empty_comment' => true,
        'no_empty_phpdoc' => true,
        'no_empty_statement' => true,

        // Only allow a single line between things
        'no_extra_blank_lines' => true,

        // Ensure we only use ascii in code
        'no_homoglyph_names' => true,

        // Namespace rules
        'no_leading_import_slash' => true,
        'no_leading_namespace_whitespace' => true,

        // Properties should not be initialised to null
        'no_null_property_initialization' => true,

        'no_php4_constructor' => true,

        // No !! short bool cast
        'no_short_bool_cast' => true,

        // No whitespace before semicolons
        'no_singleline_whitespace_before_semicolons' => true,

        // List constructs should have no trailing comma
        'no_trailing_comma_in_list_call' => true,

        // Remove unnecessary things
        'no_unneeded_control_parentheses' => true,
        'no_unneeded_curly_braces' => true,
        'no_unneeded_final_method' => true,
        'no_unused_imports' => true,
        'no_useless_return' => true,
        'no_whitespace_before_comma_in_array' => true,
        'no_whitespace_in_blank_line' => true,
        'non_printable_character' => true,

        // Always use [] for array access
        'normalize_index_brace' => true,

        // No space before or after ->
        'object_operator_without_whitespace' => true,

        // Imports should be ordered
        'ordered_imports' => [
            'imports_order' => [
                'class',
                'const',
                'function',
            ],
        ],

        // PHPUnit specific fixes
        'php_unit_dedicate_assert' => [
            'target' => 'newest',
        ],
        'php_unit_fqcn_annotation' => true,
        'php_unit_test_case_static_method_calls' => ['call_type' => 'self'],
        'php_unit_test_class_requires_covers' => true,
        'php_unit_ordered_covers' => true,

        // Add a full stop to any summary sentence in a docblock
        'phpdoc_summary' => true,

        // Trim phpdocs of irrelevant whitespace
        'phpdoc_trim' => true,

        // Order all types with null last
        'phpdoc_types_order' => ['null_adjustment' => 'always_last'],

        // Move any protected things to private when possible
        'protected_to_private' => true,

        'psr4' => true,

        // Use mt_ rand functions
        'random_api_migration' => true,

        // Instructions must be terminated with a semicolon
        'semicolon_after_instruction' => true,

        // Use short cast types (boolean) -> (bool)
        'short_scalar_cast' => true,

        // Whitespace around namespaces
        'single_blank_line_before_namespace' => true,

        // Use single quotes where possible
        'single_quote' => true,

        // Enforce // instead of #
        'single_line_comment_style' => true,

        // Removes whitespace after a semicolon
        'space_after_semicolon' => true,

        // Make lambdas static when possible
        'static_lambda' => true,

        // ===
        'strict_comparison' => true,

        // Always use strict=true in functions that have a strict param
        'strict_param' => true,

        // Standard spacing around ternaries
        'ternary_operator_spaces' => true,

        // Use ?? where possible
        'ternary_to_null_coalescing' => true,

        // Add trailing commas
        'trailing_comma_in_multiline_array' => true,

        // Format arrays like function/method arguments
        'trim_array_spaces' => true,

        // Unary operators should be placed next to their operands
        'unary_operator_spaces' => true,

        // Add whitespace to single line arrays after ,
        'whitespace_after_comma_in_array' => true,

        // Yoda, we dont.
        'yoda_style' => false,
    ])
    ->setFinder($finder)
;
