<?php
/**
 * Mathr\Node\NullNode class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr\Node;

use Mathr\Node;
use Mathr\Scope;
use Mathr\Token;

class NullNode extends Node
{
	/**
	 * NullNode constructor.
	 */
	public function __construct()
	{
		parent::__construct(null);
	}
	
	/**
	 * @inheritdoc
	 */
	public function evaluate(Scope $scope): Node
	{
		return $this;
	}
	
	/**
	 * @inheritdoc
	 */
	public static function fromToken(Token $token, \SplStack $stack): Node
	{
		return new static;
	}
	
}
