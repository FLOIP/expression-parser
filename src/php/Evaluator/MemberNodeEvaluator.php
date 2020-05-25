<?php

namespace Viamo\Floip\Evaluator;

use ArrayAccess;
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

        $key = $node['key'];

        $keys = explode('.', $key);
        $currentContext = $context;

        // traverse the context tree until we run out of keys
        foreach ($keys as $currentKey) {
            if (key_exists($currentKey, $currentContext)) {
                $currentContext = $currentContext[$currentKey];
            } else {
                // if our current key doesn't exist, we return the compound key
                return $key;
            }
        }

        // at this point, we have a value associated with our key
		// if it is a nested context, return its default value or JSON
        if ($this->isArray($currentContext) && $this->isAssociative($currentContext)) {
            if (\key_exists('__value__', $currentContext)) {
                return $currentContext['__value__'];
            }
            return \json_encode($currentContext, \JSON_FORCE_OBJECT);
        }
        return $currentContext;
    }

    private function isArray($thing) {
        return $thing instanceof ArrayAccess || is_array($thing);
    }

    public function handles()
    {
        return ParsesFloip::MEMBER_TYPE;
    }

    private function isAssociative(array $arr) {
        foreach ($arr as $key => $value) {
            if (is_string($key)) {
                return true;
            }
        }
        return false;
    }
}
