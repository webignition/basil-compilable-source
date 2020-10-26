<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\DocBlock;

use webignition\BasilCompilableSource\Annotation\AnnotationInterface;
use webignition\BasilCompilableSource\DeferredResolvableCreationTrait;
use webignition\BasilCompilableSource\SourceInterface;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvableWithoutContext;
use webignition\StubbleResolvable\ResolvedTemplateMutationInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class DocBlock implements ResolvableInterface, SourceInterface, ResolvedTemplateMutationInterface
{
    use DeferredResolvableCreationTrait;

    private const RENDER_TEMPLATE_EMPTY = <<<'EOD'
/**
 */
EOD;

    private const RENDER_TEMPLATE = <<<'EOD'
/**
%s
 */
EOD;

    /**
     * @var array<int, string|AnnotationInterface>
     */
    private array $lines;

    /**
     * @param array<int, string|AnnotationInterface> $lines
     */
    public function __construct(array $lines)
    {
        $this->lines = $lines;
    }

    public function append(DocBlock $addition): self
    {
        return $this->merge($this, $addition);
    }

    public function prepend(DocBlock $addition): self
    {
        return $this->merge($addition, $this);
    }

    public function getResolvedTemplateMutator(): callable
    {
        return function (string $resolvedTemplate): string {
            if ('' === $resolvedTemplate) {
                return self::RENDER_TEMPLATE_EMPTY;
            }

            return sprintf(self::RENDER_TEMPLATE, rtrim($resolvedTemplate));
        };
    }

    private function merge(DocBlock $source, DocBlock $addition): self
    {
        return new DocBlock(array_merge($source->lines, $addition->lines));
    }

    protected function createResolvable(): ResolvableInterface
    {
        $resolvableItems = [];

        foreach ($this->lines as $line) {
            if (is_string($line)) {
                $resolvableItems[] = new ResolvableWithoutContext($line);
            } else {
                $resolvableItems[] = $line;
            }
        }

        array_walk($resolvableItems, function (&$resolvable) {
            $resolvable = new ResolvedTemplateMutatorResolvable(
                $resolvable,
                function (string $resolvedLine): string {
                    return $this->resolvedLineTemplateMutator($resolvedLine);
                }
            );
        });

        return ResolvableCollection::create($resolvableItems);
    }

    private function resolvedLineTemplateMutator(string $resolvedLine): string
    {
        if ('' === trim($resolvedLine)) {
            $resolvedLine = ' *';
        } else {
            $resolvedLine = ' * ' . $resolvedLine;
        }

        return $resolvedLine . "\n";
    }
}
