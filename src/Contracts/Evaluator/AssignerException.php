<?php
/**
 * An exception found when assigning a binding in memory.
 * @package Mathr\Contracts\Evaluator
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @copyright 2017-present Rodrigo Siqueira
 * @license MIT License
 */
namespace Mathr\Contracts\Evaluator;

use Mathr\Contracts\MathrException;

/**
 * Represents an exception thrown while an assignment is evaluated.
 * @package Mathr\Contracts\Evaluator
 */
class AssignerException extends MathrException
{
    /**
     * An assignment to an invalid binding was tried.
     * @param NodeInterface $node The invalid binding instance.
     * @return static The exception to be thrown.
     */
    public static function assignmentIsInvalid(NodeInterface $node): static
    {
        return new static(
            sprintf("Cannot assign value to '%s'", $node->strRepr())
        );
    }

    /**
     * An assignment with invalid binding parameter types was tried.
     * @param NodeInterface $node The invalid binding instance.
     * @return static The exception to be thrown.
     */
    public static function bindingParamsAreInvalid(NodeInterface $node): static
    {
        return new static(
            sprintf("Invalid binding parameters types in '%s'", $node->strRepr())
        );
    }
}
