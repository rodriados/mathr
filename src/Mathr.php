<?php
/**
 * Mathr\Mathr class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

class Mathr
{
	/**
	 * Parser instance.
	 * @var Parser Instance responsible for parsing the expressions.
	 */
	protected $parser;
	
	/**
	 * Scope instance.
	 * @var Scope Instance responsible for storing all variables and functions.
	 */
	protected $scope;
	
	/**
	 * Mathr constructor.
	 * This constructor simply initializes the scope and parser instances
	 * needed for evaluating mathematical expressions.
	 */
	public function __construct()
	{
		$this->parser = new Parser;
		$this->scope = new Scope;
	}
}
