<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface ValueObject
{
    public function __toString();
    public function getValue();
}
