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
use Mathr\Node\OperatorNode;
use Mathr\Node\VariableNode;
use Mathr\Node\FunctionNode;
use Mathr\Node\NodeInterface;
use Mathr\Exception\ScopeException;

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
	public function __construct(int $limit = 20)
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
		if(isset($this->var[$name->getValue()]))
			return $this->var[$name->getValue()];
		
		if($value = $this->peek($name))
			return $value;
		
		if(in_array($name->getValue(), Native::CONSTANT_LIST))
			return new NumberNode(Native::CONSTANT[$name->getValue()]);
		
		return new NullNode;
	}
	
	/**
	 * Stores a variable into the scope.
	 * @param VariableNode $name The variable name.
	 * @param NodeInterface $value The variable value.
	 */
	public function setVariable(VariableNode $name, NodeInterface $value)
	{
		$this->var[$name->getValue()] = $value;
	}
	
	/**
	 * Retrieves a stored or global function.
	 * @param FunctionNode $name The function being retrieved.
	 * @return NodeInterface|array The search result.
	 */
	public function getFunction(FunctionNode $name)
	{
		$name = $name->getValue();
		
		if(isset($this->func[$name]))
			return $this->func[$name];
		
		if(in_array($name, Native::FUNCTION_LIST))
			return new OperatorNode($name);
		
		return new NullNode;
	}
	
	/**
	 * Stores a function definition into the scope.
	 * @param FunctionNode $decl The function declaration.
	 * @param NodeInterface $block The function block.
	 */
	public function setFunction(FunctionNode $decl, NodeInterface $block)
	{
		$this->func[$decl->getValue()][] = [$decl, $block];
	}
	
	/**
	 * Pops a frame from execu$nametion stack.
	 * @throws ScopeException There are no frames in stack.
	 */
	public function pop()
	{
		if($this->stack->isEmpty())
			throw ScopeException::stackFrame();
		
		$count = $this->stack->pop();
		
		for($i = 0; $i < $count; ++$i)
			$this->stack->pop();
		
		--$this->depth;
	}
	
	/**
	 * Peeks at value in current stack frame.
	 * @param Node $node Name to be peeked from stack.
	 * @return bool|mixed Peeked stack value.
	 * @throws ScopeException
	 */
	public function peek(Node $node)
	{
		if($this->stack->isEmpty())
			return false;
		
		if(!is_numeric($name = substr($node->getValue(), 1)))
			return false;
		
		$name = intval($name);
		
		if($name > $this->stack->top())
			throw ScopeException::segmentationFault();
		
		return $this->stack[$name];
	}
	
	/**
	 * Pushes a frame to execution stack.
	 * @param array $args Arguments to be pushed to stack.
	 * @throws ScopeException
	 */
	public function push(array $args)
	{
		if($this->depth > $this->limit)
			throw ScopeException::stackOverflow();
		
		foreach(array_reverse($args) as $arg)
			$this->stack->push($arg);
		
		$this->stack->push(count($args));
		++$this->depth;
	}
}
