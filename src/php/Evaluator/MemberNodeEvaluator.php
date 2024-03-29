<?php

namespace Viamo\Floip\Evaluator;

use ArrayAccess;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\NodeEvaluatorException;
use Viamo\Floip\Evaluator\MemberNodeEvaluator\MemberObject;
use Viamo\Floip\Util\Arr;

class MemberNodeEvaluator extends AbstractNodeEvaluator
{
    /**
     * Evaluate the value of a member access node given a context.
     */
    public function evaluate(Node $node, $context): mixed {
        if (!isset($node['key'])) {
            throw new NodeEvaluatorException('Member node is the wrong shape, should have "key"');
        }

        $key = $node['key'];

        $keys = explode('.', (string) $key);
        $currentContext = $context;

        // if the top-level key does not exist at the top-level
        // of the context, return the expression as originally
        // entered (prefixed with @)
        if (!Arr::exists($currentContext, reset($keys))) {
            return "@{$key}";
        }

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
        // if it is a nested context, return its default value or a
        // representation that, when coerced into a string, becomes JSON
        if (Arr::isArray($currentContext)) {
            if ((is_array($currentContext) && Arr::isAssoc($currentContext)) || $currentContext instanceof ArrayAccess) {
                if (Arr::exists($currentContext, '__value__')) {
                    return $currentContext['__value__'];
                }
                return new MemberObject($currentContext);
            }
        }
        return $currentContext;
    }

    public function handles(): string {
        return ParsesFloip::MEMBER_TYPE;
    }
}
