<?php

namespace Floip;

class ParseTree
{
    private $data = [];

    private function __construct($json)
    {
        $this->data = json_decode($json);
    }
}
