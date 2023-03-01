<?php

namespace Viamo\Floip\Tests\Evaluator\MethodNodeEvaluator;

use Viamo\Floip\Evaluator\MethodNodeEvaluator\Text;
use Viamo\Floip\Tests\TestCase;

class TextTest extends TestCase {
    
    public function setUp(): void {
        $this->textMethodHandler = new Text();
        
        parent::setUp();
    }
    
    public function testCanAddHandler() {
        $result = $this->textMethodHandler->substitute(
            string: 'abcd.efgh.ijkl',
            old: '.',
            new: '',
            replaceAtIndex: 1
        );
        
        $this->assertEquals('abcdefgh.ijkl', $result);
    }
}
