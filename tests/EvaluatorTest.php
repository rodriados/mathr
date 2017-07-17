<?php

use Mathr\Evaluator;
use PHPUnit\Framework\TestCase;

final class EvaluatorTest extends TestCase
{
	public function testCanBeCreated()
	{
		$this->assertInstanceOf(
			Evaluator::class,
			new Evaluator
		);
	}
	
	public function testCanDeclareVariable()
	{
		$eval = new Evaluator;
		$eval->evaluate("a = 1024");
		
		$this->assertEquals(
			1024,
			$eval->evaluate("a")->value()
		);
	}
	
	public function testCanDeclareFunction()
	{
		$eval = new Evaluator;
		$eval->evaluate("f(x) = (x + 1) ^ 2");
		
		$this->assertEquals(
			16,
			$eval->evaluate("f(3)")->value()
		);
	}
	
	public function testCanDeclareRecursiveFunction()
	{
		$eval = new Evaluator;
		$eval->evaluate("f(0) = 0");
		$eval->evaluate("f(1) = 1");
		$eval->evaluate("f(x) = f(x - 1) + f(x - 2)");
		
		$this->assertEquals(
			55,
			$eval->evaluate("f(10)")->value()
		);
	}
	
}
