<?php
/**
 * Node for general parenthesis.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;

/**
 * Represents a parenthesis grouping in an expression node.
 * @package Mathr\Parser\Node
 */
class ParenthesisNode extends PairNode
{
    /**
     * ParenthesisNode constructor.
     * @param Token $token The token represented by the node.
     */
    public function __construct(Token $token) {
        parent::__construct($token);
    }

    /**
     * Indicates the required closing token type.
     * @return int The required closing token type.
     */
    public static function getOpeningPair(): int
    {
        return Token::PARENTHESIS | Token::LEFT;
    }

    /**
     * Informs the expected closing token type.
     * @return int The token type expected to close a parenthesis node.
     */
    public static function getClosingPair(): int
    {
        return Token::PARENTHESIS | Token::RIGHT;
    }
}
