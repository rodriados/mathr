<?php

use Mathr\Evaluator;
use Mathr\Exception\StackOverflowException;
use PHPUnit\Framework\TestCase;

final class EvaluatorTest extends TestCase
{
	protected $eval;
	
	public function setUp()
	{
		$this->eval = new Evaluator;
	}
	
	public function testCanBeCreated()
	{
		$this->assertInstanceOf(
			Evaluator::class,
			$this->eval
		);
	}
	
	public function testCanDeclareVariable()
	{
		$this->eval->evaluate("a = 1024");
		$this->eval->evaluate("b = 10");
		
		$this->assertEquals(
			$this->eval->evaluate("a")->value(),
			1024
		);
		
		$this->assertEquals(
			$this->eval->evaluate("b")->value(),
			10
		);
	}
	
	public function testCanDeclareFunction()
	{
		$this->eval->evaluate("f(x) = (x + 1) ^ 2");
		
		$this->assertEquals(
			$this->eval->evaluate("f(3)")->value(),
			16
		);
	}
	
	public function testCanDeclareRecursiveFunction()
	{
		$this->eval->evaluate("fib(0) = 0");
		$this->eval->evaluate("fib(1) = 1");
		$this->eval->evaluate("fib(x) = fib(x - 1) + fib(x - 2)");
		
		$this->assertEquals(
			$this->eval->evaluate("fib(10)")->value(),
			55
		);
	}
	
	public function testLimitsRecursionDepth()
	{
		$this->testCanDeclareRecursiveFunction();
		$this->expectException(StackOverflowException::class);
		$this->eval->evaluate("fib(30)");
	}
	
	public function testCanImplicitlyMultiply()
	{
		$this->assertEquals(
			$this->eval->evaluate("3(4+2)")->value(),
			18
		);
	}
	
	public function testKnowsOperatorPrecedence()
	{
		$this->assertEquals(
			$this->eval->evaluate("3+4*5")->value(),
			23
		);
	}
	
	public function testCanUseVariableAsFunctionParameter()
	{
		$this->testCanDeclareVariable();
		$this->testCanDeclareFunction();
		$this->testCanDeclareRecursiveFunction();
		
		$this->assertEquals(
			$this->eval->evaluate("fib(b)")->value(),
			55
		);
		
		$this->assertEquals(
			$this->eval->evaluate("f(a)")->value(),
			1050625
		);
	}
	
	public function testKnowsSomeMathConstants()
	{
		$this->assertEquals(
			$this->eval->evaluate("pi")->value(),
			M_PI
		);
		
		$this->assertEquals(
			$this->eval->evaluate("e")->value(),
			M_E
		);
	}
	
	public function testKnowsSomeCommomFunctions()
	{
		$this->assertEquals(
			$this->eval->evaluate("sqrt(81)")->value(),
			9
		);
		
		$this->assertEquals(
			$this->eval->evaluate("log(8,2)")->value(),
			3
		);
	}
	
	public function testCanUseVariableWithCommomFunctions()
	{
		$this->testCanDeclareVariable();
		
		$this->assertEquals(
			$this->eval->evaluate("log(a,2)")->value(),
			10
		);
	}
	
}
