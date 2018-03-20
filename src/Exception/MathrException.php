<?php
/**
 * Mathr\Exception\MathrException class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Exception;

class MathrException extends \Exception
{
	/**
	 * MathrException constructor.
	 * Builds a new MathrExpression, creating an informing message.
	 * @param string $format Message format.
	 * @param array ...$params Message parameters
	 */
	public function __construct(string $format = "", ...$params)
	{
		parent::__construct(sprintf($format, ...$params));
	}
	
	/**
	 * Creates a no function exception.
	 * @param string $decl Function declaration.
	 * @return MathrException The created exception.
	 */
	public static function noFunction(string $decl)
	{
		return new self("The function '%s' could not be created!", $decl);
	}
}
