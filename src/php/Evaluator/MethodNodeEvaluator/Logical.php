<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator;

use Stringable;
use Viamo\Floip\Evaluator\Exception\MethodNodeException;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Logical as LogicalInterface;
use function array_map;
use function func_get_args;
use function func_num_args;
use function strtoupper;
use function trigger_error;
use const E_USER_ERROR;

/**
 * @method and
 * @method if
 * @method or
 */
class Logical extends AbstractMethodHandler implements LogicalInterface
{
    public function _and(): bool {
        return !in_array(false, array_map([$this, 'value'], func_get_args()));
    }

    public function _if(): mixed {
        $args = array_map([$this, 'value'], func_get_args());
        if (count($args) != 3) {
            throw new MethodNodeException('Wrong number of args for if: ', func_num_args());
        }
        return $args[0] ? $args[1] : $args[2];
    }

    public function _or(): bool {
        return in_array(true, array_map([$this, 'value'], func_get_args()));
    }

    protected function value($thing)
    {
        if ($thing instanceof Stringable) {
            $thing = (string)$thing;
            if (is_string($thing)) {
                switch (strtoupper($thing)) {
                    case 'TRUE':
                        return true;
                    case 'FALSE':
                        return false;
                }
            }
        }
        return match ($thing) {
            'TRUE', 'true' => true,
            'FALSE', 'false' => false,
            default => $thing,
        };
    }

    public function __call($name, array $args)
    {
        switch ($name) {
            case 'and':
            case 'or':
            case 'if':
                return call_user_func_array([$this, "_$name"], $args);
        }
        trigger_error('Call to undefined method ' . static::class . '::' . $name . '()', E_USER_ERROR);
    }

    /**
     * @inheritDoc
     * @return string[]
     */
    public function handles(): array {
        return ['and', 'or', 'if'];
    }
}
