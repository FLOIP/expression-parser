<?php

namespace Viamo\Floip;

use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Contract\EvaluatesExpression;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\RecursiveNodeIterator;
use Viamo\Floip\Evaluator\Exception\EvaluatorException;

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

    private function mapNodes($item)
    {
        if (Node::isNode($item)) {
            return new Node($item);
        }
        return $item;
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

        $nodes = array_map([$this, 'mapNodes'], $ast);

        // we want to evaluate the nodes from the deepest child first
        // since some nodes will have others as arguments
        $it = $this->getIterator($nodes);
        foreach ($it as $node) {
            if ($node instanceof Node) {
                $value = $this->evalNode($node, $context);
                $node->setValue($value);
            }
        }

        return implode('', $nodes);
    }

    /**
     * @param EvaluatesExpression $evaluator
     * @see Floip\Contact\ParsesFloip for node types
     * @return Evaluator
     */
    public function addNodeEvaluator(EvaluatesExpression $evaluator)
    {
        $type = $evaluator->handles();
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
        throw new EvaluatorException("Unknown node type: $type");
    }

    private function evalNode(Node $node, array $context)
    {
        return $this->getNodeEvaluator($node['type'])
            ->evaluate($node, $context);
    }

    private function getIterator(array $ast)
    {
        // we want to recurse over the tree depth-first, starting with the 
        // deepest nodes
        $arrayIterator = new RecursiveNodeIterator($ast);
        return new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::CHILD_FIRST);
    }
}
