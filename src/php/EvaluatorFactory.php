<?php

namespace Viamo\Floip;

use Viamo\Floip\Parser;
use Viamo\Floip\Evaluator;
use Viamo\Floip\Evaluator\BoolNodeEvaluator;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Math;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\EscapeNodeEvaluator;
use Viamo\Floip\Evaluator\ConcatenationNodeEvaluator;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\RouterTest;
use Viamo\Floip\Evaluator\NullNodeEvaluator;

class EvaluatorFactory
{
	public function newInstance() {
		$eval = new Evaluator(new Parser);
		foreach ($this->getNodeEvaluators() as $nodeHandler) {
			$eval->addNodeEvaluator($nodeHandler);
		}

		return $eval;
	}

	public function create() {
		return $this->newInstance();
	}

	protected function methodNodeInstance()
	{
		$eval = new MethodNodeEvaluator;
		foreach ($this->getMethodHandlers() as $methodHandler) {
			$eval->addHandler($methodHandler);
		}

		return $eval;
	}
	
	protected function getMethodHandlers()
	{
		return [
			new DateTime,
			new Excellent,
			new Logical,
			new Math,
			new Text,
			new ArrayHandler,
			new RouterTest,
		];
	}

	protected function getNodeEvaluators()
	{
		return [
			new LogicNodeEvaluator,
			new MemberNodeEvaluator,
			new EscapeNodeEvaluator,
			new ConcatenationNodeEvaluator,
			new MathNodeEvaluator,
			new NullNodeEvaluator,
			new MethodNodeEvaluator,
			new BoolNodeEvaluator,
			$this->methodNodeInstance()
		];
	}
}
