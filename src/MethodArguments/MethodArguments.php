<?php

declare(strict_types=1);

namespace webignition\BasilCompilableSource\MethodArguments;

use webignition\BasilCompilableSource\Expression\ExpressionInterface;
use webignition\BasilCompilableSource\HasMetadataTrait;
use webignition\BasilCompilableSource\Metadata\Metadata;
use webignition\BasilCompilableSource\Metadata\MetadataInterface;
use webignition\BasilCompilableSource\RenderTrait;
use webignition\StubbleResolvable\ResolvableCollection;
use webignition\StubbleResolvable\ResolvableInterface;
use webignition\StubbleResolvable\ResolvedTemplateMutatorResolvable;

class MethodArguments implements MethodArgumentsInterface
{
    use HasMetadataTrait;
    use RenderTrait;

    public const FORMAT_INLINE = 'inline';
    public const FORMAT_STACKED = 'stacked';

    private const INDENT = '    ';

    private string $format;

    /**
     * @var ExpressionInterface[]
     */
    private array $arguments;

    /**
     * @param ExpressionInterface[] $arguments
     * @param string $format
     */
    public function __construct(array $arguments = [], string $format = self::FORMAT_INLINE)
    {
        $arguments = array_filter($arguments, function ($item) {
            return $item instanceof ExpressionInterface;
        });

        $this->metadata = new Metadata();
        foreach ($arguments as $expression) {
            $this->metadata = $this->metadata->merge($expression->getMetadata());
        }

        $this->arguments = $arguments;
        $this->format = $format;
    }

    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function getResolvable(): ResolvableInterface
    {
        $resolvableArguments = [];

        foreach ($this->arguments as $argument) {
            $resolvableArguments[] = new ResolvedTemplateMutatorResolvable(
                $argument,
                function (string $resolvedTemplate) {
                    return $this->argumentResolvedTemplateMutator($resolvedTemplate);
                }
            );
        }

        return new ResolvedTemplateMutatorResolvable(
            ResolvableCollection::create($resolvableArguments),
            function (string $resolvedTemplate) {
                return $this->resolvedTemplateMutator($resolvedTemplate);
            }
        );
    }

    private function resolvedTemplateMutator(string $resolvedTemplate): string
    {
        if ('' === $resolvedTemplate) {
            return $resolvedTemplate;
        }

        return self::FORMAT_STACKED === $this->format
            ? $this->stackedResolvedTemplateMutator($resolvedTemplate)
            : rtrim($resolvedTemplate, ', ');
    }

    private function stackedResolvedTemplateMutator(string $resolvedTemplate): string
    {
        $resolvedTemplate = rtrim($resolvedTemplate, ",\n");

        $lines = explode("\n", $resolvedTemplate);
        array_walk($lines, function (string &$line) {
            if ('' !== $line) {
                $line = self::INDENT . $line;
            }
        });

        return "\n" . implode("\n", $lines) . "\n";
    }

    private function argumentResolvedTemplateMutator(string $resolvedTemplate): string
    {
        if ('' === $resolvedTemplate) {
            return $resolvedTemplate;
        }

        return self::FORMAT_STACKED === $this->format
            ? $resolvedTemplate . ',' . "\n"
            : $resolvedTemplate . ', ';
    }
}
