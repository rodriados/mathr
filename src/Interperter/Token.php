<?php
/**
 * An expression token representation.
 * @package Mathr\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter;

use Exception;
use Mathr\Contracts\Interperter\TokenInterface;

/**
 * The token extracted by a tokenizer.
 * @package Mathr\Interperter
 */
class Token implements TokenInterface
{
    /**#@+
     * The token type constants.
     * These flags can be joined with flags of different types.
     */
    public const NUMBER      = 0x0001;
    public const IDENTIFIER  = 0x0002;
    public const OPERATOR    = 0x0004;
    public const PARENTHESIS = 0x0008;
    public const COMMA       = 0x0040;
    public const EOS         = 0x0080;
    public const UNKNOWN     = 0x8000;
    /**#@-*/

    /**#@+
     * Composed token types.
     * These token flags are a composition of basic token types.
     */
    public const FUNCTION    = self::IDENTIFIER | self::PARENTHESIS;
    /**#@-*/

    /**#@+
     * The token associativity constants.
     * These flags inform whether a token has a specific associativity.
     */
    public const RIGHT       = 0x1000;
    public const LEFT        = 0x2000;
    public const UNARY       = 0x4000;
    public const OPEN        = self::LEFT;
    public const CLOSE       = self::RIGHT;
    /**#@-*/

    /**#@+
     * Token type masks.
     * These masks allow information to be extracted from the token's type.
     */
    public const TYPE_MASK   = 0x00FF;
    public const ASSOC_MASK  = 0x7000;
    /**#@-*/

    /**#@+
     * Token properties list.
     * These flags informs which tokens fall into the given property.
     */
    public const ARGUMENTED  = self::IDENTIFIER;
    public const PAIRED      = self::PARENTHESIS;
    public const VALUED      = self::IDENTIFIER | self::NUMBER;
    /**#@-*/

    /**#@+
     * The operators' token symbols.
     * Informs the symbol representation of each operator.
     */
    public const OP_EQL =  '=';
    public const OP_SUM =  '+';
    public const OP_SUB =  '-';
    public const OP_MUL =  '*';
    public const OP_DIV =  '/';
    public const OP_PWR =  '^';
    public const OP_POS = '+#';
    public const OP_NEG = '-#';
    /**#@-*/

    /**
     * Operators associativity.
     * Informs the associativity of each operator.
     */
    public const OP_ASSOC    = [
        self::OP_EQL => self::LEFT,
        self::OP_SUM => self::RIGHT,
        self::OP_SUB => self::RIGHT,
        self::OP_MUL => self::RIGHT,
        self::OP_DIV => self::RIGHT,
        self::OP_PWR => self::LEFT,
        self::OP_POS => self::UNARY,
        self::OP_NEG => self::UNARY,
    ];

    /**
     * Unary translations.
     * Informs all operator transformation to unary possibilities.
     */
    private const TO_UNARY   = [
        self::OP_SUM => self::OP_POS,
        self::OP_SUB => self::OP_NEG,
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
     * @param int $flag The flag to check the token against.
     * @return bool Has the token matched the flag?
     */
    public function isOf(int $flag): bool
    {
        return $flag == $this->getType($flag);
    }

    /**
     * Exports the token by serializing it into a string.
     * @return string The serialized token string.
     */
    public function serialize(): string
    {
        return join(':', [ $this->type, $this->data ]);
    }

    /**
     * Imports a token by unserializing it from a string.
     * @param string $serialized The serialized token string.
     */
    public function unserialize(string $serialized): void
    {
        [ $this->type, $this->data ] = explode(':', $serialized);
    }

    /**
     * Transform an operator token into its unary counterpart, if possible.
     * @param TokenInterface $token The token to be transformed.
     * @return Token|null The new unary operator token.
     */
    public static function makeUnary(TokenInterface $token): ?Token
    {
        if (!$token->isOf(self::OPERATOR) || !array_key_exists($token->getData(), self::TO_UNARY))
            return null;

        return new static(
            data: self::TO_UNARY[$token->getData()],
            type: self::OPERATOR | self::UNARY,
            position: $token->getPosition(),
        );
    }
}
