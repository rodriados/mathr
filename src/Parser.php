<?php
/**
 * Mathr\Parser class file.
 * @package Parser
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Exception\MismatchedParenthesesException;
use SplStack;
use Mathr\Parser\Token;
use Mathr\Parser\Tokenizer;
use Mathr\Exception\UnexpectedTokenException;

class Parser
{
	protected static $precedence = [
		'^' => 4,
		'*' => 3,
		'/' => 3,
		'+' => 2,
		'-' => 2,
	    '=' => 0
	];
	
	/**
	 * @var SplStack
	 */
	private $stack;
	
	/**
	 * @var Tokenizer
	 */
	private $tokenizer;
	
	/**
	 * @var Expression
	 */
	private $expression;
	
	/**
	 * @var bool
	 */
	private $expectingOperator;
	
	/**
	 * @var bool
	 */
	private $inFunction;
	
	public function __construct()
	{
		$this->expectingOperator = false;
		$this->inFunction = false;
	}
	
	public function parse(string $expr)
	{
		$expr = explode(';', trim($expr));
		
		foreach($expr as $command)
			$exprs[] = $this->doParse(trim($command));
		
		return $exprs ?? [];
	}
	
	private function doParse(string $expr) : Expression
	{
		$this->tokenizer = new Tokenizer($expr);
		$this->expression = new Expression;
		$this->stack = new SplStack;
		
		foreach($this->tokenizer as $token)
			$this->receive($token);

		while(!$this->stack->isEmpty()) {
			if($this->stack->top()->is(Token::PARENTHESES|Token::LEFT)) {
				throw new MismatchedParenthesesException;
			}
			
			$this->expression->push($this->stack->pop());
		}
		
		return $this->expression;
	}
	
	private function receive(Token $token)
	{
		if($token->is(Token::NUMBER)) {
			if($this->expectingOperator)
				throw new UnexpectedTokenException;
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->expression->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		if($token->is(Token::VARIABLE)) {
			if($this->expectingOperator)
				$this->receiveMult();
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->expression->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		if($token->is(Token::FUNCTION)) {
			if($this->expectingOperator)
				$this->receiveMult();
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->stack->push($token);
			$this->stack->push(Token::number('0'));
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
		
		if($token->is(Token::OPERATOR)) {
			if(!$this->expectingOperator || $this->inFunction)
				throw new UnexpectedTokenException;
			
			while(
				!$this->stack->isEmpty() &&
				$this->stack->top()->is(Token::OPERATOR) &&
				self::$precedence[$this->stack->top()->data()] >= self::$precedence[$token->data()] &&
			    !$token->is(Token::LEFT)
			) {
				$this->expression->push($this->stack->pop());
			}
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		if($token->is(Token::PARENTHESES|Token::LEFT)) {
			if($this->expectingOperator)
				$this->receiveMult();
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		if($token->is(Token::PARENTHESES|Token::RIGHT)) {
			while(
				!$this->stack->isEmpty() &&
			    !$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->expression->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw new MismatchedParenthesesException;
			
			$popped = $this->stack->pop();
			
			if($popped->is(Token::FUNCTION))
				$this->expression->push($popped);
			
			$this->expectingOperator = true;
			$this->inFunction = false;
			return;
		}
		
		if($token->is(Token::COMMA)) {
			if(!$this->expectingOperator || $this->inFunction)
				throw new UnexpectedTokenException;
			
			while(
				!$this->stack->isEmpty() &&
				!$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->expression->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw new UnexpectedTokenException;
			
			$this->stack->push($this->expression->pop());
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
	}
	
	private function incrementFunction()
	{
		$value = (int)$this->stack->pop()->data();
		$this->stack->push(Token::number($value + 1));
		$this->inFunction = false;
	}
	
	private function receiveMult()
	{
		$this->receive(Token::operator('*', Token::RIGHT));
	}
	
}
