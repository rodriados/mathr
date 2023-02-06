<?php
/**
 * StatefulParser unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Interperter\Token;
use Mathr\Interperter\Parser\StatefulParser;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Interperter\ParserInterface;
use Mathr\Interperter\Tokenizer\DefaultTokenizer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * The Mathr\Interpreter\Parser\StatefulParser test class.
 * @package Mathr
 */
final class StatefulParserTest extends TestCase
{
    /**
     * The test's target parser for testing.
     * @var StatefulParser The parser instance.
     */
    protected StatefulParser $parser;

    /**
     * Sets the tests' environment up.
     * @since 3.0
     */
    protected function setUp(): void
    {
        $this->parser = new StatefulParserMock(new DefaultTokenizer);
    }

    /**
     * Checks whether the parser has been successfully instantiated.
     * @since 3.0
     */
    public function testIfCanBeInstantiated()
    {
        $this->assertInstanceOf(ParserInterface::class, $this->parser);
        $this->assertInstanceOf(StatefulParser::class, $this->parser);
    }

    /**
     * Tests whether a expressions can be parsed.
     * @param string $expression The expression to be parsed.
     * @param array $expected The expected parsed tokens.
     * @since 3.0
     */
    #[DataProvider("provideExpressions")]
    public function testIfParsesExpressions(string $expression, array $expected)
    {
        $expressionMock = $this->parser->runParser($expression);
        $tokenList = $expressionMock->getRaw();

        $this->assertSameSize($expected, $tokenList);

        foreach ($tokenList as $pos => $token) {
            $this->assertEquals($expected[$pos]->getData(), $token->getData());
            $this->assertEquals($expected[$pos]->getType(), $token->getType());
            $this->assertEquals($expected[$pos]->getPosition(), $token->getPosition());
        }
    }

    /**
     * Provides expressions to be parsed and tested with.
     * @return array[] The list of expressions.
     */
    public static function provideExpressions(): array
    {
        return [
            [
                'x + 3(2x + 4y)^2',
                [
                    new Token('x',  0, Token::IDENTIFIER              ),
                    new Token('3',  4, Token::NUMBER                  ),
                    new Token('2',  6, Token::NUMBER                  ),
                    new Token('x',  7, Token::IDENTIFIER              ),
                    new Token('*',  7, Token::OPERATOR | Token::RIGHT ),
                    new Token('4', 11, Token::NUMBER                  ),
                    new Token('y', 12, Token::IDENTIFIER              ),
                    new Token('*', 12, Token::OPERATOR | Token::RIGHT ),
                    new Token('+',  9, Token::OPERATOR | Token::RIGHT ),
                    new Token('2', 15, Token::NUMBER                  ),
                    new Token('^', 14, Token::OPERATOR | Token::LEFT  ),
                    new Token('*',  5, Token::OPERATOR | Token::RIGHT ),
                    new Token('+',  2, Token::OPERATOR | Token::RIGHT ),
                ]
            ],
            [
                'f(x, y) = g(x + y) + -(h(x - 1)^2 - i(-x, +y))',
                [
                    new Token( 'x',  2, Token::IDENTIFIER              ),
                    new Token( 'y',  5, Token::IDENTIFIER              ),
                    new Token('f(',  0, Token::FUNCTION | Token::OPEN  ),
                    new Token( 'x', 12, Token::IDENTIFIER              ),
                    new Token( 'y', 16, Token::IDENTIFIER              ),
                    new Token( '+', 14, Token::OPERATOR | Token::RIGHT ),
                    new Token('g(', 10, Token::FUNCTION | Token::OPEN  ),
                    new Token( 'x', 25, Token::IDENTIFIER              ),
                    new Token( '1', 29, Token::NUMBER                  ),
                    new Token( '-', 27, Token::OPERATOR | Token::RIGHT ),
                    new Token('h(', 23, Token::FUNCTION | Token::OPEN  ),
                    new Token( '2', 32, Token::NUMBER                  ),
                    new Token( '^', 31, Token::OPERATOR | Token::LEFT  ),
                    new Token( 'x', 39, Token::IDENTIFIER              ),
                    new Token('-#', 38, Token::OPERATOR | Token::UNARY ),
                    new Token( 'y', 43, Token::IDENTIFIER              ),
                    new Token('+#', 42, Token::OPERATOR | Token::UNARY ),
                    new Token('i(', 36, Token::FUNCTION | Token::OPEN  ),
                    new Token( '-', 34, Token::OPERATOR | Token::RIGHT ),
                    new Token('-#', 21, Token::OPERATOR | Token::UNARY ),
                    new Token( '+', 19, Token::OPERATOR | Token::RIGHT ),
                    new Token( '=',  8, Token::OPERATOR | Token::LEFT  ),
                ]
            ],
            [
                'f() * (g() + h())',
                [
                    new Token('f(',  0, Token::FUNCTION | Token::OPEN  ),
                    new Token('g(',  7, Token::FUNCTION | Token::OPEN  ),
                    new Token('h(', 13, Token::FUNCTION | Token::OPEN  ),
                    new Token( '+', 11, Token::OPERATOR | Token::RIGHT ),
                    new Token( '*',  4, Token::OPERATOR | Token::RIGHT ),
                ],
            ],
            [
                'x + y = 10',
                [
                    new Token( 'x', 0, Token::IDENTIFIER              ),
                    new Token( 'y', 4, Token::IDENTIFIER              ),
                    new Token( '+', 2, Token::OPERATOR | Token::RIGHT ),
                    new Token('10', 8, Token::NUMBER                  ),
                    new Token( '=', 6, Token::OPERATOR | Token::LEFT  ),
                ]
            ]
        ];
    }

    /**
     * Tests whether invalid expressions can be detected.
     * @param string $expression The invalid expression.
     * @since 3.0
     */
    #[DataProvider("provideInvalidExpressions")]
    public function testIfDetectsInvalidExpressions(string $expression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches("/Unexpected token '.' at position \d/");
        $this->parser->runParser($expression);
    }

    /**
     * Provides invalid expressions for testing.
     * @return string[][] The invalid expressions.
     */
    public static function provideInvalidExpressions(): array
    {
        return [
            [ '2 +' ],
            [ '3 5' ],
            [ '(1,2)' ],
            [ '3 * ()' ],
            [ 'func(,)' ],
            [ 'func(2,)' ],
            [ 'func(*3)' ],
        ];
    }

    /**
     * Tests whether mismatched pair nodes can be detected.
     * @param string $expression The mismatched expression.
     * @since 3.0
     */
    #[DataProvider("provideMismatchedExpressions")]
    public function testIfDetectsMismatches(string $expression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches("/^Mismatched token '.' at position \d$/");
        $this->parser->runParser($expression);
    }

    /**
     * Provides mismatched expressions for testing.
     * @return string[][] The mismatched expresions.
     */
    public static function provideMismatchedExpressions(): array
    {
        return [
            [ '(((((1))))' ],
            [ '(f(1,2,3)'  ],
            [ 'f(1,2,3))'  ],
            [ '((1 + 2)))' ],
        ];
    }
}
