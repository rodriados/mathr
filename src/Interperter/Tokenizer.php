<?php
/**
 * An abstract tokenizer implementation.
 * @package Mathr\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Interperter;

use Mathr\Contracts\Interperter\TokenInterface;
use Mathr\Contracts\Interperter\TokenizerInterface;

/**
 * The abstract base for the project's internal tokenizers.
 * @package Mathr\Interperter
 */
abstract class Tokenizer implements TokenizerInterface
{
    /**
     * Tokenizes the given expression.
     * @param string $expression The target expression.
     * @return TokenInterface[] The list of extracted tokens.
     */
    final public function runTokenizer(string $expression): array
    {
        return static::extractTokens($expression);
    }

    /**
     * Extracts all tokens from the expression.
     * @param string $expression The expression to be tokenized.
     * @return TokenInterface[] The list of tokens extracted from the expression.
     */
    protected abstract static function extractTokens(string $expression): array;
}
