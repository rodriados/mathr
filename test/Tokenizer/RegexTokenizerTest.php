<?php
/**
 * RegexTokenizer unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Interperter\Token;
use Mathr\Interperter\Tokenizer\RegexTokenizer;
use Mathr\Contracts\Interperter\TokenizerInterface;
use PHPUnit\Framework\TestCase;

/**
 * The Mathr\Parser\Tokenizer\RegexTokenizer test class.
 * @package Mathr
 */
class RegexTokenizerTest extends TestCase
{
    /**
     * The test's target tokenizer for testing.
     * @var RegexTokenizer The tokenizer instance.
     */
    protected RegexTokenizer $tokenizer;

    /**
     * Sets the tests' environment up.
     * @since 3.0
     */
    protected function setUp(): void
    {
        $this->tokenizer = new RegexTokenizer();
    }

    /**
     * Checks whether the tokenizer has been successfully instantiated.
     * @since 3.0
     */
    public function testIfCanBeInstantiated()
    {
        $this->assertInstanceOf(TokenizerInterface::class, $this->tokenizer);
        $this->assertInstanceOf(RegexTokenizer::class, $this->tokenizer);
    }

    /**
     * Checks whether all token types can be correctly parsed.
     * @since 3.0
     */
    public function testForCorrectTokenTypes()
    {
        $testCases = [
            '9' => Token::NUMBER,
            'i' => Token::IDENTIFIER,
            '+' => Token::OPERATOR    | Token::RIGHT,
            '-' => Token::OPERATOR    | Token::RIGHT,
            '*' => Token::OPERATOR    | Token::RIGHT,
            '/' => Token::OPERATOR    | Token::RIGHT,
            '=' => Token::OPERATOR    | Token::LEFT,
            '^' => Token::OPERATOR    | Token::LEFT,
            '(' => Token::PARENTHESIS | Token::OPEN,
            ')' => Token::PARENTHESIS | Token::CLOSE,
            ',' => Token::COMMA,
            '#' => Token::UNKNOWN
        ];

        foreach ($testCases as $expression => $type) {
            $tokenList = $this->tokenizer->runTokenizer($expression);

            $this->assertEquals(2, count($tokenList));
            $this->assertEquals($type, $tokenList[0]->getType());
            $this->assertEquals(Token::EOS, $tokenList[1]->getType());
            $this->assertEquals($expression, $tokenList[0]->getData());
            $this->assertEquals(0, $tokenList[0]->getPosition());
        }
    }

    /**
     * Checks whether a whole expression produces the correct list of tokens.
     * @since 3.0
     */
    public function testIfExpressionCanBeTokenized()
    {
        $testCase = 'f(x) = (phi ^ x - (1 - phi) ^ x) / sqrt(5)';

        $expected = [
            new Token('f',      0, Token::IDENTIFIER                 ),
            new Token('(',      1, Token::PARENTHESIS | Token::OPEN  ),
            new Token('x',      2, Token::IDENTIFIER                 ),
            new Token(')',      3, Token::PARENTHESIS | Token::CLOSE ),
            new Token('=',      5, Token::OPERATOR    | Token::LEFT  ),
            new Token('(',      7, Token::PARENTHESIS | Token::OPEN  ),
            new Token('phi',    8, Token::IDENTIFIER                 ),
            new Token('^',     12, Token::OPERATOR    | Token::LEFT  ),
            new Token('x',     14, Token::IDENTIFIER                 ),
            new Token('-',     16, Token::OPERATOR    | Token::RIGHT ),
            new Token('(',     18, Token::PARENTHESIS | Token::OPEN  ),
            new Token('1',     19, Token::NUMBER                     ),
            new Token('-',     21, Token::OPERATOR    | Token::RIGHT ),
            new Token('phi',   23, Token::IDENTIFIER                 ),
            new Token(')',     26, Token::PARENTHESIS | Token::CLOSE ),
            new Token('^',     28, Token::OPERATOR    | Token::LEFT  ),
            new Token('x',     30, Token::IDENTIFIER                 ),
            new Token(')',     31, Token::PARENTHESIS | Token::CLOSE ),
            new Token('/',     33, Token::OPERATOR    | Token::RIGHT ),
            new Token('sqrt',  35, Token::IDENTIFIER                 ),
            new Token('(',     39, Token::PARENTHESIS | Token::OPEN  ),
            new Token('5',     40, Token::NUMBER                     ),
            new Token(')',     41, Token::PARENTHESIS | Token::CLOSE ),
            new Token('',      -1, Token::EOS                        ),
        ];

        $tokenList = $this->tokenizer->runTokenizer($testCase);

        $this->assertSameSize($expected, $tokenList);

        foreach ($tokenList as $pos => $token) {
            $this->assertEquals($expected[$pos]->getData(), $token->getData());
            $this->assertEquals($expected[$pos]->getType(), $token->getType());
            $this->assertEquals($expected[$pos]->getPosition(), $token->getPosition());
        }
    }
}
