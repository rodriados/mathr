<?php
/**
 * A tokenizer based on regex extraction.
 * @package Mathr\Interperter\Tokenizer
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter\Tokenizer;

use Mathr\Interperter\Token;
use Mathr\Interperter\Tokenizer;
use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Interperter\TokenizerException;

/**
 * A regex-based expression tokenizer and token extractor.
 * @package Mathr\Interperter\Tokenizer
 */
class RegexTokenizer extends Tokenizer
{
    /**
     * The list of regex patterns for each token type.
     * @var string[] The list of token type patterns.
     */
    private const TOKEN_PATTERNS = [
        'number'     => '(?:[0-9]*\.[0-9]+|[0-9]+\.?)(?:e[+-]?[0-9]+)?',
        'identifier' => '(?:[\p{L}_][\p{L}0-9_]*)',
        'op_right'   => '(?:[-+/*])',
        'op_left'    => '(?:[=^])',
        'paren'      => '(?:[()])',
        'bracket'    => '(?:[\[\]])',
        'curly'      => '(?:[{}])',
        'comma'      => '(?:,)',
        'unknown'    => '(?:[^\s])',
    ];

    /**
     * Extracts the tokens from expression using regex.
     * @param string $expression The expression to be tokenized.
     * @return TokenInterface[] The tokens extracted from the expression.
     * @throws TokenizerException The expression is invalid.
     */
    protected static function extractTokens(string $expression): array
    {
        [$success, $extracted] = static::runRegex($expression);

        if (!$success)
            throw TokenizerException::expressionIsInvalid();

        return self::processExtraction($extracted);
    }

    /**
     * Runs the regex onto the expression, and return its raw extraction.
     * @param string $expression The expression to be tokenized.
     * @return array The raw regex extraction and success flag.
     */
    protected static function runRegex(string $expression): array
    {
        foreach (self::TOKEN_PATTERNS as $group => $pattern)
            $regex[] = sprintf('(?<%s>%s)', $group, $pattern);

        $regex   = sprintf('#%s#iu', join('|', $regex ?? []));
        $success = preg_match_all($regex, $expression, $raw, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);

        return [!!$success, $raw];
    }

    /**
     * Transforms the regex extraction into tokens.
     * @param array $extracted The raw regex extraction.
     * @return TokenInterface[] The list of extracted tokens.
     */
    protected static function processExtraction(array $extracted): array
    {
        return array_merge(
            array_map([self::class, 'matchToToken'], $extracted),
            [new Token(type: Token::EOS, position: -1)]
        );
    }

    /**
     * Transforms a regex match into a token instance.
     * @param array $match The regex match to be transformed.
     * @return TokenInterface The created token.
     */
    private static function matchToToken(array $match): TokenInterface
    {
        $lambda = fn ($value, $key) => !is_numeric($key) && $value[1] >= 0;
        $token  = array_filter($match, $lambda, ARRAY_FILTER_USE_BOTH);
        $type   = array_key_first($token);

        return self::makeToken($type, ...$token[$type]);
    }

    /**
     * Creates a token from the regex extracted data.
     * @param string $type The type of extracted token.
     * @param string $data The raw token's string data.
     * @param int $pos The token's extraction position.
     * @return TokenInterface The parsed token.
     */
    private static function makeToken(string $type, string $data, int $pos): TokenInterface
    {
        return match ($type) {
            'number'     => new Token($data, $pos, type: Token::NUMBER),
            'identifier' => new Token($data, $pos, type: Token::IDENTIFIER),
            'op_right'   => new Token($data, $pos, type: Token::OPERATOR | Token::RIGHT),
            'op_left'    => new Token($data, $pos, type: Token::OPERATOR | Token::LEFT),
            'paren'      => new Token($data, $pos, type: Token::PARENTHESIS | self::getPairType($data)),
            'bracket'    => new Token($data, $pos, type: Token::BRACKETS    | self::getPairType($data)),
            'curly'      => new Token($data, $pos, type: Token::CURLYBRACES | self::getPairType($data)),
            'comma'      => new Token($data, $pos, type: Token::COMMA),
            default      => new Token($data, $pos, type: Token::UNKNOWN),
        };
    }

    /**
     * Informs whether a paired token is its left or right character.
     * @param string $data The raw token's string data.
     * @return int The token's pair side.
     */
    private static function getPairType(string $data): int
    {
        return match ($data) {
            '(', '[', '{' => Token::OPEN,
            ')', ']', '}' => Token::CLOSE,
            default       => Token::UNKNOWN,
        };
    }
}
