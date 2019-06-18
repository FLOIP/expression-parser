<?php

namespace Viamo\Floip\Evaluator;

use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Contract\ParsesFloip;

class MemberNodeEvaluator extends AbstractNodeEvaluator
{
    /**
     * Evaluate the value of a member access node given a context.
     *
     * @param Node $node
     * @param array $context
     * @return mixed
     */
    public function evaluate(Node $node, array $context)
    {
        if (!isset($node['key'])) {
            throw new NodeEvaluatorException('Member node is the wrong shape, should have "key"');
        }
        if (!key_exists($node['key'], $context)) {
            if (key_exists('value', $node)) {
                return $node['key'] . '.' . $node['value'];
            }
            return $node['key'];
        }
        $el = $context[$node['key']];
        if (!isset($node['value'])) {
            // return the __value__ element of the context, or else the whole
            // context serialized
            if (\key_exists('__value__', $el)) {
                return $el['__value__'];
            }
            return \json_encode($el);
        }
        return $el[$node['value']];
    }

    public function handles()
    {
        return ParsesFloip::MEMBER_TYPE;
    }
}
