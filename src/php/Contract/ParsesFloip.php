<?php

namespace Viamo\Floip\Contract;

interface ParsesFloip
{
    const MEMBER_TYPE = 'MEMBER';
    const METHOD_TYPE = 'METHOD';
    const MATH_TYPE = 'MATH';
    const LOGIC_TYPE = 'LOGIC';
    const ESCAPE_TYPE = 'ESCAPE';
    const IDENTIFIER = '@';
    
    /**
     * Parse the $input string to produce an abstract syntax tree representing
     * FLOIP expressions found within.
     *
     * @param string $input
     * @return array
     */
    public function parse($input);
}
