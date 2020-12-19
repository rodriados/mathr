<?php
/**
 * Expression token extraction.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

/**
 * The expression tokenizer and token extractor.
 * @package Mathr\Parser
 */
class Tokenizer implements TokenizerInterface
{
    /**
     * The current expression extracted tokens.
     * @var array The raw tokenization result.
     */
    private array $tokens = [];

    /**
     * The current tokenization position.
     * @var int The position being currently tokenized.
     */
    private int $position = 0;

    /**
     * The target expression to tokenize.
     * @var string The expression to tokenize.
     */
    private string $expression;

    /**
     * Sets the expression to be tokenized.
     * @param string $expression The target expression.
     * @throws ParserException The expression is invalid.
     */
    public function tokenize(string $expression): void
    {
        $this->tokens     = static::extract($expression);
        $this->expression = $expression;

        $this->rewind();
    }

    /**
     * Extracts the tokens from expression using regex.
     * @param string $expression The expression to be tokenized.
     * @return array The tokens extracted from the expression.
     * @throws ParserException The expression is invalid.
     */
    protected static function extract(string $expression): array
    {
        $success = preg_match_all(
            '#(?<number>(?:[0-9]*\.[0-9]+|[0-9]+\.?)(?:e[+-]?[0-9]+)?)'.    # Group 1: Number literals
            '|(?<identifier>\p{L}[\p{L}0-9_]*\(?)'.                         # Group 2: Identifiers
            '|(?<rop>[-+/*])'.                                              # Group 3: Right associativity operators
            '|(?<lop>[=^])'.                                                # Group 4: Left associativity operators
            '|(?<paren>[()])'.                                              # Group 5: Parentheses
            '|(?<bracket>[\[\]])'.                                          # Group 6: Brackets
            '|(?<curly>[{}])'.                                              # Group 7: Curlies
            '|(?<comma>,)'.                                                 # Group 8: Comma
            '|(?<unknown>[^\s])'.                                           # Group 9: Unknown
            '#iu',
            $expression,
            $extracted,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );

        if (!$success)
            throw ParserException::invalidExpression();

        return array_merge(
            self::process($extracted),
            [new Token(type: Token::EOS, position: strlen($expression))]
        );
    }

    /**
     * Transforms the regex extraction into tokens.
     * @param array $extracted The raw regex extraction.
     * @return array The list of extracted tokens.
     */
    private static function process(array $extracted): array
    {
        return array_map(array: $extracted, callback: function ($match) {
            $token = array_filter(
                array: $match,
                callback: fn ($value, $key) => !is_numeric($key) && $value[1] >= 0,
                mode: ARRAY_FILTER_USE_BOTH,
            );

            $type = array_key_first($token);
            return self::makeToken($type, ...$token[$type]);
        });
    }

    /**
     * Creates a token from the regex extracted data.
     * @param string $type The type of extracted token.
     * @param string $data The raw token's string data.
     * @param int $pos The token's extraction position.
     * @return Token The parsed token.
     */
    private static function makeToken(string $type, string $data, int $pos): Token
    {
        return match ($type) {
            'number'     => new Token($data, $pos, type: Token::NUMBER),
            'identifier' => new Token($data, $pos, type: Token::IDENTIFIER),
            'rop'        => new Token($data, $pos, type: Token::OPERATOR | Token::RIGHT),
            'lop'        => new Token($data, $pos, type: Token::OPERATOR | Token::LEFT),
            'paren'      => new Token($data, $pos, type: Token::PARENTHESIS | self::pairType($data)),
            'bracket'    => new Token($data, $pos, type: Token::BRACKETS | self::pairType($data)),
            'curly'      => new Token($data, $pos, type: Token::CURLY | self::pairType($data)),
            'comma'      => new Token($data, $pos, type: Token::COMMA),
            default      => new Token($data, $pos, type: Token::UNKNOWN),
        };
    }

    /**
     * Informs whether a paired token is its left or right character.
     * @param string $data The raw token's string data.
     * @return int The token's pair side.
     */
    private static function pairType(string $data): int
    {
        return match ($data) {
            '(', '[', '{' => Token::LEFT,
            ')', ']', '}' => Token::RIGHT,
            default       => Token::UNKNOWN,
        };
    }

    /**
     * Extracts a token from the expression.
     * @return Token The extracted token.
     */
    public function current(): Token
    {
        return $this->tokens[$this->position];
    }

    /**
     * Advances tokenization to the next position.
     * @return int The new iterator position.
     */
    public function next(): int
    {
        return ++$this->position;
    }

    /**
     * Returns the current iteration key.
     * @return int Expression iteration position.
     */
    public function key(): int
    {
        return $this->position;
    }

    /**
     * Informs whether the iterator is still valid.
     * @return bool Is the iterator valid?
     */
    public function valid(): bool
    {
        return $this->position < count($this->tokens);
    }

    /**
     * Rewinds the iterator position to the first position.
     * @return bool It always succeeds.
     */
    public function rewind(): bool
    {
        $this->position = 0;
        return true;
    }
}
