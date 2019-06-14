<?php

namespace Floip\Evaluator;

use Floip\Contract\EvaluatesExpression;

abstract class AbstractNodeEvaluator implements EvaluatesExpression
{
    abstract public function evaluate(Node $node, array $context);
}
