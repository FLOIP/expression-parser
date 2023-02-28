<?php

namespace Viamo\Floip;

use Viamo\Floip\Evaluator\BoolNodeEvaluator;
use Viamo\Floip\Evaluator\ConcatenationNodeEvaluator;
use Viamo\Floip\Evaluator\EscapeNodeEvaluator;
use Viamo\Floip\Evaluator\LogicNodeEvaluator;
use Viamo\Floip\Evaluator\MathNodeEvaluator;
use Viamo\Floip\Evaluator\MemberNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\ArrayHandler;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\DateTime;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Logical;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\MatchTest;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Math;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Evaluator\NullNodeEvaluator;

class EvaluatorFactory {

	public function newInstance(): Evaluator {
		$eval = new Evaluator(new Parser);
		foreach ($this->getNodeEvaluators() as $nodeHandler) {
			$eval->addNodeEvaluator($nodeHandler);
		}

		return $eval;
	}

	public function create(): Evaluator {
		return $this->newInstance();
	}

	protected function methodNodeInstance(): MethodNodeEvaluator {
		$eval = new MethodNodeEvaluator;
		foreach ($this->getMethodHandlers() as $methodHandler) {
			$eval->addHandler($methodHandler);
		}

		return $eval;
	}
	
	protected function getMethodHandlers(): array {
		return [
			new DateTime,
			new Excellent,
			new Logical,
			new Math,
			new Text,
			new ArrayHandler,
			new MatchTest,
		];
	}

	protected function getNodeEvaluators(): array {
		return [
			new LogicNodeEvaluator,
			new MemberNodeEvaluator,
			new EscapeNodeEvaluator,
			new ConcatenationNodeEvaluator,
			new MathNodeEvaluator,
			new NullNodeEvaluator,
			new MethodNodeEvaluator,
			new BoolNodeEvaluator,
			$this->methodNodeInstance(),
		];
	}
}
