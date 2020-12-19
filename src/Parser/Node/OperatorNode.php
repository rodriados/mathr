<?php
/**
 * Node for operators.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;
use Mathr\Parser\ParserException;

/**
 * Stores an operator reference in an expression node.
 * @package Mathr\Parser\Node
 */
class OperatorNode extends Node
{
    /**#@+
     * The operators' symbols.
     * Informs the symbol representation of each operator.
     */
    public const EQL = '=';
    public const SUM = '+';
    public const SUB = '-';
    public const MUL = '*';
    public const DIV = '/';
    public const PWR = '^';
    public const POS = 'U+';
    public const NEG = 'U-';
    /**#@-*/

    /**
     * The operators' precedence.
     * Informs the order in which operators must be evaluated.
     */
    private const PRECEDENCE = [
        self::EQL =>  0,
        self::SUM =>  2,
        self::SUB =>  2,
        self::MUL =>  3,
        self::DIV =>  3,
        self::PWR =>  4,
        self::POS => 10,
        self::NEG => 10,
    ];

    /**
     * The list of unary operators.
     * Informs which operators are unary and only require one argument.
     */
    private const UNARY = [
        self::POS,
        self::NEG,
    ];

    /**
     * The operator's associativity, either left or right.
     * @var int The operator's associativity.
     */
    private int $assoc;

    /**
     * OperatorNode constructor.
     * @param Token $token The token represented by the node.
     * @throws ParserException Invalid operator detected.
     */
    public function __construct(Token $token)
    {
        if (!array_key_exists($token->getData(), self::PRECEDENCE))
            throw ParserException::unexpectedToken($token);

        $this->assoc = $token->getType(Token::ASSOC_MASK);

        parent::__construct($token);
    }

    /**
     * Informs whether the node's operator has a left or right associativity.
     * @return int The operator's associativity.
     */
    public function getAssoc(): int
    {
        return $this->assoc;
    }

    /**
     * Retrieves the node's operator precedence.
     * @return int The operator's precedence.
     */
    public function getPrecedence(): int
    {
        return self::PRECEDENCE[$this->getData()];
    }
}
