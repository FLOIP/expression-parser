<?php

namespace Floip\Evaluator;

class MemberEvaluator extends AbstractNodeEvaluator
{
    /**
     * Evaluate the value of a member access node given a context.
     *
     * @param string $string
     * @param Node $node
     * @param array $context
     * @return mixed
     */
    public function evaluate($string, Node $node, array $context)
    {
        if (!isset($node['key'], $context[$node['key']])) {
            throw new \Exception;
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
}
