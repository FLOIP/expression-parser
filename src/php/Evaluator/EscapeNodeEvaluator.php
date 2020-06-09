<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;

class EscapeNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context)
    {
        return ParsesFloip::IDENTIFIER;
    }

    public function handles()
    {
        return ParsesFloip::ESCAPE_TYPE;
    }
}
