<?php

namespace Floip;

use Floip\Contract\ParsesFloip;
use Floip\Contract\EvaluatesExpression;
use Floip\Evaluator\Node;

class Evaluator
{
    /** @var ParsesFloip */
    protected $parser;
    /** @var array */
    protected $evaluators = [];

    public function __construct(ParsesFloip $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Evaluate a FLOIP expression
     *
     * @param string $expression Expression to evaluate
     * @param array $context The expression context
     * @return string
     */
    public function evaluate($expression, array $context)
    {
        $ast = $this->parser->parse($expression);

        $nodes = [];

        // transform the AST array into objects
        foreach ($ast as $item) {
            if (Node::isNode($item)) {
                $nodes[] = new Node($item);
            }
        }

        // we want to evaluate the nodes from the deepest child first
        // since some nodes will have others as arguments
        $it = $this->getIterator($nodes);
        foreach ($it as $node) {
            $value = $this->evalNode($expression, $node, $context);
            $node->setValue($value);
        }

        $offset = 0;

        foreach ($nodes as $node) {
            // every time we modify the expression, we change the length
            // we should keep track of this offset
            $oldLength = strlen($expression);
            $expression = $this->insert($node, $expression, $node->getValue(), $offset);
            $newLength = strlen($expression);
            if ($oldLength < $newLength) {
                $offset -= $newLength - $oldLength;
            }
            if ($oldLength > $newLength) {
                $offset += $newLength - $oldLength;
            }
        }

        return $expression;
    }


    /**
     * @param EvaluatesExpression $evaluator
     * @param string $type The type of node that is handled
     * @see Floip\Contact\ParsesFloip for node types
     * @return Evaluator
     */
    public function addNodeEvaluator(EvaluatesExpression $evaluator, $type)
    {
        $this->evaluators[$type] = $evaluator;
        return $this;
    }

    /**
     * @param string $type
     * @return EvaluatesExpression
     */
    public function getNodeEvaluator($type)
    {
        if (isset($this->evaluators[$type])) {
            return $this->evaluators[$type];
        }
        throw new \Exception;
    }

    private function evalNode($expression, Node $node, array $context)
    {
        return $this->getNodeEvaluator($node['type'])
            ->evaluate($expression, $node, $context);
        throw new \Exception;
    }

    private function getIterator(array $ast)
    {
        // we want to recurse over the tree depth-first, starting with the 
        // deepest nodes
        $arrayIterator = new \RecursiveArrayIterator($ast);
        return new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::CHILD_FIRST);
    }

    protected function insert(Node $node, $string, $insert, $offset)
    {
        $location = $node['location'];
        $begin = \substr($string, 0, $location['start']['offset'] + $offset);
        $end = substr($string, $location['end']['offset'] + $offset);
        return $begin . $insert . $end;
    }
}
