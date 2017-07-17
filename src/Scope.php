<?php
/**
 * Mathr\Parser class file.
 * @package Parser
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
	protected $var;
	protected $func;
	
	protected $stack;
	protected $depth;
	protected $limit;
	
	public function __construct(int $limit = 20)
	{
		$this->var = [];
		$this->func = [];
		
		$this->stack = new SplStack;
		$this->limit = $limit;
		$this->depth = 0;
	}
	
	public function assign(AbstractNode $target, AbstractNode $value)
	{
		if($target instanceof VariableNode)
			$this->var[$target->value()] = $value;
		
		elseif($target instanceof FunctionDeclNode)
			$this->func[$target->value()][] = [$target, $value];
	}
	
	public function has(AbstractNode $node) : bool
	{
		return
			$node instanceof VariableNode && isset($this->var[$node->value()]) ||
		    $node instanceof FunctionDeclNode && isset($this->func[$node->value()])
		;
	}
	
	public function retrieve(AbstractNode $node)
	{
		if(!$this->has($node))
			return $node instanceof VariableNode
				? $this->stackRetrieve($node)
				: false;
		
		if($node instanceof VariableNode)
			return $this->var[$node->value()] ?? false;
		
		if($node instanceof FunctionDeclNode)
			return $this->func[$node->value()] ?? false;
		
		return false;
	}

	public function stackDown()
	{
		if($this->stack->isEmpty())
			throw new StackFrameException;
		
		$count = $this->stack->pop();
		
		for($i = 0; $i < $count; ++$i)
			$this->stack->pop();
		
		--$this->depth;
	}
	
	public function stackRetrieve(VariableNode $node)
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
	
	public function stackUp(array $args)
	{
		if($this->depth > $this->limit)
			throw new StackOverflowException;
		
		foreach(array_reverse($args) as $arg)
			$this->stack->push($arg);
		
		$this->stack->push(count($args));
		++$this->depth;
	}
	
}
