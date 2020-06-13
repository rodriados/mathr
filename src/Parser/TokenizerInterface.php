<?php
/**
 * Interface for expression tokenizers.
 * @package Mathr\Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2020-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Parser;

use Iterator;

/**
 * Represents the methods needed for an expression tokenizer.
 * @package Mathr\Parser
 */
interface TokenizerInterface extends Iterator
{
    /**
     * Sets the expression to be tokenized.
     * @param string $expression The target expression.
     */
    public function tokenize(string $expression): void;

    /**
     * Extracts a token from the expression.
     * @return Token The extracted token.
     */
    public function current(): Token;
}
