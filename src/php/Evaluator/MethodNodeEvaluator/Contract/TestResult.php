<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;


interface TestResult extends ValueObject, Chainable
{
    public function getMatch();

    public function getValue();

    public function __toString();
}
