<?php

namespace Viamo\Floip\Evaluator;

use ArrayAccess;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Util\Arr;

class MemberNodeEvaluator extends AbstractNodeEvaluator
{
    /**
     * Evaluate the value of a member access node given a context.
     */
    public function evaluate(Node $node, $context)
    {
        if (!isset($node['key'])) {
            throw new NodeEvaluatorException('Member node is the wrong shape, should have "key"');
        }

        $key = $node['key'];

        $keys = explode('.', $key);
        $currentContext = $context;

        // traverse the context tree until we run out of keys
        foreach ($keys as $currentKey) {
            if (Arr::exists($currentContext, $currentKey)) {
                $currentContext = $currentContext[$currentKey];
            } else {
                // if our current key doesn't exist, we return the compound key
                return $key;
            }
        }

        // at this point, we have a value associated with our key
        // if it is a nested context, return its default value or JSON
        if (Arr::isArray($currentContext)) {
            if ((is_array($currentContext) && Arr::isAssoc($currentContext)) || $currentContext instanceof ArrayAccess) {
                if (Arr::exists($currentContext, '__value__')) {
                    return $currentContext['__value__'];
                }
                return \json_encode($currentContext, \JSON_FORCE_OBJECT);
            }
        }
        return $currentContext;
    }

    public function handles()
    {
        return ParsesFloip::MEMBER_TYPE;
    }
}
