<?php

namespace Floip\Contract;

use Floip\Evaluator\Node;

interface EvaluatesExpression
{
    public function evaluate($string, Node $node, array $context);
}
