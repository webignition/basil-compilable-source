<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Expression;

use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableProviderInterface;
use webignition\StubbleResolvable\ResolvableWithoutContext;

class CompositeExpression extends AbstractExpression implements ResolvableProviderInterface
{
    use RenderTrait;

    /**
     * @var ExpressionInterface[]
     */
    private $expressions;

    /**
     * @param array<mixed> $expressions
     */
    public function __construct(array $expressions)
    {
        $this->expressions = array_filter($expressions, function ($item) {
            return $item instanceof ExpressionInterface;
        });

        $metadata = new Metadata();
        foreach ($this->expressions as $expression) {
            $metadata = $metadata->merge($expression->getMetadata());
        }

        parent::__construct($metadata);
    }

    public function getResolvable(): ResolvableInterface
    {
        $resolvables = [];
        foreach ($this->expressions as $expression) {
            $expressionResolvable = $this->getExpressionResolvable($expression);

            if ($expressionResolvable instanceof ResolvableInterface) {
                $resolvables[] = $expressionResolvable;
            }
        }

        return ResolvableCollection::create($resolvables);
    }

    private function getExpressionResolvable(ExpressionInterface $expression): ?ResolvableInterface
    {
        if ($expression instanceof ResolvableInterface) {
            return $expression;
        }

        if ((is_object($expression) && method_exists($expression, '__toString'))) {
            return new ResolvableWithoutContext((string) $expression);
        }

        if ($expression instanceof ResolvableProviderInterface) {
            return $expression->getResolvable();
        }

        return null;
    }
}
