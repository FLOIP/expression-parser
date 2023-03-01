<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;

class BoolNodeEvaluator extends AbstractNodeEvaluator
{
    public function evaluate(Node $node, $context): mixed {
        if (!isset($node['value'])) {
            throw new NodeEvaluatorException('Bool node is the wrong shape, should have "value"');
        }
        return match (strtolower((string) $node['value'])) {
            'true' => true,
            'false' => false,
            default => throw new NodeEvaluatorException("Unknown value in Bool node: {$node['value']}"),
        };
    }

    public function handles(): string {
        return ParsesFloip::BOOL_TYPE;
    }
}
