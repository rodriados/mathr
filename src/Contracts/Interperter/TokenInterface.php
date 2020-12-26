<?php
/**
 * The basics needed for a token.
 * @package Mathr\Contracts\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Interperter;

/**
 * Represents a token extracted from a parsing execution.
 * @package Mathr\Contracts\Interperter
 */
interface TokenInterface
{
    /**
     * Returns the token data, extracted from the expression string.
     * @return string The token's data as a string.
     */
    public function getData(): string;

    /**
     * Returns the token's position.
     * @return int The token's position on the string.
     */
    public function getPosition(): int;

    /**
     * Returns the token's type.
     * @param int $mask The mask to apply to token type.
     * @return int The token's type.
     */
    public function getType(int $mask = ~0): int;

    /**
     * Checks whether the token matches the requested flag.
     * @param int $flag The flag to check the token against.
     * @return bool Has the token matched the flag?
     */
    public function isOf(int $flag): bool;
}
