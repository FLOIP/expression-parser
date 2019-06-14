<?php

namespace Floip\Evaluator;

class MemberEvaluator extends AbstractNodeEvaluator
{
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
