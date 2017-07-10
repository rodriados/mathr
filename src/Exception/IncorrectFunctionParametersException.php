<?php
/**
 * Mathr\Exception\IncorrectFunctionParametersException class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Throwable;

class IncorrectFunctionParametersException
	extends \Exception
{
	public function __construct(string $name)
	{
		parent::__construct("Incorrect parameters for function: {$name}");
	}
}
