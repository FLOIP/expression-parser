<?php

namespace Viamo\Floip;

use ArrayAccess;
use RecursiveIteratorIterator;
use Viamo\Floip\Contract\EvaluatesExpression;
use Viamo\Floip\Contract\ParsesFloip;
use Viamo\Floip\Evaluator\Exception\EvaluatorException;
use Viamo\Floip\Evaluator\Node;
use Viamo\Floip\Evaluator\RecursiveNodeIterator;
use function is_array;

class Evaluator
{
    protected array $evaluators = [];

    public function __construct(
        protected ParsesFloip $parser
    ) {
    }

    private function mapNodes($item)
    {
        if (Node::isNode($item)) {
            return new Node($item);
        }
        return $item;
    }

    /**
     * Evaluate a FLOIP expression.
     * Each expression object in the AST produced by the parser
     * will be transformed into a Node and evaluated with an assigned
     * NodeEvaluator. These NodeEvaluators are added to this Evaluator object
     * via addNodeEvaluator.
     *
     * @param string $expression Expression to evaluate
     * @param array|ArrayAccess $context The expression context
     *
     * @throws EvaluatorException
     * @see Evaluator::addNodeEvaluator
     */
    public function evaluate(string $expression, array|ArrayAccess $context): string {
        // check that our context is array accessable
        $this->validateContextOrThrow($context);

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
     * @throws EvaluatorException
     */
    private function validateContextOrThrow(mixed $context): void {
        if (is_array($context)) {
            return;
        }
        if ($context instanceof ArrayAccess) {
            return;
        }
        throw new EvaluatorException('Context must be array or implement ArrayAccess');
    }

    /**
     * @see ParsesFloip for node types
     */
    public function addNodeEvaluator(EvaluatesExpression $evaluator): Evaluator {
        $type = $evaluator->handles();
        $this->evaluators[$type] = $evaluator;
        return $this;
    }

    public function getNodeEvaluator(string $type): EvaluatesExpression {
        if (isset($this->evaluators[$type])) {
            return $this->evaluators[$type];
        }
        throw new EvaluatorException("Unknown node type: $type");
    }

    private function evalNode(Node $node, array|ArrayAccess $expressionContext): mixed {
        return $this->getNodeEvaluator($node['type'])
            ->evaluate($node, $expressionContext);
    }

    private function getIterator(array $ast): RecursiveIteratorIterator {
        // we want to recurse over the tree depth-first, starting with the
        // deepest nodes
        $arrayIterator = new RecursiveNodeIterator($ast);
        return new RecursiveIteratorIterator($arrayIterator, RecursiveIteratorIterator::CHILD_FIRST);
    }
}
