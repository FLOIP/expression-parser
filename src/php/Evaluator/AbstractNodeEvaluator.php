<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\EvaluatesExpression;

abstract class AbstractNodeEvaluator implements EvaluatesExpression
{
    abstract public function evaluate(Node $node, $context): mixed;
}
