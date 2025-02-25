<?php
declare(strict_types=1);

namespace PHP_CodeSniffer\Files;

/**
 * @see vendor/squizlabs/php_codesniffer/src/Files/File.php
 */
class File
{
    /**
     * @return array{
     *     is_readonly: bool,
     *     is_static: bool,
     *     nullable_type: bool,
     *     scope: string,
     *     scope_specified: bool,
     *     type: string,
     *     type_end_token: int|false,
     *     type_token: int|false,
     * }
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException
     */
    public function getMemberProperties(int $stackPtr): array
    {
        return [];
    }

    /**
     * @return array< array-key, array{
     *     comma_token: int|false,
     *     content: string,
     *     default?: string,
     *     default_equal_token?: int,
     *     default_token?: int,
     *     has_attributes: bool,
     *     name: string,
     *     nullable_type: bool,
     *     pass_by_reference: bool,
     *     property_readonly?: bool,
     *     property_visibility?: string,
     *     readonly_token?: int,
     *     reference_token: int|false,
     *     token: int,
     *     type_hint: string,
     *     type_hint_end_token: int|false,
     *     type_hint_token: int|false,
     *     variable_length: bool,
     *     variadic_token: int|false,
     *     visibility_token?: int|false,
     * }>
     *
     * @throws \PHP_CodeSniffer\Exceptions\RuntimeException
     */
    public function getMethodParameters(int $stackPtr): array
    {
        return [];
    }

    /**
     * @return array<int, array{
     *     attribute_closer: int,
     *     bracket_closer: int|null,
     *     code: int|string,
     *     column: int,
     *     comment_closer: int|null,
     *     content: string,
     *     length: int,
     *     line: int,
     *     parenthesis_closer: int|null,
     *     scope_closer: int,
     *     scope_opener: int,
     *     type: string,
     * }>
     */
    public function getTokens(): array
    {
        return [];
    }
}
