<?php
/**
 * The basics needed for an expression tokenizer.
 * @package Mathr\Contracts\Interperter
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Interperter;

/**
 * Lists the methods needed by an expression tokenizer.
 * @package Mathr\Contracts\Interperter
 */
interface TokenizerInterface
{
    /**
     * Sets the expression to be tokenized.
     * @param string $expression The target expression.
     * @return TokenInterface[] The created tokenizer instance.
     */
    public function runTokenizer(string $expression): array;
}
