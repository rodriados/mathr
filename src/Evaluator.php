<?php
/**
 * Mathr\Parser class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

class Evaluator
{
	protected $parser;
	protected $scope;
	
	public function __construct()
	{
		$this->parser = new Parser;
		$this->scope = new Scope;
	}
	
	public function parse(string $expr)
	{
		$expr = explode(';', $expr);
		$return = [];
		
		foreach($expr as $command)
			$return[] = $this->parser
				->parse(trim($command))
				->evaluate($this->scope);
		
		return $return;
	}
	
}
