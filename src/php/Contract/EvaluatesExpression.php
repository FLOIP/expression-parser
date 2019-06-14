<?php

namespace Floip\Contract;

use Floip\Evaluator\Node;

interface EvaluatesExpression
{
    public function evaluate(Node $node, array $context);
}
