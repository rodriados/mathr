<?php
/**
 * Expression token representation.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

/**
 * The token extracted by a tokenizer.
 * @package Mathr\Parser
 */
class Token
{
    /**#@+
     * The token type constants.
     * These flags can be joined with flags of different types.
     */
    const NUMBER      = 0x0001;
    const IDENTIFIER  = 0x0002;
    const OPERATOR    = 0x0004;
    const FUNCTION    = 0x0008;
    const PARENTHESES = 0x0010;
    const BRACKETS    = 0x0020;
    const CURLY       = 0x0040;
    const COMMA       = 0x0080;
    const UNKNOWN     = 0x8000;
    /**#@-*/

    /**#@+
     * The token associativity constants.
     * These flags inform whether a token has a specific associativity.
     */
    const RIGHT       = 0x0100;
    const LEFT        = 0x0200;
    /**#@-*/

    /**
     * Token constructor.
     * @param string $data The token's detection string.
     * @param int $position The token's position on expression.
     * @param int $type The type of the parsed token.
     */
    public function __construct(
        private string $data,
        private int $position = 0,
        private int $type = self::UNKNOWN,
    ) {}

    /**
     * Returns a string representation from the token.
     * @return string The token represented by a string.
     */
    public function __toString(): string
    {
        return $this->getData();
    }

    /**
     * Returns the token data.
     * @return string The token's data as a string.
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Returns the token's position.
     * @return int The token's position on the string.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Returns the token's type.
     * @return int The token's type.
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * Checks whether the token matches the requested flag.
     * @param int $check The flag to check the token against.
     * @return bool Has the token matched the flag?
     */
    public function is(int $check): bool
    {
        return $check == ($this->type & $check);
    }
}
