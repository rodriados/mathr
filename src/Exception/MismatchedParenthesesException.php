<?php
/**
 * Mathr\Exception\MismatchedParenthesesException class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr\Exception;

use Mathr\Parser\Token;

class MismatchedParenthesesException
	extends \Exception
{
	public $token;
	public $position;

	public function __construct(Token $token, int $position) {
		parent::__construct("Mismatched {$token} in position {$position}");
	}
}
