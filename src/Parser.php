<?php
/**
 * Mathr\Parser class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use SplQueue as Queue;
use SplStack as Stack;
use Mathr\Parser\Token;
use Mathr\Parser\Tokenizer;
use Mathr\Exception\ParseException;
use Mathr\Exception\UnexpectedTokenException;
use Mathr\Exception\MismatchedParenthesesException;

class Parser
{
	/**
	 * Operators precedence.
	 * @var array Informs which operators should execute before the others.
	 */
	protected static $precedence = [
		'0+' => 6,
		'0-' => 6,
		'^' => 4,
		'*' => 3,
		'/' => 3,
		'+' => 2,
		'-' => 2,
	    '=' => 0
	];
	
	/**
	 * Parsing operator stack.
	 * @var Stack All operator are stacked before being sent to output.
	 */
	private $stack;
	
	/**
	 * Token iterator.
	 * @var Tokenizer Extracts and iterates over the tokens of given expression.
	 */
	private $tokenizer;
	
	/**
	 * Output queue.
	 * @var Queue Stores operators and values in RPN format.
	 */
	private $output;
	
	/**
	 * Informs whether an operator is expected.
	 * @var bool Checks if an operator should be the next token.
	 */
	private $expectingOperator;
	
	/**
	 * Informs whether the token is inside a function.
	 * @var bool Checks if the function should be informed about parameter.
	 */
	private $inFunction;
	
	/**
	 * Parser constructor.
	 */
	public function __construct()
	{
		$this->expectingOperator = false;
		$this->inFunction = false;
	}
	
	/**
	 * Parses the expression and produces an output tree.
	 * @param string $expr Expression to be parsed.
	 * @return Tree Execution tree.
	 * @throws MismatchedParenthesesException
	 */
	public function parse(string $expr) : Tree
	{
		$this->tokenizer = new Tokenizer($expr);
		$this->output = new Queue;
		$this->stack = new Stack;
		$this->expectingOperator = false;
		$this->inFunction = false;
		
		foreach($this->tokenizer as $position => $token)
			$this->receive($token, $position);

		while(!$this->stack->isEmpty()) {
			if($this->stack->top()->is(Token::PARENTHESES|Token::LEFT)) {
				throw new MismatchedParenthesesException($this->stack->top(),-1);
			}
			
			$this->output->push($this->stack->pop());
		}
		
		return new Tree($this->output);
	}
	
	/**
	 * Process a received token.
	 * @param Token $token Received token.
	 * @param int $position Expression iterator position.
	 * @throws MismatchedParenthesesException
	 * @throws UnexpectedTokenException
	 */
	private function receive(Token $token, int $position)
	{
		if($token->is(Token::NUMBER)) {
			if($this->expectingOperator)
				throw new UnexpectedTokenException($token, $position);
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->output->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		if($token->is(Token::VARIABLE)) {
			if($this->expectingOperator)
				$this->receiveMult($position);
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->output->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		if($token->is(Token::FUNCTION)) {
			if($this->expectingOperator)
				$this->receiveMult($position);
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->stack->push($token);
			$this->stack->push(Token::number(0));
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
		
		if($token->is(Token::OPERATOR)) {
			if(!$this->expectingOperator && in_array($token->data(), ['+','-']))
				$token = Token::operator("0{$token}", Token::RIGHT);
			elseif(!$this->expectingOperator || $this->inFunction)
				throw new UnexpectedTokenException($token, $position);
			
			while(
				!$this->stack->isEmpty() &&
				$this->stack->top()->is(Token::OPERATOR) &&
				self::$precedence[$this->stack->top()->data()] >= self::$precedence[$token->data()] &&
			    !$token->is(Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		if($token->is(Token::PARENTHESES|Token::LEFT)) {
			if($this->expectingOperator)
				$this->receiveMult($position);
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		if($token->is(Token::PARENTHESES|Token::RIGHT)) {
			while(
				!$this->stack->isEmpty() &&
			    !$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw new MismatchedParenthesesException($token, $position);
			
			$popped = $this->stack->pop();
			
			if($popped->is(Token::FUNCTION))
				$this->output->push($popped);
			
			$this->expectingOperator = true;
			$this->inFunction = false;
			return;
		}
		
		if($token->is(Token::COMMA)) {
			if(!$this->expectingOperator || $this->inFunction)
				throw new UnexpectedTokenException($token, $position);
			
			while(
				!$this->stack->isEmpty() &&
				!$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw new UnexpectedTokenException($token, $position);
			
			$this->stack->push($this->output->pop());
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
	}
	
	/**
	 * Informs the stacked function it's got one more parameter.
	 * @throws ParseException There are no functions stacked.
	 */
	private function incrementFunction()
	{
		for($i = 0; $i < $this->stack->count(); ++$i) {
			if(!$this->stack[$i]->is(Token::NUMBER))
				continue;
			
			$value = (int)$this->stack[$i]->data();
			$this->stack[$i] = Token::number($value + 1);
			$this->inFunction = false;
			return;
		}
		
		throw new ParseException;
	}
	
	/**
	 * Sends the parser an implicit multiplication operator.
	 * @param int $position Position that triggered implicit operator.
	 */
	private function receiveMult(int $position)
	{
		$this->receive(Token::operator('*', Token::RIGHT), $position);
	}
	
}
