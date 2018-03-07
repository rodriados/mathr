<?php
/**
 * Mathr\Parser class file.
 * @package Mathr
 * @author Rodrigo Siqueira <rodriados@gmail.com>
 * @license MIT License
 * @copyright 2017-2018 Rodrigo Siqueira
 */
namespace Mathr;

use Mathr\Exception\ParserException;

class Parser
{
	/**
	 * Operators precedence.
	 * @var array Informs which operators should execute before the others.
	 */
	protected static $prec = [
		'0+' => 10,
		'0-' => 10,
		'^' => 4,
		'*' => 3,
		'/' => 3,
		'+' => 2,
		'-' => 2,
		'=' => 0
	];
	
	/**
	 * Parsing operator stack.
	 * @var \SplStack All operator are stacked before being sent to output.
	 */
	private $stack;
	
	/**
	 * Output queue.
	 * @var Expression Stores operators and values in RPN format.
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
	 * Clears out any previous parser execution.
	 */
	public function clear()
	{
		$this->stack = new \SplStack;
		$this->output = new Expression;
		$this->expectingOperator = false;
		$this->inFunction = false;
	}
	
	/**
	 * Parses the expression and produces an output tree.
	 * @param string $expr Expression to be parsed.
	 * @return Expression The Revese Polish Notation of the given expression.
	 * @throws ParserException
	 */
	public function parse(string $expr): Expression
	{
		$this->clear();

		foreach(new Tokenizer($expr) as $token)
			$this->receive($token);

		while(!$this->stack->isEmpty()) {
			if($this->stack->top()->is(Token::PARENTHESES | Token::LEFT))
				throw ParserException::mismatched($this->stack->top());
		
			$this->output->push($this->stack->pop());
		}
		
		return $this->output;
	}
	
	/**
	 * Process a received token.
	 * @param Token $token Received token.
	 * @throws ParserException
	 */
	private function receive(Token $token)
	{
		/*
		 * If the current token is a NUMBER:
		 * - If an operator is expected, we have an error;
		 * - If we are in a function context, the function must be incremented;
		 * - Push the token to output;
		 */
		if($token->is(Token::NUMBER)) {
			if($this->expectingOperator)
				throw ParserException::unexpected($token);
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->output->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		/*
		 * If the current token is a VARIABLE name:
		 * - If an operator is expected, an implicit multiplication is done;
		 * - If we are in a function context, the function must be incremented;
		 * - Push the token to output;
		 */
		if($token->is(Token::VARIABLE)) {
			if($this->expectingOperator)
				$this->receiveMult($token->getPosition());
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->output->push($token);
			$this->expectingOperator = true;
			return;
		}
		
		/*
		 * If the current token is a FUNCTION name:
		 * - If an operator is expected, an implicit multiplication is done;
		 * - If we are in a function context, the function must be incremented;
		 * - Create a function context in the stack;
		 */
		if($token->is(Token::FUNCTION)) {
			if($this->expectingOperator)
				$this->receiveMult($token->getPosition());
			
			if($this->inFunction)
				$this->incrementFunction();
			
			$this->stack->push($token);
			$this->stack->push(Token::number(0, $token->getPosition()));
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
		
		/*
		 * If the current token is an OPERATOR:
		 * - Check whether the operator is unary and transform it if needed;
		 * - Push from stack to output all operator with higher precedence;
		 * - Push the token to stack;
		 */
		if($token->is(Token::OPERATOR)) {
			if(!$this->expectingOperator && in_array($token->getData(), ['+', '-']))
				$token = Token::operator("0{$token}", Token::RIGHT, $token->getPosition());
			elseif(!$this->expectingOperator || $this->inFunction)
				throw ParserException::unexpected($token);
			
			while(
				!$this->stack->isEmpty() &&
				$this->stack->top()->is(Token::OPERATOR) &&
				self::$prec[$this->stack->top()->getData()] >= self::$prec[$token->getData()] &&
				!$token->is(Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		/*
		 * If the current token is a LEFT PARENTHESES:
		 * - If an operator is expected, an implicit multiplication is done;
		 * - Push the token to stack;
		 */
		if($token->is(Token::PARENTHESES|Token::LEFT)) {
			if($this->expectingOperator)
				$this->receiveMult($token->getPosition());
			
			$this->stack->push($token);
			$this->expectingOperator = false;
			return;
		}
		
		/*
		 * If the current token is a RIGHT PARENTHESES:
		 * - While operator in stack is not a left parentheses, push to output;
		 * - If a left parentheses is not found, we have an error;
		 * - If the operator in stack is a function, push to output;
		 */
		if($token->is(Token::PARENTHESES|Token::RIGHT)) {
			while(
				!$this->stack->isEmpty() &&
				!$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw ParserException::mismatched($token);
			
			$popped = $this->stack->pop();
			
			if($popped->is(Token::FUNCTION))
				$this->output->push($popped);
			
			$this->expectingOperator = true;
			$this->inFunction = false;
			return;
		}
		
		/*
		 * If the current token is a COMMA:
		 * - If an operator is not expected, we have an error;
		 * - If we are in a function context, we have an error;
		 * - While operator in stack is not a left parentheses, push to output;
		 * - If a left parentheses is not found, we have an error;
		 * - Pop function count from output and push to stack;
		 */
		if($token->is(Token::COMMA)) {
			if(!$this->expectingOperator || $this->inFunction)
				throw ParserException::unexpected($token);
			
			while(
				!$this->stack->isEmpty() &&
				!$this->stack->top()->is(Token::PARENTHESES|Token::LEFT)
			) {
				$this->output->push($this->stack->pop());
			}
			
			if($this->stack->isEmpty())
				throw ParserException::unexpected($token);
			
			$this->stack->push($this->output->pop());
			$this->expectingOperator = false;
			$this->inFunction = true;
			return;
		}
	}
	
	/**
	 * Informs the stacked function it's got one more parameter.
	 * @throws ParserException
	 */
	private function incrementFunction()
	{
		for($i = 0; $i < $this->stack->count(); ++$i) {
			if(!$this->stack[$i]->is(Token::NUMBER))
				continue;
			
			$value = (int)$this->stack[$i]->getData();
			$this->stack[$i] = Token::number($value + 1, -1);
			$this->inFunction = false;
			return;
		}
		
		throw ParserException::unknown();
	}
	
	/**
	 * Sends the parser an implicit multiplication operator.
	 * @param int $position Position that triggered implicit operator.
	 * @throws ParserException
	 */
	private function receiveMult(int $position)
	{
		$this->receive(Token::operator('*', Token::RIGHT, $position));
	}
	
}
