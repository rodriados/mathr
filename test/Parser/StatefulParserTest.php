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
use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Interperter\ParserException;
use Mathr\Contracts\Interperter\ParserInterface;
use Mathr\Interperter\Tokenizer\DefaultTokenizer;
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
     * Tests whether a simple expression can be parsed.
     * @since 3.0
     */
    public function testIfParsesSimpleExpression()
    {
        $testCase = "x + 3(2x + 4y)^2";

        $expected = [
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
        ];

        /** @var TokenListExpressionBuilderMock $expression */
        $expression = $this->parser->runParser($testCase);
        $tokenList = $expression->getTokens();

        $this->assertExpectedTokens($expected, $tokenList);
    }

    /**
     * Tests whether functions are correctly parsed.
     * @since 3.0
     */
    public function testIfParsesFunctionExpression()
    {
        $testCase = "f(x, y) = g(x + y) + -(h(x - 1)^2 - i(-x, +y))";

        $expected = [
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
        ];

        /** @var TokenListExpressionBuilderMock $expression */
        $expression = $this->parser->runParser($testCase);
        $tokenList = $expression->getTokens();

        $this->assertExpectedTokens($expected, $tokenList);
    }

    /**
     * Tests whether empty functions can be parsed.
     * @since 3.0
     */
    public function testIfParsesEmptyFunctionExpression()
    {
        $testCase = "f() * (g() + h())";

        $expected = [
            new Token('f(',  0, Token::FUNCTION | Token::OPEN  ),
            new Token('g(',  7, Token::FUNCTION | Token::OPEN  ),
            new Token('h(', 13, Token::FUNCTION | Token::OPEN  ),
            new Token( '+', 11, Token::OPERATOR | Token::RIGHT ),
            new Token( '*',  4, Token::OPERATOR | Token::RIGHT ),
        ];

        /** @var TokenListExpressionBuilderMock $expression */
        $expression = $this->parser->runParser($testCase);
        $tokenList = $expression->getTokens();

        $this->assertExpectedTokens($expected, $tokenList);
    }

    /**
     * Tests whether expressions with vectors can be parsed.
     * @since 3.0
     */
    public function testIfParsesVectorExpression()
    {
        $testCase = "{{1, 2, 3 + 1}, 3 {4 ^ 2, 5, 6}, -{7, f(8, x), 9}}[1, 2 * 3]";

        $expected = [
            new Token( '1',  2, Token::NUMBER                     ),
            new Token( '2',  5, Token::NUMBER                     ),
            new Token( '3',  8, Token::NUMBER                     ),
            new Token( '1', 12, Token::NUMBER                     ),
            new Token( '+', 10, Token::OPERATOR    | Token::RIGHT ),
            new Token( '{',  1, Token::CURLYBRACES | Token::OPEN  ),
            new Token( '3', 16, Token::NUMBER                     ),
            new Token( '4', 19, Token::NUMBER                     ),
            new Token( '2', 23, Token::NUMBER                     ),
            new Token( '^', 21, Token::OPERATOR    | Token::LEFT  ),
            new Token( '5', 26, Token::NUMBER                     ),
            new Token( '6', 29, Token::NUMBER                     ),
            new Token( '{', 18, Token::CURLYBRACES | Token::OPEN  ),
            new Token( '*', 18, Token::OPERATOR    | Token::RIGHT ),
            new Token( '7', 35, Token::NUMBER                     ),
            new Token( '8', 40, Token::NUMBER                     ),
            new Token( 'x', 43, Token::IDENTIFIER                 ),
            new Token('f(', 38, Token::FUNCTION    | Token::OPEN  ),
            new Token( '9', 47, Token::NUMBER                     ),
            new Token( '{', 34, Token::CURLYBRACES | Token::OPEN  ),
            new Token('-#', 33, Token::OPERATOR    | Token::UNARY ),
            new Token( '{',  0, Token::CURLYBRACES | Token::OPEN  ),
            new Token( '1', 51, Token::NUMBER                     ),
            new Token( '2', 54, Token::NUMBER                     ),
            new Token( '3', 58, Token::NUMBER                     ),
            new Token( '*', 56, Token::OPERATOR    | Token::RIGHT ),
            new Token( '[', 50, Token::BRACKETS    | Token::OPEN  ),
        ];

        /** @var TokenListExpressionBuilderMock $expression */
        $expression = $this->parser->runParser($testCase);
        $tokenList = $expression->getTokens();

        $this->assertExpectedTokens($expected, $tokenList);
    }

    /**
     * Tests whether invalid expressions can be detected.
     * @param string $expression The invalid expression.
     * @dataProvider provideInvalidExpressions
     * @since 3.0
     */
    public function testIfDetectsInvalidExpressions(string $expression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches("/Unexpected token '.' at position \d/");
        $this->parser->runParser($expression);
    }

    /**
     * Tests whether mismatched pair nodes can be detected.
     * @param string $expression The mismatched expression.
     * @dataProvider provideMismatchedExpressions
     * @since 3.0
     */
    public function testIfDetectsMismatches(string $expression)
    {
        $this->expectException(ParserException::class);
        $this->expectExceptionMessageMatches("/^Mismatched token '.' at position \d$/");
        $this->parser->runParser($expression);
    }

    /**
     * Asserts whether the parsed tokens correspond to the expected.
     * @param TokenInterface[] $expected The list of expected tokens.
     * @param TokenInterface[] $tokens The list of obtained tokens.
     */
    private function assertExpectedTokens(array $expected, array $tokens): void
    {
        $this->assertSameSize($expected, $tokens);

        foreach ($tokens as $pos => $token) {
            $this->assertEquals($expected[$pos]->getData(), $token->getData());
            $this->assertEquals($expected[$pos]->getType(), $token->getType());
            $this->assertEquals($expected[$pos]->getPosition(), $token->getPosition());
        }
    }

    /**
     * Provides invalid expressions for testing.
     * @return string[][] The invalid expressions.
     */
    public function provideInvalidExpressions(): array
    {
        return [
            [ '2 +' ],
            [ '3 5' ],
            [ '(1,2)' ],
            [ '3 * ()' ],
            [ 'func(,)' ],
            [ 'func(2,)' ],
            [ 'func(*3)' ],
            [ 'func([1])' ],
        ];
    }

    /**
     * Provides mismatched expressions for testing.
     * @return string[][] The mismatched expresions.
     */
    public function provideMismatchedExpressions(): array
    {
        return [
            [ '(((((1))))' ],
            [ '{{1,2,3})'  ],
            [ '{1,2,3}[1'  ],
            [ '((1 + 2)))' ],
        ];
    }
}
