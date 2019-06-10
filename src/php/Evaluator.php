<?php

namespace Floip;

use Floip\Evaluator\Context;

class Evaluator
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    
}
