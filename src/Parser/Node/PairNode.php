<?php
/**
 * Node for paired constructs.
 * @package Mathr\Parser\Node
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser\Node;

use Mathr\Parser\Token;
use Mathr\Parser\ParserException;

/**
 * Represents nodes that opening and closing pairs.
 * @package Mathr\Parser\Node
 */
abstract class PairNode extends Node
{
    /**
     * Indicates whether the current node is opening or closing the pair.
     * @var bool Is the node opening the pair?
     */
    protected bool $isOpening;

    /**
     * PairedNode constructor.
     * @param Token $token The token represented by the node.
     */
    public function __construct(Token $token)
    {
        parent::__construct($token);
        $this->isOpening = $token->is(static::getOpeningPair());
    }

    /**
     * Closes a paired opening node.
     * @param PairNode $node The pair's closing node.
     * @throws ParserException Mismatched token for closing node.
     */
    public function close(PairNode $node): void
    {
        if (!$node->getToken()->is(static::getClosingPair())) {
            throw ParserException::unexpectedToken($node->getToken());
        }
    }

    /**
     * Indicates the required closing token type.
     * @return int The required closing token type.
     */
    public abstract static function getOpeningPair(): int;

    /**
     * Indicates the required closing token type.
     * @return int The required closing token type.
     */
    public abstract static function getClosingPair(): int;
}
