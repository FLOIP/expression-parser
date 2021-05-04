<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;

class BoolNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context) {
        if (!isset($node['value'])) {
            throw new NodeEvaluatorException('Bool node is the wrong shape, should have "value"');
        }
        switch (strtolower($node['value'])) {
            case 'true':
                return true;
            case 'false':
                return false;
        }
        throw new NodeEvaluatorException("Unknown value in Bool node: {$node['value']}");
    }

    public function handles() {
        return ParsesFloip::BOOL_TYPE;
    }
}
