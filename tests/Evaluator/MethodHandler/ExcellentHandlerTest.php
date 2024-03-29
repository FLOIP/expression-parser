<?php

namespace Viamo\Floip\Tests\Evaluator\MethodHandler;

use Viamo\Floip\Tests\TestCase;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Excellent;
use Viamo\Floip\Evaluator\MethodNodeEvaluator\Contract\Excellent as ExcellentContract;

class ExcellentHandlerTest extends TestCase
{
    /** @var ExcellentContract */
    private ExcellentContract|Excellent $excellent;

    public function setUp(): void
    {
        $this->excellent = new Excellent;
        parent::setUp();
    }

    /**
     * @dataProvider wordProvider
     */
    public function testWord($string, $number, $bySpaces, $expected): void {
        $this->assertEquals($expected, $this->excellent->word($string, $number, $bySpaces));
    }

        /**
     * @dataProvider firstWordProvider
     */
    public function testFirstWord($input, $expected): void {
        $this->assertEquals($expected, $this->excellent->firstWord($input));
    }

    /**
     * @dataProvider wordCountProvider
     */
    public function testWordCount($input, $expected): void {
        $this->assertEquals($expected, $this->excellent->wordCount($input));
    }

    /**
     * @dataProvider wordSliceProvider
     */
    public function testWordSlice($string, $start, $stop, $bySpaces, $expected): void {
        $this->assertEquals($expected, $this->excellent->wordSlice($string, $start, $stop, $bySpaces));
    }

    /**
     * @dataProvider isNumberProvider
     */
    public function testIsNumber($value, $expected): void {
        $this->assertEquals($expected, $this->excellent->isNumber($value));
        
        $node = new Node([]);
        $node->setValue($value);
        
        $this->assertEquals($expected, $this->excellent->isNumber($value));
    }

    /**
     * @dataProvider isStringProvider
     */
    public function testIsString($value, $expected): void {
        $this->assertEquals($expected, $this->excellent->isString($value));
        
        $node = new Node([]);
        $node->setValue($value);
        
        $this->assertEquals($expected, $this->excellent->isString($value));
    }

    /**
     * @dataProvider isBoolProvider
     */
    public function testIsBool($value, $expected): void {
        $this->assertEquals($expected, $this->excellent->isBool($value));
        
        $node = new Node([]);
        $node->setValue($value);
        
        $this->assertEquals($expected, $this->excellent->isBool($value));
    }
    
    public function wordCountProvider(): array {
        return [
            ['I am a little teapot.', 5],
            ['I.am.a.little.teapot', 5],
            ['I am. A. Little.Teapot', 5],
            ['I.Am,A!Little;Teapot:', 5],
        ];
    }
    
    public function firstWordProvider(): array {
        return [
            ['Foo Bar', 'Foo'],
            ['Foo,Bar', 'Foo'],
            ['Foo. Bar', 'Foo'],
            ['Foo...bar!', 'Foo'],
        ];
    }
    
    public function wordProvider(): array {
        return [
            ['hello cow-boy', 2, false, 'cow'],
            ['hello cow-boy', 2, true, 'cow-boy'],
            ['hello cow-boy', -1, false, 'boy'],
        ];
    }
    
    public function wordSliceProvider(): array {
        return [
            ['RapidPro expressions are fun', 2, 4, null, 'expressions are'],
            ['RapidPro expressions are fun', 2, null, null, 'expressions are fun'],
            ['RapidPro expressions are fun', 1, -2, null, 'RapidPro expressions'],
            ['RapidPro expressions are fun', -1, 2, null, 'fun'],
        ];
    }
    
    public function isNumberProvider(): array {
        return [
            [0, true],
            [-1, true],
            [1, true],
            ['0', true],
            ['-100', true],
            ['100', true],
            [true, false],
            ['string', false],
            ['TRUE', false],
        ];
    }
    
    public function isStringProvider(): array {
        return [
            ['yes', true],
            ['', true],
            [0, false],
            [1, false],
            [-1, false],
            ['0', false],
            ['1', false],
            ['-1', false],
            [true, false],
        ];
    }
    
    public function isBoolProvider(): array {
        $n1 = new Node([]);
        $n1->setValue(true);
        
        $n2 = new Node([]);
        $n2->setValue(false);
        
        $n3 = new Node([]);
        $n3->setValue('foo');
        
        $n4 = new Node([]);
        $n4->setValue(4);

        return [
            ['TRUE', true],
            ['FALSE', true],
            [$n1, true],
            [$n2, true],
            [$n3, false],
            [$n4, false],
            ['true', false],
            ['false', false],
            ['yes', false],
            ['', false],
            [0, false],
            [1, false],
            [-1, false]
        ];
    }
}
