<?php
/**
 * A mock for a stateful parser.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use SplStack as Stack;
use Mathr\Interperter\Parser\StatefulParser;

/**
 * Mocks a stateful parser class.
 * @package Mathr
 */
class StatefulParserMock extends StatefulParser
{
    /**
     * Creates a mocked state for the parser.
     * @return object The parser's new empty state.
     */
    protected static function stateFactory(): object
    {
        return (object) [
            'stack'          => new Stack,
            'expression'     => new TokenListExpressionBuilderMock,
            'expectOperator' => false,
        ];
    }
}
