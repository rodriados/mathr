<?php
/**
 * Tokenizer unit tests.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */

use Mathr\Parser\Token;
use Mathr\Parser\Tokenizer;
use PHPUnit\Framework\TestCase;

/**
 * The Mathr\Tokenizer test class.
 */
final class TokenizerTest extends TestCase
{
	/**
     * The parser instance to test.
     * @var Tokenizer The testing subject.
     */
	protected $tokenizer;

    /**
     * Sets the testing environment up.
     */
    public function setUp(): void
	{
		$this->tokenizer = new Tokenizer;
	}

    /**
     * Checks whether the tokenizer has been successfully instantiated.
     */
    public function testIfCanBeInstantiated()
    {
        $this->assertInstanceOf(Tokenizer::class, $this->tokenizer);
    }

    /**
     * Checks whether all token types can be correctly parsed.
     */
    public function testForCorrectTokenTypes()
    {
        $expected = [
            '9' => Token::NUMBER,
            'i' => Token::IDENTIFIER,
            '+' => Token::OPERATOR | Token::RIGHT,
            '-' => Token::OPERATOR | Token::RIGHT,
            '*' => Token::OPERATOR | Token::RIGHT,
            '/' => Token::OPERATOR | Token::RIGHT,
            '=' => Token::OPERATOR | Token::LEFT,
            '^' => Token::OPERATOR | Token::LEFT,
            '(' => Token::PARENTHESES | Token::LEFT,
            ')' => Token::PARENTHESES | Token::RIGHT,
            '[' => Token::BRACKETS | Token::LEFT,
            ']' => Token::BRACKETS | Token::RIGHT,
            '{' => Token::CURLY | Token::LEFT,
            '}' => Token::CURLY | Token::RIGHT,
            ',' => Token::COMMA,
            '#' => Token::UNKNOWN
        ];

        foreach ($expected as $expression => $type) {
            $this->tokenizer->tokenize($expression);
            $this->assertEquals($type, $this->tokenizer->current()->getType());
        }
    }

    /**
     * Checks whether a whole expression produces the correct list of tokens.
     */
    public function testIfExpressionCanBeTokenized()
    {
        $expression = 'f(x) = (phi ^ x - (1 - phi) ^ x) / sqrt(5)';

        $expected = [
            new Token('f',     0, Token::IDENTIFIER                 ),
            new Token('(',     1, Token::PARENTHESES | Token::LEFT  ),
            new Token('x',     2, Token::IDENTIFIER                 ),
            new Token(')',     3, Token::PARENTHESES | Token::RIGHT ),
            new Token('=',     5, Token::OPERATOR    | Token::LEFT  ),
            new Token('(',     7, Token::PARENTHESES | Token::LEFT  ),
            new Token('phi',   8, Token::IDENTIFIER                 ),
            new Token('^',    12, Token::OPERATOR    | Token::LEFT  ),
            new Token('x',    14, Token::IDENTIFIER                 ),
            new Token('-',    16, Token::OPERATOR    | Token::RIGHT ),
            new Token('(',    18, Token::PARENTHESES | Token::LEFT  ),
            new Token('1',    19, Token::NUMBER                     ),
            new Token('-',    21, Token::OPERATOR    | Token::RIGHT ),
            new Token('phi',  23, Token::IDENTIFIER                 ),
            new Token(')',    26, Token::PARENTHESES | Token::RIGHT ),
            new Token('^',    28, Token::OPERATOR    | Token::LEFT  ),
            new Token('x',    30, Token::IDENTIFIER                 ),
            new Token(')',    31, Token::PARENTHESES | Token::RIGHT ),
            new Token('/',    33, Token::OPERATOR    | Token::RIGHT ),
            new Token('sqrt', 35, Token::IDENTIFIER                 ),
            new Token('(',    39, Token::PARENTHESES | Token::LEFT  ),
            new Token('5',    40, Token::NUMBER                     ),
            new Token(')',    41, Token::PARENTHESES | Token::RIGHT ),
        ];

        $this->tokenizer->tokenize($expression);

        foreach ($this->tokenizer as $pos => $token) {
            $this->assertEquals($expected[$pos]->getData(), $token->getData());
            $this->assertEquals($expected[$pos]->getType(), $token->getType());
            $this->assertEquals($expected[$pos]->getPosition(), $token->getPosition());
        }
    }
}
