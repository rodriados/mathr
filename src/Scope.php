<?php
/**
 * Mathr\Parser class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use SplStack;
use Mathr\Node\AbstractNode;
use Mathr\Node\VariableNode;
use Mathr\Node\FunctionDeclNode;
use Mathr\Exception\StackFrameException;
use Mathr\Exception\StackOverflowException;
use Mathr\Exception\SegmentationFaultException;

class Scope
{
	/**
	 * List of declared variables.
	 * @var AbstractNode[] Stores all declared variables in scope.
	 */
	protected $var;
	
	/**
	 * List of declared functions.
	 * @var AbstractNode[] Stores all declared functions in scope.
	 */
	protected $func;
	
	/**
	 * Execution stack.
	 * @var SplStack Creation of stack frames while executing functions.
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
		
		$this->stack = new SplStack;
		$this->limit = $limit;
		$this->depth = 0;
	}
	
	/**
	 * Assigns a value or block to a variable or function.
	 * @param AbstractNode $target Name to be assigned.
	 * @param AbstractNode $value Value or block being assigned.
	 */
	public function assign(AbstractNode $target, AbstractNode $value)
	{
		if($target instanceof VariableNode)
			$this->var[$target->value()] = $value;
		
		elseif($target instanceof FunctionDeclNode)
			$this->func[$target->value()][] = [$target, $value];
	}
	
	/**
	 * Checks whether a name has been previously declared in this scope.
	 * @param AbstractNode $node Name to be checked.
	 * @return bool Is the name declared?
	 */
	public function has(AbstractNode $node) : bool
	{
		return
			$node instanceof VariableNode && isset($this->var[$node->value()]) ||
		    $node instanceof FunctionDeclNode && isset($this->func[$node->value()])
		;
	}
	
	/**
	 * Retrieves a declared variable or function from the scope.
	 * @param AbstractNode $node Name to be retrieved.
	 * @return bool|mixed Value attached to retrieved name.
	 */
	public function retrieve(AbstractNode $node)
	{
		if(!$this->has($node))
			return $node instanceof VariableNode
				? $this->peek($node)
				: false;
		
		if($node instanceof VariableNode)
			return $this->var[$node->value()] ?? false;
		
		if($node instanceof FunctionDeclNode)
			return $this->func[$node->value()] ?? false;
		
		return false;
	}
	
	/**
	 * Pops a frame from execution stack.
	 * @throws StackFrameException There are no frames in stack.
	 */
	public function pop()
	{
		if($this->stack->isEmpty())
			throw new StackFrameException;
		
		$count = $this->stack->pop();
		
		for($i = 0; $i < $count; ++$i)
			$this->stack->pop();
		
		--$this->depth;
	}
	
	/**
	 * Peeks at value in current stack frame.
	 * @param VariableNode $node Name to be peeked from stack.
	 * @return bool|mixed Peeked stack value.
	 * @throws SegmentationFaultException
	 */
	public function peek(VariableNode $node)
	{
		if($this->stack->isEmpty())
			return false;
		
		if(!is_numeric($name = substr($node->value(), 1)))
			return false;
		
		$name = intval($name);
		
		if($name > $this->stack->top())
			throw new SegmentationFaultException;
		
		return $this->stack[$name];
	}
	
	/**
	 * Pushes a frame to execution stack.
	 * @param array $args Arguments to be pushed to stack.
	 * @throws StackOverflowException
	 */
	public function push(array $args)
	{
		if($this->depth > $this->limit)
			throw new StackOverflowException;
		
		foreach(array_reverse($args) as $arg)
			$this->stack->push($arg);
		
		$this->stack->push(count($args));
		++$this->depth;
	}
	
}
