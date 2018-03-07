<?php
/**
 * Mathr\Scope class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Node\NullNode;
use Mathr\Node\NumberNode;
use Mathr\Node\NodeInterface;
use Mathr\Exception\ScopeException;
use Mathr\Node\OperatorNode;

class Scope
{
	/**
	 * List of declared variables.
	 * @var NodeInterface[] Stores all declared variables in scope.
	 */
	protected $var;
	
	/**
	 * List of declared functions.
	 * @var NodeInterface[] Stores all declared functions in scope.
	 */
	protected $func;
	
	/**
	 * Execution stack.
	 * @var \SplStack Creation of stack frames while executing functions.
	 */
	protected $stack;
	
	/**
	 * Function calls depth.
	 * @var int Counts the current function calls depth.
	 */
	protected $depth;
	
	/**
	 * The limit for function calls depth.
	 * @var int Sets a rigid limit for function calls depth or recursion.
	 */
	protected $limit;
	
	/**
	 * Scope constructor.
	 * @param int $limit Limit for function calls depth.
	 */
	public function __construct(int $limit = 100)
	{
		$this->var = [];
		$this->func = [];
		
		$this->stack = new \SplStack;
		$this->limit = $limit;
		$this->depth = 0;
	}
	
	/**
	 * Retrieves the value of a stored or global variable.
	 * @param Node $name The variable being retrieved.
	 * @return NodeInterface The search result.
	 */
	public function getVariable(Node $name): NodeInterface
	{
		$name = $name->getValue();
		
		if(isset($this->var[$name]))
			return $this->var[$name];
		
		if(in_array($name, Native::CONSTANT_LIST))
			return new NumberNode(Native::CONSTANT[$name]);
		
		return new NullNode;
	}
	
	/**
	 * Stores a value into a variable.
	 * @param Node $name The variable name.
	 * @param NodeInterface $value The variable value.
	 */
	public function setVariable(Node $name, NodeInterface $value)
	{
		$this->var[$name->getValue()] = $value;
	}
	
	/**
	 * Retrieves a stored or global function.
	 * @param Node $name The function being retrieved.
	 * @return NodeInterface The search result.
	 */
	public function getFunction(Node $name): NodeInterface
	{
		$name = $name->getValue();
		
		if(isset($this->func[$name]))
			return $this->func[$name];
		
		if(in_array($name, Native::FUNCTION_LIST))
			return new OperatorNode($name);
		
		return new NullNode;
	}
}
