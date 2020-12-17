<?php
/**
 * Expression token representation.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

use Mathr\Parser\Node\OperatorNode;

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
    public const NUMBER      = 0x0001;
    public const IDENTIFIER  = 0x0002;
    public const OPERATOR    = 0x0004;
    public const PARENTHESIS = 0x0008;
    public const BRACKETS    = 0x0010;
    public const CURLY       = 0x0020;
    public const COMMA       = 0x0040;
    public const EOS         = 0x0080;
    public const UNKNOWN     = 0x8000;
    /**#@-*/

    /**#@+
     * The token associativity constants.
     * These flags inform whether a token has a specific associativity.
     */
    public const RIGHT       = 0x1000;
    public const LEFT        = 0x2000;
    public const UNARY       = 0x4000;
    /**#@-*/

    /**#@+
     * Token type masks.
     * These masks allow information to be extracted from the token's type.
     */
    public const TYPE_MASK   = 0x00FF;
    public const ASSOC_MASK  = 0x7000;
    /**#@-*/

    /**
     * Operators that may be unary.
     * These are all operators that may become an unary token.
     */
    public const MAYBE_UNARY = [
        OperatorNode::SUM,
        OperatorNode::SUB,
    ];

    /**
     * Unary translations.
     * Informs all operator transformation to unary possibilities.
     */
    private const TO_UNARY    = [
        OperatorNode::SUM => OperatorNode::POS,
        OperatorNode::SUB => OperatorNode::NEG,
    ];

    /**
     * Token constructor.
     * @param string $data The token's detection string.
     * @param int $position The token's position on expression.
     * @param int $type The type of the parsed token.
     */
    public function __construct(
        private string $data = "",
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
     * @param int $mask The mask to apply to token type.
     * @return int The token's type.
     */
    public function getType(int $mask = 0xFFFF): int
    {
        return $this->type & $mask;
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

    /**
     * Transform an operator token into its unary counterpart, if possible.
     * @param Token $token The token to be transformed.
     * @return Token The new unary operator token.
     */
    public static function makeUnary(Token $token): Token
    {
        return $token->is(self::OPERATOR) && array_key_exists($token->data, self::TO_UNARY)
            ? new static(self::TO_UNARY[$token->data], $token->position, self::OPERATOR | self::UNARY)
            : $token;
    }
}
