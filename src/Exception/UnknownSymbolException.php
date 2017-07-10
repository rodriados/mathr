<?php
/**
 * Mathr\Exception\UnknownSymbolException class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Throwable;

class UnknownSymbolException
	extends \Exception
{
	public function __construct(string $name)
	{
		parent::__construct("Unknown symbol: {$name}");
	}
}
