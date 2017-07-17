<?php
/**
 * Mathr\Exception\NoCompatibleDefinitionFoundException class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Exception;

class NoCompatibleDefinitionFoundException
	extends \Exception
{
	public function __construct(string $name)
	{
		parent::__construct("Function name: {$name}");
	}
}
