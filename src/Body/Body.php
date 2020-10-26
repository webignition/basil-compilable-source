<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\Body;

use webignition\BasilCompilableSource\DeferredResolvableCreationTrait;
use webignition\BasilCompilableSource\Expression\AssignmentExpression;
use webignition\BasilCompilableSource\Expression\ClosureExpression;
use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\HasMetadataInterface;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\BasilCompilableSource\Statement\Statement;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class Body implements BodyInterface, ResolvedTemplateMutationInterface
{
    use DeferredResolvableCreationTrait;
    use RenderTrait;

    /**
     * @var BodyContentInterface[]
     */
    private array $content;

    private MetadataInterface $metadata;

    /**
     * @param BodyContentInterface[] $content
     */
    public function __construct(array $content)
    {
        $this->content = $this->filterContent($content);
        $this->metadata = $this->buildMetadata();
    }

    public static function createEnclosingBody(BodyInterface $body): self
    {
        return new Body([
            new Statement(
                new ClosureExpression($body)
            ),
        ]);
    }

    /**
     * @param array<mixed> $expressions
     *
     * @return self
     *
     * @throws \InvalidArgumentException
     */
    public static function createFromExpressions(array $expressions): self
    {
        $statements = [];

        foreach ($expressions as $index => $expression) {
            if ($expression instanceof ExpressionInterface) {
                $statements[] = new Statement($expression);
            } else {
                throw new \InvalidArgumentException('Non-expression at index ' . (string) $index);
            }
        }

        return new Body($statements);
    }

    public static function createForSingleAssignmentStatement(
        ExpressionInterface $variable,
        ExpressionInterface $value
    ): self {
        return new Body([
            new Statement(
                new AssignmentExpression($variable, $value)
            )
        ]);
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getResolvedTemplateMutator(): callable
    {
        return function (string $resolvedTemplate): string {
            return rtrim($resolvedTemplate);
        };
    }

    protected function createResolvable(): ResolvableInterface
    {
        $resolvables = [];

        foreach ($this->content as $item) {
            $resolvables[] = new ResolvedTemplateMutatorResolvable(
                $item,
                function (string $resolvedTemplate): string {
                    return $this->resolvedItemTemplateMutator($resolvedTemplate);
                }
            );
        }

        return ResolvableCollection::create($resolvables);
    }

    private function resolvedItemTemplateMutator(string $resolvedTemplate): string
    {
        return rtrim($resolvedTemplate) . "\n";
    }

    /**
     * @param BodyContentInterface[] $content
     *
     * @return BodyContentInterface[]
     */
    private function filterContent(array $content): array
    {
        $filteredContent = [];

        foreach ($content as $item) {
            if ($this->includeContent($item)) {
                $filteredContent[] = clone $item;
            }
        }

        return $filteredContent;
    }

    /**
     * @param mixed $item
     *
     * @return bool
     */
    private function includeContent($item): bool
    {
        if (!$item instanceof BodyContentInterface) {
            return false;
        }

        if ($item instanceof self && 0 === count($item->content)) {
            return false;
        }

        return true;
    }

    private function buildMetadata(): MetadataInterface
    {
        $metadata = new Metadata();

        foreach ($this->content as $item) {
            if ($item instanceof HasMetadataInterface) {
                $metadata = $metadata->merge($item->getMetadata());
            }
        }

        return $metadata;
    }
}
