<?php
/**
 * Node for brackets.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter\Node;

use Mathr\Interperter\Token;

/**
 * A syntax-sugar for the vector accessor function.
 * @package Mathr\Parser\Node
 */
class BracketsNode extends FunctionNode
{
    /**
     * Retrieves the data represented by the node.
     * @return string The node's internal data.
     */
    public function getData(): string
    {
        return '[]';
    }

    /**
     * Indicates the required closing token type.
     * @return int The required closing token type.
     */
    public static function getOpeningPair(): int
    {
        return Token::BRACKETS | Token::LEFT;
    }

    /**
     * Informs the expected closing token type.
     * @return int The token type expected to close a parenthesis node.
     */
    public static function getClosingPair(): int
    {
        return Token::BRACKETS | Token::RIGHT;
    }
}
