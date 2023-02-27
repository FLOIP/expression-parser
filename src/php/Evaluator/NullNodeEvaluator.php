<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;

class NullNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context): mixed {
	    return null;
    }
	
	public function handles(): string {
		return ParsesFloip::NULL_TYPE;
	}
}
