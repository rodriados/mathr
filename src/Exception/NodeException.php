<?php
/**
 * Mathr\Exception\NodeException class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Mathr\Token;

class NodeException extends MathrException
{
	/**
	 * Creates an invalid number node exception.
	 * @param mixed $value The unknown value number.
	 * @return NodeException
	 */
	public static function invalidNumber($value): self
	{
		return new self("The string '".$value."' is not numeric.");
	}
	
	/**
	 * Creates an incompatible function definition exception.
	 * @param string $value The incompatible function.
	 * @return NodeException
	 */
	public static function incompatibleDefinition($value): self
	{
		return new self("No compatible definition of function '".$value."' was found.");
	}
	
	/**
	 * Creates an unknown symbol node exception.
	 * @param mixed $value The unknown value.
	 * @return NodeException
	 */
	public static function unknownSymbol($value): self
	{
		return new self("The symbol '".$value."' is unknown.");
	}
}