<?php

namespace Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract;

interface TestResult
{
    public function getMatch();

    public function getValue();

    public function __toString();
}
