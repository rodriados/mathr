<?php
/**
 * Mathr\Exception\ScopeException class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Mathr\Token;

class ScopeException extends MathrException
{
	/**
	 * Creates an unknown variable exception.
	 * @param Token $name The unknown variable.
	 * @return ScopeException
	 */
	public static function unknownVariable(Token $name): self
	{
		return new self(sprintf("The variable '%s' is not known.",
			$name->getData()
		));
	}
	
	/**
	 * Creates an unknown function exception.
	 * @param Token $name The unknown function.
	 * @return ScopeException
	 */
	public static function unknownFunction(Token $name): self
	{
		return new self(sprintf("The function '%s' is not known.",
                $name->getData()
        ));
	}
	
	/**
	 * Creates a stack frame exception.
	 * @return ScopeException
	 */
	public static function stackFrame(): self
	{
		return new self("The scope's stack is empty and cannot be popped.");
	}
	
	/**
	 * Creates a segmentation fault exception.
	 * @return ScopeException
	 */
	public static function segmentationFault(): self
	{
		return new self("A segmentation fault occurred.");
	}
	
	/**
	 * Creates a segmentation fault exception.
	 * @return ScopeException
	 */
	public static function stackOverflow(): self
	{
		return new self("A stack overflow occurred.");
	}
}
