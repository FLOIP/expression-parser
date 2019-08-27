<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;

class NullNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, array $context)
    {
        return null;
    }

    public function handles()
    {
        return ParsesFloip::NULL_TYPE;
    }
}
